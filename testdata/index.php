<?php declare(strict_types=1);

/**
 * Subscriptions - Sample / Test Data Manager
 *
 * Operations:
 *   ?op=load   Import sample data (with confirmation step)
 *   ?op=save   Export current table data to YAML files
 *   ?op=clear  Truncate all module tables (with confirmation step)
 *
 * @copyright  XOOPS Project (https://xoops.org)
 * @license    GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 */

use Xmf\Database\TableLoad;
use Xmf\Request;
use Xmf\Yaml;
use XoopsModules\Subscriptions\Helper;

require \dirname(__DIR__, 3) . '/include/cp_header.php';
require \dirname(__DIR__) . '/preloads/autoloader.php';

$op = Request::getCmd('op', '');

$helper = Helper::getInstance();
$helper->loadLanguage('common');

switch ($op) {

    // ---- Import sample data ----------------------------------------------
    case 'load':
        if (Request::hasVar('ok', 'REQUEST') && 1 === Request::getInt('ok', 0)) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                \redirect_header(
                    $helper->url('admin/index.php'),
                    3,
                    \implode(',', $GLOBALS['xoopsSecurity']->getErrors())
                );
            }
            loadSampleData();
        } else {
            xoops_cp_header();
            xoops_confirm(
                ['ok' => 1, 'op' => 'load'],
                'index.php',
                _CO_SUBSCRIPTIONS_LOAD_SAMPLEDATA_CONFIRM,
                _CO_SUBSCRIPTIONS_CONFIRM,
                true
            );
            xoops_cp_footer();
        }
        break;

    // ---- Export current data to YAML ------------------------------------
    case 'save':
        saveSampleData();
        break;

    // ---- Clear all module data ------------------------------------------
    case 'clear':
        if (Request::hasVar('ok', 'REQUEST') && 1 === Request::getInt('ok', 0)) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                \redirect_header(
                    $helper->url('admin/index.php'),
                    3,
                    \implode(',', $GLOBALS['xoopsSecurity']->getErrors())
                );
            }
            clearSampleData();
        } else {
            xoops_cp_header();
            xoops_confirm(
                ['ok' => 1, 'op' => 'clear'],
                'index.php',
                _CO_SUBSCRIPTIONS_CLEAR_SAMPLEDATA_CONFIRM,
                _CO_SUBSCRIPTIONS_CONFIRM,
                true
            );
            xoops_cp_footer();
        }
        break;

    default:
        \redirect_header($helper->url('admin/index.php'), 0, '');
        break;
}

// ---------------------------------------------------------------------------
// Functions
// ---------------------------------------------------------------------------

/**
 * Load YAML fixtures into the database and copy test uploads.
 */
function loadSampleData(): void
{
    global $xoopsConfig;

    $moduleDirName = \basename(\dirname(__DIR__));
    $helper        = Helper::getInstance();

    $tables = $helper->getModule()->getInfo('tables');

    // Prefer the site language folder; fall back to english
    $language = 'english';
    if (\is_dir(__DIR__ . '/' . $xoopsConfig['language'])) {
        $language = $xoopsConfig['language'];
    }
    $dataDir = __DIR__ . '/' . $language . '/';

    // Truncate and reload each module table
    foreach ($tables as $table) {
        $ymlFile = $dataDir . $table . '.yml';
        if (\is_file($ymlFile)) {
            $tabledata = Yaml::readWrapped($ymlFile);
            TableLoad::truncateTable($table);
            TableLoad::loadTableFromArray($table, $tabledata);
        }
    }

    // Copy test uploads → XOOPS uploads/{moduleDirName}/
    $uploadsSrc  = __DIR__ . '/uploads';
    $uploadsDest = \XOOPS_UPLOAD_PATH . '/' . $moduleDirName;
    if (\is_dir($uploadsSrc)) {
        rcopyDir($uploadsSrc, $uploadsDest);
    }

    \redirect_header(
        '../admin/index.php',
        1,
        _CO_SUBSCRIPTIONS_LOAD_SAMPLEDATA_SUCCESS
    );
}

/**
 * Export current database tables to YAML in a timestamped subdirectory.
 */
function saveSampleData(): void
{
    global $xoopsConfig;

    $helper = Helper::getInstance();

    $tables = $helper->getModule()->getInfo('tables');

    $langFolder = __DIR__ . '/' . $xoopsConfig['language'];
    if (!\is_dir($langFolder)) {
        \mkdir($langFolder, 0755, true);
    }
    $exportFolder = $langFolder . '/Exports-' . \date('Y-m-d-H-i-s') . '/';
    \mkdir($exportFolder, 0755, true);

    foreach ($tables as $table) {
        TableLoad::saveTableToYamlFile($table, $exportFolder . $table . '.yml');
    }

    \redirect_header(
        '../admin/index.php',
        1,
        _CO_SUBSCRIPTIONS_SAVE_SAMPLEDATA_SUCCESS
    );
}

/**
 * Truncate all module tables.
 */
function clearSampleData(): void
{
    $helper = Helper::getInstance();

    $tables = $helper->getModule()->getInfo('tables');
    foreach ($tables as $table) {
        TableLoad::truncateTable($table);
    }

    \redirect_header(
        $helper->url('admin/index.php'),
        1,
        _CO_SUBSCRIPTIONS_CLEAR_SAMPLEDATA_OK
    );
}

/**
 * Recursively copy a directory tree.
 *
 * @param string $src  Source directory path
 * @param string $dest Destination directory path
 */
function rcopyDir(string $src, string $dest): void
{
    if (!\is_dir($dest)) {
        \mkdir($dest, 0755, true);
    }
    $dir = \opendir($src);
    if (!$dir) {
        return;
    }
    while (false !== ($file = \readdir($dir))) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        $srcPath  = $src  . '/' . $file;
        $destPath = $dest . '/' . $file;
        if (\is_dir($srcPath)) {
            rcopyDir($srcPath, $destPath);
        } else {
            \copy($srcPath, $destPath);
        }
    }
    \closedir($dir);
}
