<?php

declare(strict_types=1);

/**
 * Blocks Admin — manage this module's blocks without leaving the module admin.
 *
 * @copyright XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 */

use Xmf\Request;
use XoopsModules\Subscriptions\{
    Common\Blocksadmin,
    Helper,
    Utility
};

require __DIR__ . '/admin_header.php';
xoops_cp_header();
Utility::addAdminAssets();

$helper      = Helper::getInstance();
$xoopsDB     = \XoopsDatabaseFactory::getDatabaseConnection();
$blocksadmin = new Blocksadmin($xoopsDB, $helper);
$xoopsModule = \XoopsModule::getByDirname($helper->getDirname());

if (!is_object($GLOBALS['xoopsUser'])
    || !is_object($xoopsModule)
    || !$GLOBALS['xoopsUser']->isAdmin($xoopsModule->mid())) {
    exit(_AM_SUBSCRIPTIONS_BLOCKS_ERROR403);
}

require_once XOOPS_ROOT_PATH . '/class/xoopsblock.php';

$op = Request::getCmd('op', 'list');

// Collect POST vars only when needed
if (!empty($_POST)) {
    $btitle     = Request::getString('btitle',     '',  'POST');
    $bside      = Request::getString('bside',      '0', 'POST');
    $bweight    = Request::getString('bweight',    '0', 'POST');
    $bvisible   = Request::getString('bvisible',   '0', 'POST');
    $bcachetime = Request::getString('bcachetime', '0', 'POST');
    $bmodule    = Request::getArray('bmodule',  [], 'POST');
    $groups     = Request::getArray('groups',   [], 'POST');
    $options    = Request::getArray('options',  [], 'POST');
}

if ('list' === $op) {
    $blocksadmin->listBlocks();
    require __DIR__ . '/admin_footer.php';
    exit();
}

if (in_array($op, ['edit', 'edit_ok', 'delete', 'delete_ok', 'clone', 'clone_ok'], true)) {
    $bid = Request::getInt('bid', 0);
    $ok  = Request::getInt('ok',  0);

    switch ($op) {
        case 'clone':
            $blocksadmin->cloneBlock($bid);
            break;
        case 'clone_ok':
            $blocksadmin->isBlockCloned($bid, $bside, $bweight, $bvisible, $bcachetime, $bmodule, $options, $groups);
            break;
        case 'edit':
            $blocksadmin->editBlock($bid);
            break;
        case 'edit_ok':
            $blocksadmin->updateBlock($bid, $btitle, $bside, $bweight, $bvisible, $bcachetime, $bmodule, $options, $groups);
            break;
        case 'delete':
            if (1 === $ok) {
                $blocksadmin->deleteBlock($bid);
            } else {
                xoops_confirm(
                    ['ok' => 1, 'op' => 'delete', 'bid' => $bid],
                    'blocksadmin.php',
                    _AM_SUBSCRIPTIONS_BLOCKS_DELETE_CONFIRM,
                    _AM_SUBSCRIPTIONS_BLOCKS_CONFIRM,
                    true
                );
                xoops_cp_footer();
            }
            break;
    }
}

if ('order' === $op) {
    $bid        = Request::getArray('bid',        [], 'POST');
    $title      = Request::getArray('title',      [], 'POST');
    $side       = Request::getArray('side',       [], 'POST');
    $weight     = Request::getArray('weight',     [], 'POST');
    $visible    = Request::getArray('visible',    [], 'POST');
    $bcachetime = Request::getArray('bcachetime', [], 'POST');
    $bmodule    = Request::getArray('bmodule',    [], 'POST');
    $groups     = Request::getArray('groups',     [], 'POST');
    $oldtitle      = Request::getArray('oldtitle',    [], 'POST');
    $oldside       = Request::getArray('oldside',     [], 'POST');
    $oldweight     = Request::getArray('oldweight',   [], 'POST');
    $oldvisible    = Request::getArray('oldvisible',  [], 'POST');
    $oldgroups     = Request::getArray('oldgroups',   [], 'POST');
    $oldbcachetime = Request::getArray('oldcachetime',[], 'POST');
    $oldbmodule    = Request::getArray('oldbmodule',  [], 'POST');

    $blocksadmin->orderBlock(
        $bid, $oldtitle, $oldside, $oldweight, $oldvisible, $oldgroups,
        $oldbcachetime, $oldbmodule, $title, $weight, $visible, $side,
        $bcachetime, $groups, $bmodule
    );
}

require __DIR__ . '/admin_footer.php';
