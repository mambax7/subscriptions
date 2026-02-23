<?php
/**
 * Subscriptions - Cron job
 *
 * Run this file periodically (e.g. every hour) via a cron entry:
 *   php /path/to/xoops/modules/subscriptions/cron.php
 *
 * Or schedule via a system cron calling:
 *   wget -q -O /dev/null "https://yoursite.com/modules/subscriptions/cron.php?token=SECRET"
 *
 * Tasks performed:
 *  1. Finalise subscriptions scheduled to cancel at period end.
 *  2. Fire EVENT_TRIAL_ENDING for trials ending within 24 hours.
 *  3. Mark expired active/trial subscriptions as 'expired'.
 */

use XoopsModules\Subscriptions\{
    AccessControl,
    Helper,
    Subscription,
    Utility,
    Webhook
};

// Bootstrap XOOPS — mainfile.php defines XOOPS_ROOT_PATH itself,
// so we must NOT define it beforehand; just resolve the path for the require.
$xoopsRootPath = dirname(dirname(dirname(__FILE__)));
require_once $xoopsRootPath . '/mainfile.php';

require_once XOOPS_ROOT_PATH . '/modules/subscriptions/preloads/autoloader.php';
require_once XOOPS_ROOT_PATH . '/modules/subscriptions/include/functions.php';
require_once XOOPS_ROOT_PATH . '/modules/subscriptions/language/english/main.php';

// HTTP invocation: require a secret token to prevent abuse.
if (PHP_SAPI !== 'cli') {
    $cronSecret = Utility::config('cron_secret', '');
    $token      = $_GET['token'] ?? '';
    if (empty($cronSecret) || !hash_equals($cronSecret, $token)) {
        http_response_code(403);
        exit('Forbidden');
    }
}

$helper              = Helper::getInstance();
$subscriptionHandler = $helper->getHandler('Subscription');
$whHandler           = $helper->getHandler('Webhook');
$accessControl       = new AccessControl();

$log = [];

// -----------------------------------------------------------------------
// Task 1: Finalise period-end cancellations
// -----------------------------------------------------------------------
$pendingCancels = $subscriptionHandler->getPendingCancellations();
foreach ($pendingCancels as $sub) {
    $userId = (int)$sub->getVar('user_id');
    $subId  = (int)$sub->getVar('sub_id');

    // Cancel immediately now that the period has ended
    $sub->setVar('status',               Subscription::STATUS_CANCELLED);
    $sub->setVar('cancelled_at',         time());
    $sub->setVar('cancel_at_period_end', 0);
    $sub->setVar('auto_renew',           0);
    $sub->setVar('updated_at',           time());
    $subscriptionHandler->insert($sub);

    // Remove from XOOPS groups
    $accessControl->syncXoopsGroups($userId, false);

    // Fire webhook
    $whHandler->fire(Webhook::EVENT_SUBSCRIPTION_CANCELLED, [
        'sub_id'     => $subId,
        'user_id'    => $userId,
        'reason'     => $sub->getVar('cancel_reason', 'n'),
        'trigger'    => 'period_end',
    ]);

    $log[] = "Cancelled sub #{$subId} for user #{$userId} (period-end)";
}

// -----------------------------------------------------------------------
// Task 2: Fire trial-ending reminders (trials ending in next 24 h)
// -----------------------------------------------------------------------
$endingSoon = $subscriptionHandler->getTrialsEndingSoon(24);
foreach ($endingSoon as $sub) {
    $userId = (int)$sub->getVar('user_id');
    $subId  = (int)$sub->getVar('sub_id');

    $whHandler->fire(Webhook::EVENT_TRIAL_ENDING, [
        'sub_id'        => $subId,
        'user_id'       => $userId,
        'trial_ends_at' => (int)$sub->getVar('trial_ends_at'),
    ]);

    // Also send an email notification to the user
    subscriptions_send_trial_ending_email($sub);

    $log[] = "Fired trial.ending for sub #{$subId} (user #{$userId})";
}

// -----------------------------------------------------------------------
// Task 3: Mark truly expired subscriptions (period has ended, not scheduled cancel)
// -----------------------------------------------------------------------
$expiredSubs = $subscriptionHandler->getExpiredActive();
foreach ($expiredSubs as $sub) {
    $userId = (int)$sub->getVar('user_id');
    $subId  = (int)$sub->getVar('sub_id');

    $sub->setVar('status',     Subscription::STATUS_EXPIRED);
    $sub->setVar('updated_at', time());
    $subscriptionHandler->insert($sub);

    // Remove from XOOPS groups
    $accessControl->syncXoopsGroups($userId, false);

    $whHandler->fire(Webhook::EVENT_SUBSCRIPTION_EXPIRED, [
        'sub_id'  => $subId,
        'user_id' => $userId,
    ]);

    $log[] = "Expired sub #{$subId} for user #{$userId}";
}

// -----------------------------------------------------------------------
// Output
// -----------------------------------------------------------------------
if (PHP_SAPI === 'cli') {
    echo implode("\n", $log) . "\n";
    echo 'Done. ' . count($log) . " actions.\n";
} else {
    http_response_code(200);
    header('Content-Type: text/plain');
    echo implode("\n", $log) . "\n";
    echo 'Done. ' . count($log) . " actions.\n";
}

// -----------------------------------------------------------------------
// Helper: trial-ending email
// -----------------------------------------------------------------------
function subscriptions_send_trial_ending_email(Subscription $sub): void
{
    $userHandler = xoops_getHandler('user');
    $user        = $userHandler->get((int)$sub->getVar('user_id', 'n'));
    if (!$user) {
        return;
    }
    $plan       = $sub->getPlan();
    $planName   = $plan ? $plan->getVar('name', 'n') : '';
    $trialEnds  = Utility::formatDate((int)$sub->getVar('trial_ends_at'));
    $plansUrl   = XOOPS_URL . '/modules/subscriptions/plans.php';

    $mailer = xoops_getMailer();
    $mailer->setToUsers([$user]);
    $mailer->setFromName($GLOBALS['xoopsConfig']['sitename'] ?? '');
    $mailer->setSubject(_MD_SUBSCRIPTIONS_EMAIL_TRIAL_ENDING_SUBJECT);
    $mailer->setBody(sprintf(
        _MD_SUBSCRIPTIONS_EMAIL_TRIAL_ENDING_BODY,
        $user->getVar('uname', 'n'),
        $planName,
        $trialEnds,
        $plansUrl
    ));
    $mailer->send();
}
