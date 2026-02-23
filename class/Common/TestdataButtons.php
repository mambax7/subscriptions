<?php

declare(strict_types=1);

namespace XoopsModules\Subscriptions\Common;

/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * TestdataButtons — renders the Import/Export/Clear sample-data buttons in the admin.
 *
 * Design decisions vs. the publisher reference implementation:
 *  - State is stored exclusively in the XOOPS module preference (`displaySampleButton`),
 *    not in a separate config/admin.yml file, so there is only one source of truth.
 *  - The show/hide toggle writes back through the XOOPS config handler so the preference
 *    page and the button stay in sync automatically.
 *  - The class is self-contained: it derives dirname/namespace from __DIR__ and uses
 *    \Xmf\Module\Helper::getHelper() so it requires no module-specific imports.
 *
 * @copyright  XOOPS Project (https://xoops.org)
 * @license    GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 */

use Criteria;
use CriteriaCompo;
use XoopsConfigHandler;
use XoopsModules\Subscriptions\Helper;

/**
 * Class TestdataButtons.
 */
class TestdataButtons
{
    private const SHOW_BUTTONS = 1;

    private const HIDE_BUTTONS = 0;

    /**
     * Add testdata action buttons to the admin object.
     * Call this from admin/index.php when displaySampleButton preference is true.
     *
     * @param \Xmf\Module\Admin $adminObject
     */
    public static function loadButtonConfig($adminObject): void
    {
        $helper = Helper::getInstance();

        // Only add buttons when the master preference is ON.
        // When displaySampleButton=0 (set via Preferences page OR via the
        // in-page "Hide" button), nothing is rendered at all.
        // The caller in admin/index.php gates this call on the same preference,
        // so this check is a defensive guard only.
        if ((int) $helper->getConfig('displaySampleButton') !== self::SHOW_BUTTONS) {
            return;
        }

        $adminObject->addItemButton(
            _CO_SUBSCRIPTIONS_LOAD_SAMPLEDATA,
            $helper->url('testdata/index.php?op=load'),
            'add'
        );
        $adminObject->addItemButton(
            _CO_SUBSCRIPTIONS_SAVE_SAMPLEDATA,
            $helper->url('testdata/index.php?op=save'),
            'add'
        );
        $adminObject->addItemButton(
            _CO_SUBSCRIPTIONS_CLEAR_SAMPLEDATA,
            $helper->url('testdata/index.php?op=clear'),
            'alert'
        );
        $adminObject->addItemButton(
            _CO_SUBSCRIPTIONS_HIDE_SAMPLEDATA_BUTTONS,
            '?op=hide_buttons',
            'delete'
        );
    }

    /**
     * Hide the testdata buttons by updating the module preference.
     */
    public static function hideButtons(): void
    {
        self::setButtonVisibility(self::HIDE_BUTTONS);
    }

    /**
     * Show the testdata buttons by updating the module preference.
     */
    public static function showButtons(): void
    {
        self::setButtonVisibility(self::SHOW_BUTTONS);
    }

    /**
     * Write the displaySampleButton preference value via the XOOPS config system.
     * Using the config handler ensures the preference page and the button stay in sync.
     *
     * @param int $value self::SHOW_BUTTONS or self::HIDE_BUTTONS
     */
    private static function setButtonVisibility(int $value): void
    {
        $helper = Helper::getInstance();
        $moduleId = (int) $helper->getModule()->getVar('mid');

        /** @var XoopsConfigHandler $configHandler */
        $configHandler = xoops_getHandler('Config');

        // Locate the specific config item by module ID + name
        $criteria = new CriteriaCompo(new Criteria('conf_modid', $moduleId));
        $criteria->add(new Criteria('conf_name', 'displaySampleButton'));
        $configs = $configHandler->getConfigs($criteria);

        if (! empty($configs)) {
            $configItem = reset($configs);
            $configItem->setVar('conf_value', $value);
            $configHandler->insertConfig($configItem);
        }

        redirect_header('index.php', 0, '');
    }
}
