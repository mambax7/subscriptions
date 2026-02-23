<?php

declare(strict_types=1);

namespace XoopsModules\Subscriptions\Common;

use Criteria;
use CriteriaCompo;
use Xmf\Module\Admin;
use Xmf\Request;
use XoopsBlock;
use XoopsDatabase;
use XoopsDatabaseFactory;
use XoopsFormButton;
use XoopsFormDhtmlTextArea;
use XoopsFormElementTray;
use XoopsFormHidden;
use XoopsFormLabel;
use XoopsFormRadioYN;
use XoopsFormSelect;
use XoopsFormSelectGroup;
use XoopsFormText;
use XoopsGroupPermHandler;
use XoopsMemberHandler;
use XoopsModuleHandler;
use XoopsThemeForm;
use XoopsTplfileHandler;

use function constant;
use function count;
use function in_array;
use function is_array;
use function sprintf;

use const XOOPS_CENTERBLOCK_BOTTOM;
use const XOOPS_CENTERBLOCK_BOTTOMLEFT;
use const XOOPS_CENTERBLOCK_BOTTOMRIGHT;
use const XOOPS_CENTERBLOCK_CENTER;
use const XOOPS_CENTERBLOCK_LEFT;
use const XOOPS_CENTERBLOCK_RIGHT;
use const XOOPS_SIDEBLOCK_LEFT;
use const XOOPS_SIDEBLOCK_RIGHT;

/**
 * Blocksadmin — manage this module's blocks from within the module admin.
 *
 * Ported from the Publisher module and cleaned up:
 *   - Language loaded once in the constructor, not repeated in every method.
 *   - Side-position state uses an array instead of eight cascading assignments.
 *   - Dead `$buttonTray` preview-button overwrite removed from render().
 *   - moduleDirName derived from the injected helper, not from __DIR__ depth.
 *
 * @copyright XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 */
class Blocksadmin
{
    public XoopsDatabase $db;

    public \Xmf\Module\Helper $helper;

    public string $moduleDirName;

    public string $moduleDirNameUpper;

    public string $pathIcon16;

    public function __construct(?XoopsDatabase $db, \Xmf\Module\Helper $helper)
    {
        $this->db = $db ?? XoopsDatabaseFactory::getDatabaseConnection();
        $this->helper = $helper;
        $this->moduleDirName = $helper->getDirname();
        $this->moduleDirNameUpper = mb_strtoupper($this->moduleDirName);
        $this->pathIcon16 = Admin::iconUrl('', '16');

        // Load all language files once here — not repeated in every method.
        xoops_loadLanguage('admin', 'system');
        xoops_loadLanguage('admin/blocksadmin', 'system');
        xoops_loadLanguage('admin/groups', 'system');
        xoops_loadLanguage('common', $this->moduleDirName);
        xoops_loadLanguage('blocksadmin', $this->moduleDirName);
    }

    public function listBlocks(): void
    {
        global $xoopsModule;

        require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';

        /** @var XoopsModuleHandler $moduleHandler */
        $moduleHandler = xoops_getHandler('module');
        /** @var XoopsMemberHandler $memberHandler */
        $memberHandler = xoops_getHandler('member');
        /** @var XoopsGroupPermHandler $grouppermHandler */
        $grouppermHandler = xoops_getHandler('groupperm');

        $groups = $memberHandler->getGroups();
        $criteria = new CriteriaCompo(new Criteria('hasmain', '1'));
        $criteria->add(new Criteria('isactive', '1'));
        $moduleList = $moduleHandler->getList($criteria);
        $moduleList[-1] = _AM_SYSTEM_BLOCKS_TOPPAGE;
        $moduleList[0] = _AM_SYSTEM_BLOCKS_ALLPAGES;
        ksort($moduleList);

        echo '<h4 style="text-align:left;">' . constant('_CO_' . $this->moduleDirNameUpper . '_BADMIN') . '</h4>';
        echo '<form action="' . $_SERVER['SCRIPT_NAME'] . '" name="blockadmin" method="post">';
        echo $GLOBALS['xoopsSecurity']->getTokenHTML();
        echo '<table width="100%" class="outer" cellpadding="4" cellspacing="1">
        <tr valign="middle">
            <th align="center">' . _AM_SYSTEM_BLOCKS_TITLE . '</th>
            <th align="center" nowrap="nowrap">' . constant('_CO_' . $this->moduleDirNameUpper . '_SIDE')
                                                 . '<br>' . _LEFT . '-' . _CENTER . '-' . _RIGHT . '</th>
            <th align="center">' . constant('_CO_' . $this->moduleDirNameUpper . '_WEIGHT') . '</th>
            <th align="center">' . constant('_CO_' . $this->moduleDirNameUpper . '_VISIBLE') . '</th>
            <th align="center">' . _AM_SYSTEM_BLOCKS_VISIBLEIN . '</th>
            <th align="center">' . _AM_SYSTEM_ADGS . '</th>
            <th align="center">' . _AM_SYSTEM_BLOCKS_BCACHETIME . '</th>
            <th align="center">' . constant('_CO_' . $this->moduleDirNameUpper . '_ACTION') . '</th>
        </tr>';

        $cachetimes = [
            0       => _NOCACHE,
            30      => sprintf(_SECONDS, 30),
            60      => _MINUTE,
            300     => sprintf(_MINUTES, 5),
            1800    => sprintf(_MINUTES, 30),
            3600    => _HOUR,
            18000   => sprintf(_HOURS, 5),
            86400   => _DAY,
            259200  => sprintf(_DAYS, 3),
            604800  => _WEEK,
            2592000 => _MONTH,
        ];

        $blockArray = XoopsBlock::getByModule($xoopsModule->mid());
        $class = 'even';

        foreach ($blockArray as $block) {
            $bid = $block->getVar('bid');
            $groupsPermissions = $grouppermHandler->getGroupIds('block_read', $bid);

            $sql = 'SELECT module_id FROM ' . $this->db->prefix('block_module_link') . ' WHERE block_id=' . $bid;
            $result = $this->db->query($sql);
            $modules = [];
            if (! $this->db->isResultSet($result)) {
                trigger_error("Query Failed! SQL: {$sql} Error: " . $this->db->error(), E_USER_ERROR);
            }
            while (false !== ($row = $this->db->fetchArray($result))) {
                $modules[] = (int) $row['module_id'];
            }

            // Build cache-time <option> list
            $cachetimeOptions = '';
            foreach ($cachetimes as $seconds => $label) {
                $selected = ($block->getVar('bcachetime') === $seconds) ? " selected='selected'" : '';
                $cachetimeOptions .= "<option value='{$seconds}'{$selected}>{$label}</option>\n";
            }

            // Side-position checked state — one array, no cascade
            $side = $block->getVar('side');
            $sideMap = [
                XOOPS_SIDEBLOCK_LEFT          => 'ssel0',
                XOOPS_SIDEBLOCK_RIGHT         => 'ssel1',
                XOOPS_CENTERBLOCK_LEFT        => 'ssel2',
                XOOPS_CENTERBLOCK_CENTER      => 'ssel3',
                XOOPS_CENTERBLOCK_RIGHT       => 'ssel4',
                XOOPS_CENTERBLOCK_BOTTOMLEFT  => 'ssel5',
                XOOPS_CENTERBLOCK_BOTTOMRIGHT => 'ssel6',
                XOOPS_CENTERBLOCK_BOTTOM      => 'ssel7',
            ];
            $ssel = array_fill_keys(array_values($sideMap), '');
            if (isset($sideMap[$side])) {
                $ssel[$sideMap[$side]] = ' checked';
            }

            $sel0 = $block->getVar('visible') === 1 ? '' : ' checked';
            $sel1 = $block->getVar('visible') === 1 ? ' checked' : '';

            $title = $block->getVar('title') ?: '&nbsp;';

            echo "<tr valign='top'>
                <td class='{$class}' align='center'>
                    <input type='text' name='title[{$bid}]' value='{$title}'>
                </td>
                <td class='{$class}' align='center' nowrap='nowrap'>
                    <div align='center'>
                        <input type='radio' name='side[{$bid}]' value='" . XOOPS_CENTERBLOCK_LEFT . "'{$ssel['ssel2']}>
                        <input type='radio' name='side[{$bid}]' value='" . XOOPS_CENTERBLOCK_CENTER . "'{$ssel['ssel3']}>
                        <input type='radio' name='side[{$bid}]' value='" . XOOPS_CENTERBLOCK_RIGHT . "'{$ssel['ssel4']}>
                    </div>
                    <div>
                        <span style='float:right;'><input type='radio' name='side[{$bid}]' value='" . XOOPS_SIDEBLOCK_RIGHT . "'{$ssel['ssel1']}></span>
                        <div align='left'><input type='radio' name='side[{$bid}]' value='" . XOOPS_SIDEBLOCK_LEFT . "'{$ssel['ssel0']}></div>
                    </div>
                    <div align='center'>
                        <input type='radio' name='side[{$bid}]' value='" . XOOPS_CENTERBLOCK_BOTTOMLEFT . "'{$ssel['ssel5']}>
                        <input type='radio' name='side[{$bid}]' value='" . XOOPS_CENTERBLOCK_BOTTOM . "'{$ssel['ssel7']}>
                        <input type='radio' name='side[{$bid}]' value='" . XOOPS_CENTERBLOCK_BOTTOMRIGHT . "'{$ssel['ssel6']}>
                    </div>
                </td>
                <td class='{$class}' align='center'>
                    <input type='text' name='weight[{$bid}]' value='" . $block->getVar('weight') . "' size='5' maxlength='5'>
                </td>
                <td class='{$class}' align='center' nowrap>
                    <input type='radio' name='visible[{$bid}]' value='1'{$sel1}>" . _YES . "
                    &nbsp;
                    <input type='radio' name='visible[{$bid}]' value='0'{$sel0}>" . _NO . '
                </td>';

            echo "<td class='{$class}' align='center'>
                <select size='5' name='bmodule[{$bid}][]' id='bmodule[{$bid}][]' multiple='multiple'>";
            foreach ($moduleList as $k => $v) {
                $sel = in_array($k, $modules, true) ? " selected='selected'" : '';
                echo "<option value='{$k}'{$sel}>{$v}</option>";
            }
            echo '</select></td>';

            echo "<td class='{$class}' align='center'>
                <select size='5' name='groups[{$bid}][]' id='groups[{$bid}][]' multiple='multiple'>";
            foreach ($groups as $grp) {
                $sel = in_array($grp->getVar('groupid'), $groupsPermissions, true) ? " selected='selected'" : '';
                echo "<option value='" . $grp->getVar('groupid') . "'{$sel}>" . $grp->getVar('name') . '</option>';
            }
            echo '</select></td>';

            echo '<td class="' . $class . '" align="center">
                <select name="bcachetime[' . $bid . ']" size="1">' . $cachetimeOptions . '</select>
            </td>';

            echo "<td class='{$class}' align='center'>
                <a href='blocksadmin.php?op=edit&amp;bid={$bid}'><img src='{$this->pathIcon16}/edit.png' alt='" . _EDIT . "' title='" . _EDIT . "'></a>
                <a href='blocksadmin.php?op=clone&amp;bid={$bid}'><img src='{$this->pathIcon16}/editcopy.png' alt='" . _CLONE . "' title='" . _CLONE . "'></a>";

            if (! in_array($block->getVar('block_type'), ['M', 'S'], true)) {
                echo "&nbsp;<a href='blocksadmin.php?op=delete&amp;bid={$bid}'><img src='{$this->pathIcon16}/delete.png' alt='" . _DELETE . "' title='" . _DELETE . "'></a>";
            }

            echo "
                <input type='hidden' name='oldtitle[{$bid}]'    value='" . $block->getVar('title') . "'>
                <input type='hidden' name='oldside[{$bid}]'     value='" . $block->getVar('side') . "'>
                <input type='hidden' name='oldweight[{$bid}]'   value='" . $block->getVar('weight') . "'>
                <input type='hidden' name='oldvisible[{$bid}]'  value='" . $block->getVar('visible') . "'>
                <input type='hidden' name='oldgroups[{$bid}]'   value='" . $block->getVar('groups') . "'>
                <input type='hidden' name='oldbcachetime[{$bid}]' value='" . $block->getVar('bcachetime') . "'>
                <input type='hidden' name='bid[{$bid}]'         value='{$bid}'>
            </td></tr>";

            $class = ('even' === $class) ? 'odd' : 'even';
        }

        echo "<tr>
            <td class='foot' align='center' colspan='8'>
                <input type='hidden' name='op' value='order'>
                " . $GLOBALS['xoopsSecurity']->getTokenHTML() . "
                <input type='submit' name='submit' value='" . _SUBMIT . "'>
            </td>
        </tr></table></form><br><br>";
    }

    public function deleteBlock(int $bid): void
    {
        $sql = sprintf('DELETE FROM %s WHERE bid = %u', $this->db->prefix('newblocks'), $bid);
        $this->db->queryF($sql) || trigger_error($GLOBALS['xoopsDB']->error());

        $sql = sprintf('DELETE FROM %s WHERE block_id = %u', $this->db->prefix('block_module_link'), $bid);
        $this->db->queryF($sql) || trigger_error($GLOBALS['xoopsDB']->error());

        $this->helper->redirect('admin/blocksadmin.php?op=list', 1, _AM_DBUPDATED);
    }

    public function cloneBlock(int $bid): void
    {
        $myblock = new XoopsBlock($bid);
        $sql = 'SELECT module_id FROM ' . $this->db->prefix('block_module_link') . ' WHERE block_id=' . $bid;
        $result = $this->db->query($sql);
        $modules = [];
        if ($this->db->isResultSet($result)) {
            while (false !== ($row = $this->db->fetchArray($result))) {
                $modules[] = (int) $row['module_id'];
            }
        }

        $block = [
            'title'      => $myblock->getVar('title') . ' Clone',
            'form_title' => constant('_CO_' . $this->moduleDirNameUpper . '_BLOCKS_CLONEBLOCK'),
            'name'       => $myblock->getVar('name'),
            'side'       => $myblock->getVar('side'),
            'weight'     => $myblock->getVar('weight'),
            'visible'    => $myblock->getVar('visible'),
            'content'    => $myblock->getVar('content', 'N'),
            'modules'    => $modules,
            'is_custom'  => in_array($myblock->getVar('block_type'), ['C', 'E'], true),
            'ctype'      => $myblock->getVar('c_type'),
            'bcachetime' => $myblock->getVar('bcachetime'),
            'op'         => 'clone_ok',
            'bid'        => $myblock->getVar('bid'),
            'edit_form'  => $myblock->getOptions(),
            'template'   => $myblock->getVar('template'),
            'options'    => $myblock->getVar('options'),
        ];

        echo '<a href="blocksadmin.php">' . constant('_CO_' . $this->moduleDirNameUpper . '_BADMIN') . '</a>'
            . '&nbsp;<span style="font-weight:bold;">&raquo;&raquo;</span>&nbsp;'
            . _AM_SYSTEM_BLOCKS_CLONEBLOCK . '<br><br>';
        echo $this->render($block);
    }

    public function isBlockCloned(int $bid, string $bside, string $bweight, string $bvisible, string $bcachetime, ?array $bmodule, ?array $options, ?array $groups): void
    {
        $block = new XoopsBlock($bid);
        /** @var XoopsBlock $clone */
        $clone = $block->xoopsClone();

        if (empty($bmodule)) {
            xoops_error(sprintf(_AM_NOTSELNG, _AM_VISIBLEIN));
            xoops_cp_footer();
            exit();
        }

        $clone->setVar('side', $bside);
        $clone->setVar('weight', $bweight);
        $clone->setVar('visible', $bvisible);
        $clone->setVar('title', Request::getString('btitle', '', 'POST'));
        $clone->setVar('bcachetime', $bcachetime);
        if (! empty($options)) {
            $clone->setVar('options', implode('|', $options));
        }
        $clone->setVar('bid', 0);
        $clone->setVar('block_type', in_array($block->getVar('block_type'), ['C', 'E'], true) ? 'E' : 'D');

        if (! $clone->store()) {
            $clone->getHtmlErrors();
            xoops_cp_footer();
            exit();
        }
        $newid = $clone->id();

        if ('' !== $clone->getVar('template')) {
            /** @var XoopsTplfileHandler $tplfileHandler */
            $tplfileHandler = xoops_getHandler('tplfile');
            $btemplate = $tplfileHandler->find($GLOBALS['xoopsConfig']['template_set'], 'block', (string) $bid);
            if (count($btemplate) > 0) {
                $tplclone = $btemplate[0]->xoopsClone();
                $tplclone->setVar('tpl_id', 0);
                $tplclone->setVar('tpl_refid', $newid);
                $tplfileHandler->insert($tplclone);
            }
        }

        foreach ($bmodule as $bmid) {
            $sql = 'INSERT INTO ' . $this->db->prefix('block_module_link') . ' (block_id, module_id) VALUES (' . $newid . ', ' . (int) $bmid . ')';
            $this->db->query($sql);
        }
        foreach ($groups as $grp) {
            $sql = 'INSERT INTO ' . $this->db->prefix('group_permission') . ' (gperm_groupid, gperm_itemid, gperm_modid, gperm_name) VALUES (' . (int) $grp . ", {$newid}, 1, 'block_read')";
            $this->db->query($sql);
        }

        $this->helper->redirect('admin/blocksadmin.php?op=list', 1, _AM_DBUPDATED);
    }

    public function editBlock(int $bid): void
    {
        $myblock = new XoopsBlock($bid);
        $sql = 'SELECT module_id FROM ' . $this->db->prefix('block_module_link') . ' WHERE block_id=' . $bid;
        $result = $this->db->query($sql);
        $modules = [];
        if ($this->db->isResultSet($result)) {
            while (false !== ($row = $this->db->fetchArray($result))) {
                $modules[] = (int) $row['module_id'];
            }
        }

        $block = [
            'title'      => $myblock->getVar('title'),
            'form_title' => _AM_SYSTEM_BLOCKS_EDITBLOCK,
            'side'       => $myblock->getVar('side'),
            'weight'     => $myblock->getVar('weight'),
            'visible'    => $myblock->getVar('visible'),
            'content'    => $myblock->getVar('content', 'N'),
            'modules'    => $modules,
            'is_custom'  => in_array($myblock->getVar('block_type'), ['C', 'E'], true),
            'ctype'      => $myblock->getVar('c_type'),
            'bcachetime' => $myblock->getVar('bcachetime'),
            'op'         => 'edit_ok',
            'bid'        => $myblock->getVar('bid'),
            'edit_form'  => $myblock->getOptions(),
            'template'   => $myblock->getVar('template'),
            'options'    => $myblock->getVar('options'),
        ];

        echo '<a href="blocksadmin.php">' . constant('_CO_' . $this->moduleDirNameUpper . '_BADMIN') . '</a>'
            . '&nbsp;<span style="font-weight:bold;">&raquo;&raquo;</span>&nbsp;'
            . _AM_SYSTEM_BLOCKS_EDITBLOCK . '<br><br>';
        echo $this->render($block);
    }

    public function updateBlock(int $bid, string $btitle, string $bside, string $bweight, string $bvisible, string $bcachetime, ?array $bmodule, ?array $options, ?array $groups): void
    {
        $myblock = new XoopsBlock($bid);
        $myblock->setVar('title', $btitle);
        $myblock->setVar('weight', $bweight);
        $myblock->setVar('visible', $bvisible);
        $myblock->setVar('side', $bside);
        $myblock->setVar('bcachetime', $bcachetime);

        if (! empty($options)) {
            foreach ($options as $i => $val) {
                if (is_array($val)) {
                    $options[$i] = implode(',', $val);
                }
            }
            $myblock->setVar('options', implode('|', $options));
        }
        $myblock->store();

        if (! empty($bmodule)) {
            $sql = sprintf('DELETE FROM `%s` WHERE block_id = %u', $this->db->prefix('block_module_link'), $bid);
            $this->db->query($sql);
            if (in_array(0, $bmodule, true)) {
                $sql = sprintf('INSERT INTO `%s` (block_id, module_id) VALUES (%u, 0)', $this->db->prefix('block_module_link'), $bid);
                $this->db->query($sql);
            } else {
                foreach ($bmodule as $bmid) {
                    $sql = sprintf('INSERT INTO `%s` (block_id, module_id) VALUES (%u, %d)', $this->db->prefix('block_module_link'), $bid, (int) $bmid);
                    $this->db->query($sql);
                }
            }
        }

        $sql = sprintf('DELETE FROM `%s` WHERE gperm_itemid = %u', $this->db->prefix('group_permission'), $bid);
        $this->db->query($sql);
        foreach ((array) $groups as $grp) {
            $sql = sprintf("INSERT INTO `%s` (gperm_groupid, gperm_itemid, gperm_modid, gperm_name) VALUES (%u, %u, 1, 'block_read')", $this->db->prefix('group_permission'), (int) $grp, $bid);
            $this->db->query($sql);
        }

        $this->helper->redirect('admin/blocksadmin.php', 1, constant('_CO_' . $this->moduleDirNameUpper . '_UPDATE_SUCCESS'));
    }

    public function setOrder(string $bid, string $title, string $weight, string $visible, string $side, string $bcachetime, ?array $bmodule = null): void
    {
        $myblock = new XoopsBlock($bid);
        $myblock->setVar('title', $title);
        $myblock->setVar('weight', $weight);
        $myblock->setVar('visible', $visible);
        $myblock->setVar('side', $side);
        $myblock->setVar('bcachetime', $bcachetime);
        $myblock->store();
    }

    public function orderBlock(array $bid, array $oldtitle, array $oldside, array $oldweight, array $oldvisible, array $oldgroups, array $oldbcachetime, array $oldbmodule, array $title, array $weight, array $visible, array $side, array $bcachetime, array $groups, array $bmodule): void
    {
        if (! $GLOBALS['xoopsSecurity']->check()) {
            redirect_header($_SERVER['SCRIPT_NAME'], 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        }

        foreach (array_keys($bid) as $i) {
            if ($oldtitle[$i] !== $title[$i]
                || $oldweight[$i] !== $weight[$i]
                || $oldvisible[$i] !== $visible[$i]
                || $oldside[$i] !== $side[$i]
                || $oldbcachetime[$i] !== $bcachetime[$i]
                || $oldbmodule[$i] !== $bmodule[$i]) {
                $this->setOrder($bid[$i], $title[$i], $weight[$i], $visible[$i], $side[$i], $bcachetime[$i], $bmodule[$i]);
            }

            if (! empty($bmodule[$i])) {
                $sql = sprintf('DELETE FROM `%s` WHERE block_id = %u', $this->db->prefix('block_module_link'), $bid[$i]);
                $this->db->query($sql);
                if (in_array(0, $bmodule[$i], true)) {
                    $sql = sprintf('INSERT INTO `%s` (block_id, module_id) VALUES (%u, 0)', $this->db->prefix('block_module_link'), $bid[$i]);
                    $this->db->query($sql);
                } else {
                    foreach ($bmodule[$i] as $bmid) {
                        $sql = sprintf('INSERT INTO `%s` (block_id, module_id) VALUES (%u, %d)', $this->db->prefix('block_module_link'), $bid[$i], (int) $bmid);
                        $this->db->query($sql);
                    }
                }
            }

            $sql = sprintf('DELETE FROM `%s` WHERE gperm_itemid = %u', $this->db->prefix('group_permission'), $bid[$i]);
            $this->db->query($sql);
            foreach ((array) ($groups[$i] ?? []) as $grp) {
                $sql = sprintf("INSERT INTO `%s` (gperm_groupid, gperm_itemid, gperm_modid, gperm_name) VALUES (%u, %u, 1, 'block_read')", $this->db->prefix('group_permission'), (int) $grp, $bid[$i]);
                $this->db->query($sql);
            }
        }

        $this->helper->redirect('admin/blocksadmin.php', 1, constant('_CO_' . $this->moduleDirNameUpper . '_UPDATE_SUCCESS'));
    }

    public function render(?array $block = null): void
    {
        xoops_load('XoopsFormLoader');

        $form = new XoopsThemeForm($block['form_title'], 'blockform', 'blocksadmin.php', 'post', true);

        if (isset($block['name'])) {
            $form->addElement(new XoopsFormLabel(_AM_SYSTEM_BLOCKS_NAME, $block['name']));
        }

        $sideSelect = new XoopsFormSelect(_AM_SYSTEM_BLOCKS_TYPE, 'bside', $block['side']);
        $sideSelect->addOptionArray([
            0 => _AM_SYSTEM_BLOCKS_SBLEFT,
            1 => _AM_SYSTEM_BLOCKS_SBRIGHT,
            3 => _AM_SYSTEM_BLOCKS_CBLEFT,
            4 => _AM_SYSTEM_BLOCKS_CBRIGHT,
            5 => _AM_SYSTEM_BLOCKS_CBCENTER,
            7 => _AM_SYSTEM_BLOCKS_CBBOTTOMLEFT,
            8 => _AM_SYSTEM_BLOCKS_CBBOTTOMRIGHT,
            9 => _AM_SYSTEM_BLOCKS_CBBOTTOM,
        ]);
        $form->addElement($sideSelect);

        $form->addElement(new XoopsFormText(constant('_CO_' . $this->moduleDirNameUpper . '_WEIGHT'), 'bweight', 2, 5, $block['weight']));
        $form->addElement(new XoopsFormRadioYN(constant('_CO_' . $this->moduleDirNameUpper . '_VISIBLE'), 'bvisible', $block['visible']));

        $modSelect = new XoopsFormSelect(constant('_CO_' . $this->moduleDirNameUpper . '_VISIBLEIN'), 'bmodule', $block['modules'], 5, true);
        /** @var XoopsModuleHandler $moduleHandler */
        $moduleHandler = xoops_getHandler('module');
        $criteria = new CriteriaCompo(new Criteria('hasmain', '1'));
        $criteria->add(new Criteria('isactive', '1'));
        $moduleList = $moduleHandler->getList($criteria);
        $moduleList[-1] = _AM_SYSTEM_BLOCKS_TOPPAGE;
        $moduleList[0] = _AM_SYSTEM_BLOCKS_ALLPAGES;
        ksort($moduleList);
        $modSelect->addOptionArray($moduleList);
        $form->addElement($modSelect);

        $form->addElement(new XoopsFormText(_AM_SYSTEM_BLOCKS_TITLE, 'btitle', 50, 255, $block['title']));

        if ($block['is_custom']) {
            $textarea = new XoopsFormDhtmlTextArea(_AM_SYSTEM_BLOCKS_CONTENT, 'bcontent', $block['content'], 15, 70);
            $textarea->setDescription(
                '<span style="font-size:x-small;font-weight:bold;">' . _AM_SYSTEM_BLOCKS_USEFULTAGS . '</span>'
                . '<br><span style="font-size:x-small;">' . sprintf(_AM_BLOCKTAG1, '{X_SITEURL}', XOOPS_URL . '/') . '</span>'
            );
            $form->addElement($textarea, true);
            $ctypeSelect = new XoopsFormSelect(_AM_SYSTEM_BLOCKS_CTYPE, 'bctype', $block['ctype']);
            $ctypeSelect->addOptionArray([
                'H' => _AM_SYSTEM_BLOCKS_HTML,
                'P' => _AM_SYSTEM_BLOCKS_PHP,
                'S' => _AM_SYSTEM_BLOCKS_AFWSMILE,
                'T' => _AM_SYSTEM_BLOCKS_AFNOSMILE,
            ]);
            $form->addElement($ctypeSelect);
        } else {
            if ('' !== $block['template']) {
                /** @var XoopsTplfileHandler $tplfileHandler */
                $tplfileHandler = xoops_getHandler('tplfile');
                $btemplate = $tplfileHandler->find($GLOBALS['xoopsConfig']['template_set'], 'block', $block['bid']);
                $tpl = count($btemplate) > 0 ? $btemplate[0] : null;
                if (null === $tpl) {
                    $fallback = $tplfileHandler->find('default', 'block', $block['bid']);
                    $tpl = count($fallback) > 0 ? $fallback[0] : null;
                }
                if (null !== $tpl) {
                    $form->addElement(new XoopsFormLabel(
                        _AM_SYSTEM_BLOCKS_CONTENT,
                        '<a href="' . XOOPS_URL . '/modules/system/admin.php?fct=tplsets&amp;op=edittpl&amp;id=' . $tpl->getVar('tpl_id') . '">' . _AM_SYSTEM_BLOCKS_EDITTPL . '</a>'
                    ));
                }
            }
            if (false !== $block['edit_form']) {
                $form->addElement(new XoopsFormLabel(_AM_SYSTEM_BLOCKS_OPTIONS, $block['edit_form']));
            }
        }

        $cacheSelect = new XoopsFormSelect(_AM_SYSTEM_BLOCKS_BCACHETIME, 'bcachetime', $block['bcachetime']);
        $cacheSelect->addOptionArray([
            0       => _NOCACHE,
            30      => sprintf(_SECONDS, 30),
            60      => _MINUTE,
            300     => sprintf(_MINUTES, 5),
            1800    => sprintf(_MINUTES, 30),
            3600    => _HOUR,
            18000   => sprintf(_HOURS, 5),
            86400   => _DAY,
            259200  => sprintf(_DAYS, 3),
            604800  => _WEEK,
            2592000 => _MONTH,
        ]);
        $form->addElement($cacheSelect);

        /** @var XoopsGroupPermHandler $grouppermHandler */
        $grouppermHandler = xoops_getHandler('groupperm');
        $groups = $grouppermHandler->getGroupIds('block_read', $block['bid']);
        $form->addElement(new XoopsFormSelectGroup(_AM_SYSTEM_BLOCKS_GROUP, 'groups', true, $groups, 5, true));

        if (isset($block['bid'])) {
            $form->addElement(new XoopsFormHidden('bid', $block['bid']));
        }
        $form->addElement(new XoopsFormHidden('op', $block['op']));
        $form->addElement(new XoopsFormHidden('fct', 'blocksadmin'));

        $buttonTray = new XoopsFormElementTray('', '');
        $buttonTray->addElement(new XoopsFormButton('', 'submitblock', _SUBMIT, 'submit'));
        $cancel = new XoopsFormButton('', '', _CANCEL, 'button');
        $cancel->setExtra('onclick="history.go(-1)"');
        $buttonTray->addElement($cancel);
        $form->addElement($buttonTray);

        $form->display();
    }
}
