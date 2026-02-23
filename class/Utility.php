<?php

declare(strict_types=1);

namespace XoopsModules\Subscriptions;

use Criteria;
use CriteriaCompo;
use Throwable;
use XoopsObject;

use function extension_loaded;
use function function_exists;
use function in_array;
use function is_array;
use function is_object;

use const ENT_QUOTES;
use const PHP_VERSION;
use const XOOPS_VERSION;

/**
 * Utility — static helper methods for the Subscriptions module.
 *
 * Extends SysUtility for any framework-level helpers it provides.
 * Add module-specific helpers here as static methods.
 *
 * Usage (in any file):
 *   use XoopsModules\Subscriptions\Utility;
 *   Utility::formatMoney(9.99);
 *
 * @copyright XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 */
class Utility extends Common\SysUtility
{
    // -------------------------------------------------------------------------
    // Currency
    // -------------------------------------------------------------------------

    /**
     * Format a money amount using the configured (or supplied) currency symbol.
     */
    public static function formatMoney(float $amount, string $currency = ''): string
    {
        if ($currency === '') {
            $helper = Helper::getInstance();
            $currency = $helper->getConfig('currency') ?? 'USD';
        }

        return self::currencySymbol($currency) . number_format($amount, 2);
    }

    /**
     * Return the display symbol for an ISO 4217 currency code.
     */
    public static function currencySymbol(string $code): string
    {
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'CAD' => 'CA$',
            'AUD' => 'A$',
            'JPY' => '¥',
            'CHF' => 'Fr',
            'INR' => '₹',
            'BRL' => 'R$',
            'MXN' => 'Mex$',
        ];

        return $symbols[$code] ?? $code . ' ';
    }

    // -------------------------------------------------------------------------
    // Access / auth
    // -------------------------------------------------------------------------

    /**
     * Return true if the current user has an active subscription.
     */
    public static function currentUserHasSubscription(): bool
    {
        global $xoopsUser;
        if (! is_object($xoopsUser)) {
            return false;
        }
        $helper = Helper::getInstance();
        $handler = $helper->getHandler('Subscription');
        $sub = $handler->getActiveForUser((int) $xoopsUser->getVar('uid'));

        return $sub && $sub->isActive();
    }

    /**
     * Redirect to login if the visitor is not authenticated.
     */
    public static function requireLogin(): void
    {
        global $xoopsUser;
        if (! is_object($xoopsUser)) {
            redirect_header(XOOPS_URL . '/user.php', 2, _NOPERM);
        }
    }

    /**
     * Redirect to home if the visitor is not an XOOPS admin.
     */
    public static function requireAdmin(): void
    {
        global $xoopsUser;
        if (! is_object($xoopsUser) || ! $xoopsUser->isAdmin()) {
            redirect_header(XOOPS_URL . '/', 2, _NOPERM);
        }
    }

    // -------------------------------------------------------------------------
    // Config
    // -------------------------------------------------------------------------

    /**
     * Get a module config value, returning $default when absent or empty.
     *
     * @param mixed $default
     *
     * @return mixed
     */
    public static function config(string $key, $default = null)
    {
        $helper = Helper::getInstance();
        $val = $helper->getConfig($key);

        return ($val !== null && $val !== '') ? $val : $default;
    }

    // -------------------------------------------------------------------------
    // Networking
    // -------------------------------------------------------------------------

    /**
     * Return the real client IP address (v4 or v6), safe for logging.
     */
    public static function getIp(): string
    {
        foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $header) {
            if (! empty($_SERVER[$header])) {
                $ip = trim(explode(',', $_SERVER[$header])[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return '0.0.0.0';
    }

    // -------------------------------------------------------------------------
    // CSRF tokens
    // -------------------------------------------------------------------------

    /**
     * Generate a one-time CSRF token and store it in the session.
     */
    public static function generateToken(string $formName = 'default'): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $token = bin2hex(random_bytes(16));
        $_SESSION['subscriptions_token'][$formName] = $token;

        return $token;
    }

    /**
     * Verify (and consume) a one-time CSRF token.
     */
    public static function verifyToken(string $token, string $formName = 'default'): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $stored = $_SESSION['subscriptions_token'][$formName] ?? '';
        if ($stored === '' || ! hash_equals($stored, $token)) {
            return false;
        }
        unset($_SESSION['subscriptions_token'][$formName]);

        return true;
    }

    // -------------------------------------------------------------------------
    // String helpers
    // -------------------------------------------------------------------------

    /**
     * Truncate a UTF-8 string to $length characters, appending '…' if cut.
     */
    public static function truncate(string $str, int $length = 100): string
    {
        if (mb_strlen($str) <= $length) {
            return $str;
        }

        return mb_substr($str, 0, $length) . '…';
    }

    /**
     * Format a Unix timestamp for display; returns '—' for zero/negative values.
     */
    public static function formatDate(int $timestamp, string $format = 'Y-m-d'): string
    {
        if ($timestamp <= 0) {
            return '—';
        }

        return date($format, $timestamp);
    }

    // -------------------------------------------------------------------------
    // Template helpers
    // -------------------------------------------------------------------------

    /**
     * Flatten a XoopsObject's getVars() result into a plain key => scalar array
     * ready to pass to Smarty. (getVars() wraps each value in a metadata array;
     * Smarty cannot use that form directly.).
     */
    public static function objVars(XoopsObject $obj): array
    {
        $flat = [];
        foreach ($obj->getVars() as $key => $def) {
            $flat[$key] = is_array($def) ? ($def['value'] ?? null) : $def;
        }

        return $flat;
    }

    // -------------------------------------------------------------------------
    // Admin theme assets
    // -------------------------------------------------------------------------

    /**
     * Register module CSS + TableSorter into <head> via xoTheme.
     *
     * Must be called immediately after xoops_cp_header() so that
     * $GLOBALS['xoTheme'] is the real admin theme instance.
     */
    public static function addAdminAssets(): void
    {
        $helper = Helper::getInstance();
        $xoTheme = $GLOBALS['xoTheme'];
        $xoTheme->addStylesheet($helper->url('/assets/css/admin.css'));
        $xoTheme->addScript('browse.php?Frameworks/jquery/plugins/jquery.tablesorter.js');
        $xoTheme->addScript(
            '',
            ['type' => 'text/javascript'],
            'window.addEventListener("load",function(){jQuery(".xm-sortable").tablesorter({theme:"default",widthFixed:true});});'
        );
    }

    // -------------------------------------------------------------------------
    // Configuration Check
    // -------------------------------------------------------------------------

    /**
     * Returns an HTML block showing whether all module requirements are met.
     * Uses the module's CSS classes (admin.css) — designed to match the dashboard.
     *
     * Checks performed:
     *   - PHP version meets module minimum (from xoops_version.php)
     *   - XOOPS version meets module minimum
     *   - cURL extension    — required for payment gateway API calls
     *   - OpenSSL extension — required for Stripe HTTPS
     *   - mbstring extension — required for multi-byte string handling
     *   - JSON extension    — required for gateway API responses
     *   - Active payment gateway has been configured in the gateway settings
     *
     * @param \Xmf\Module\Helper $helper
     *
     * @return string
     */
    public static function getConfigCheck(\Xmf\Module\Helper $helper): string
    {
        $helper->loadLanguage('admin');

        // Read minimum requirements from xoops_version.php
        $module = $helper->getModule();
        $phpRequired = (string) $module->getInfo('min_php');
        $phpRequired = $phpRequired !== '' ? $phpRequired : '8.2';
        $xoopsRequired = (string) $module->getInfo('min_xoops');
        $xoopsRequired = $xoopsRequired !== '' ? $xoopsRequired : '2.5.12';

        $rows = [];

        // 1. PHP version --------------------------------------------------
        $phpOk = version_compare(PHP_VERSION, $phpRequired, '>=');
        $rows[] = [
            'status' => $phpOk ? 'pass' : 'fail',
            'icon'   => $phpOk ? '✔' : '✘',
            'label'  => 'PHP version ≥ ' . $phpRequired,
            'value'  => PHP_VERSION,
            'action' => $phpOk ? '' : '<a href="https://www.php.net/downloads" target="_blank" rel="noopener" style="font-size:0.8rem;color:#3b82f6;font-weight:600;text-decoration:none;white-space:nowrap;padding-left:0.5rem;">Upgrade PHP →</a>',
        ];

        // 2. XOOPS version ------------------------------------------------
        $xoopsVer = mb_substr(XOOPS_VERSION, 6);
        $xoopsOk = version_compare($xoopsVer, $xoopsRequired, '>=');
        $rows[] = [
            'status' => $xoopsOk ? 'pass' : 'fail',
            'icon'   => $xoopsOk ? '✔' : '✘',
            'label'  => 'XOOPS version ≥ ' . $xoopsRequired,
            'value'  => $xoopsVer,
            'action' => $xoopsOk ? '' : '<a href="https://xoops.org" target="_blank" rel="noopener" style="font-size:0.8rem;color:#3b82f6;font-weight:600;text-decoration:none;white-space:nowrap;padding-left:0.5rem;">Upgrade XOOPS →</a>',
        ];

        // 3. cURL extension -----------------------------------------------
        $curlOk = function_exists('curl_init');
        $rows[] = [
            'status' => $curlOk ? 'pass' : 'fail',
            'icon'   => $curlOk ? '✔' : '✘',
            'label'  => 'cURL extension',
            'value'  => $curlOk ? '' : 'Required — used for payment gateway API calls',
            'action' => '',
        ];

        // 4. OpenSSL extension --------------------------------------------
        $opensslOk = extension_loaded('openssl');
        $rows[] = [
            'status' => $opensslOk ? 'pass' : 'fail',
            'icon'   => $opensslOk ? '✔' : '✘',
            'label'  => 'OpenSSL extension',
            'value'  => $opensslOk ? '' : 'Required for Stripe HTTPS connections',
            'action' => '',
        ];

        // 5. mbstring extension -------------------------------------------
        $mbstringOk = extension_loaded('mbstring');
        $rows[] = [
            'status' => $mbstringOk ? 'pass' : 'warn',
            'icon'   => $mbstringOk ? '✔' : '⚠',
            'label'  => 'mbstring extension',
            'value'  => $mbstringOk ? '' : 'Recommended for multi-byte string handling',
            'action' => '',
        ];

        // 6. JSON extension -----------------------------------------------
        $jsonOk = function_exists('json_encode');
        $rows[] = [
            'status' => $jsonOk ? 'pass' : 'fail',
            'icon'   => $jsonOk ? '✔' : '✘',
            'label'  => 'JSON extension',
            'value'  => $jsonOk ? '' : 'Required for payment gateway API responses',
            'action' => '',
        ];

        // 7. Active gateway configured ------------------------------------
        $activeGateway = (string) ($helper->getConfig('active_gateway') ?? 'manual');
        $gatewayConfigured = true; // 'manual' needs no API keys
        if (in_array($activeGateway, ['paypal', 'stripe'], true)) {
            try {
                $gwHandler = $helper->getHandler('GatewayConfig');
                $criteria = new CriteriaCompo();
                $criteria->add(new Criteria('gateway_id', $activeGateway));
                $gatewayConfigured = $gwHandler->getCount($criteria) > 0;
            } catch (Throwable $e) {
                $gatewayConfigured = false;
            }
        }
        $gatewayLabel = htmlspecialchars(ucfirst($activeGateway), ENT_QUOTES);
        $gatewayAction = $gatewayConfigured
            ? ''
            : '<a href="gateways.php" style="font-size:0.8rem;color:#3b82f6;font-weight:600;text-decoration:none;white-space:nowrap;padding-left:0.5rem;">Configure →</a>';
        $rows[] = [
            'status' => $gatewayConfigured ? 'pass' : 'warn',
            'icon'   => $gatewayConfigured ? '✔' : '⚠',
            'label'  => 'Payment gateway (' . $gatewayLabel . ') configured',
            'value'  => '',
            'action' => $gatewayAction,
        ];

        // Build HTML — uses _serverCheckRow() from the ServerStats trait.
        // Inline styles on the <ul> override XOOPS admin theme list defaults.
        $html = '<ul class="xm-check-list" style="list-style:none;padding:0;margin:0;';
        $html .= 'border:1px solid #e5e7eb;border-radius:0.5rem;overflow:hidden;">';
        foreach ($rows as $row) {
            $html .= self::_serverCheckRow($row['status'], $row['icon'], $row['label'], $row['value'], $row['action']);
        }
        $html .= '</ul>';

        // Note: the Cron Job tip is rendered separately (above the two-column
        // grid) via getCronTip() because it is module-specific advice, not a
        // pass/fail requirement like the checks above.

        return $html;
    }

    /**
     * Returns a module-specific informational tip about the cron job.
     * Rendered ABOVE the Configuration Check / Server Status grid so it
     * stands out as actionable setup advice rather than a check result.
     *
     * @return string
     */
    public static function getCronTip(): string
    {
        $html = '<div class="xm-check-tip" style="margin:0 0 1rem;padding:0.75rem 1rem;';
        $html .= 'background:#fffbeb;border:1px solid #f59e0b;border-radius:0.5rem;';
        $html .= 'font-size:0.875rem;color:#92400e;line-height:1.5;">';
        $html .= '⏱ <strong>Cron Job (Recommended):</strong> Schedule ';
        $html .= '<code style="font-family:monospace;background:rgba(0,0,0,.06);border-radius:0.2rem;padding:0 0.3rem;">cron.php</code>';
        $html .= ' to run hourly for automatic subscription expiry checks and renewal reminders.';
        $html .= '</div>';

        return $html;
    }

    // -------------------------------------------------------------------------
    // Admin navigation
    // -------------------------------------------------------------------------

    /**
     * Return the standard admin navigation link array (shared by all admin pages).
     */
    public static function adminNav(): array
    {
        return [
            ['url' => 'index.php',         'label' => _AM_SUBSCRIPTIONS_DASHBOARD],
            ['url' => 'plans.php',         'label' => _AM_SUBSCRIPTIONS_PLANS],
            ['url' => 'subscriptions.php', 'label' => _AM_SUBSCRIPTIONS_SUBSCRIPTIONS],
            ['url' => 'payments.php',      'label' => _AM_SUBSCRIPTIONS_PAYMENTS],
            ['url' => 'coupons.php',       'label' => _AM_SUBSCRIPTIONS_COUPONS],
            ['url' => 'modules.php',       'label' => _AM_SUBSCRIPTIONS_MODULES],
            ['url' => 'gateways.php',      'label' => _AM_SUBSCRIPTIONS_GATEWAYS],
            ['url' => 'reports.php',       'label' => _AM_SUBSCRIPTIONS_REPORTS],
            ['url' => 'webhooks.php',      'label' => _AM_SUBSCRIPTIONS_WEBHOOKS],
        ];
    }
}
