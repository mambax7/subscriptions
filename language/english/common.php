<?php
/**
 * Subscriptions Language file - Common (English)
 *
 * Constants prefixed with _CO_SUBSCRIPTIONS_ are used by shared components
 * (TestdataButtons, etc.) and by the testdata/index.php script.
 *
 * Naming convention: _CO_<MODULENAME>_<KEY>
 *   - Leading underscore (_CO_) matches the XOOPS standard for all language constants.
 *   - Module name is hardcoded (not built dynamically) so the file is future-ready
 *     for the array-return translation format.
 *
 * @package    subscriptions
 * @subpackage language
 */

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

// ---- Sample / test data buttons -----------------------------------------
define('_CO_SUBSCRIPTIONS_LOAD_SAMPLEDATA',          'Import Sample Data (will delete ALL current data)');
define('_CO_SUBSCRIPTIONS_LOAD_SAMPLEDATA_CONFIRM',  'Are you sure you want to Import Sample Data? This will DELETE ALL current module data and replace it with sample records.');
define('_CO_SUBSCRIPTIONS_LOAD_SAMPLEDATA_SUCCESS',  'Sample data imported successfully.');
define('_CO_SUBSCRIPTIONS_SAVE_SAMPLEDATA',          'Export Tables to YAML');
define('_CO_SUBSCRIPTIONS_SAVE_SAMPLEDATA_SUCCESS',  'Tables exported to YAML successfully.');
define('_CO_SUBSCRIPTIONS_CLEAR_SAMPLEDATA',         'Clear Sample Data');
define('_CO_SUBSCRIPTIONS_CLEAR_SAMPLEDATA_CONFIRM', 'Are you sure you want to Clear all Sample Data? This will TRUNCATE all module tables.');
define('_CO_SUBSCRIPTIONS_CLEAR_SAMPLEDATA_OK',      'All sample data has been cleared.');
define('_CO_SUBSCRIPTIONS_HIDE_SAMPLEDATA_BUTTONS',  'Hide the Import Buttons');
define('_CO_SUBSCRIPTIONS_SHOW_SAMPLEDATA_BUTTONS',  'Show the Import Buttons');
define('_CO_SUBSCRIPTIONS_CONFIRM',                  'Confirm');

// ---- Developer tools -----------------------------------------------------
define('_CO_SUBSCRIPTIONS_SHOW_DEV_TOOLS',           'Show Developer Tools?');
define('_CO_SUBSCRIPTIONS_SHOW_DEV_TOOLS_DESC',      'If Yes, additional developer/diagnostic tools are shown in the admin panel.');

// ---- ServerStats trait constants -----------------------------------------
// Used via constant('CO_' . $moduleDirNameUpper . '_' . 'KEY') — NO leading underscore.
define('CO_SUBSCRIPTIONS_IMAGEINFO',          'Server Status');
define('CO_SUBSCRIPTIONS_SPHPINI',            'PHP Configuration');
define('CO_SUBSCRIPTIONS_GDLIBSTATUS',        'GD Library: ');
define('CO_SUBSCRIPTIONS_GDON',               'Enabled');
define('CO_SUBSCRIPTIONS_GDOFF',              'Disabled');
define('CO_SUBSCRIPTIONS_GDLIBVERSION',       'GD Library Version: ');
define('CO_SUBSCRIPTIONS_ON',                 'Enabled');
define('CO_SUBSCRIPTIONS_OFF',                'Disabled');
define('CO_SUBSCRIPTIONS_SERVERUPLOADSTATUS', 'File Uploads: ');
define('CO_SUBSCRIPTIONS_MAXUPLOADSIZE',      'Max Upload File Size: ');
define('CO_SUBSCRIPTIONS_MAXPOSTSIZE',        'Max POST Size: ');
define('CO_SUBSCRIPTIONS_MEMORYLIMIT',        'PHP Memory Limit: ');
define('CO_SUBSCRIPTIONS_SERVERPATH',         'XOOPS Root Path: ');
define('CO_SUBSCRIPTIONS_UPLOADPATHDSC',      '');

// ---- VersionChecks trait constants ----------------------------------------
define('CO_SUBSCRIPTIONS_ERROR_BAD_XOOPS', 'XOOPS %s or higher is required. You are running XOOPS %s.');
define('CO_SUBSCRIPTIONS_ERROR_BAD_PHP',   'PHP %s or higher is required. You are running PHP %s.');
define('CO_SUBSCRIPTIONS_NEW_VERSION',     'New version available: ');

// ---- Blocks admin --------------------------------------------------------
define('_CO_SUBSCRIPTIONS_BADMIN',           'Blocks Administration');
define('_CO_SUBSCRIPTIONS_SIDE',             'Side');
define('_CO_SUBSCRIPTIONS_WEIGHT',           'Weight');
define('_CO_SUBSCRIPTIONS_VISIBLE',          'Visible');
define('_CO_SUBSCRIPTIONS_VISIBLEIN',        'Visible In');
define('_CO_SUBSCRIPTIONS_ACTION',           'Action');
define('_CO_SUBSCRIPTIONS_UPDATE_SUCCESS',   'Block updated successfully.');
define('_CO_SUBSCRIPTIONS_BLOCKS_CLONEBLOCK',        'Clone Block');
define('_CO_SUBSCRIPTIONS_DELETE_BLOCK_CONFIRM',     'Are you sure you want to delete this block?');
