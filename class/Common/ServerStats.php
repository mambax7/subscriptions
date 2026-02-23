<?php

declare(strict_types=1);

namespace XoopsModules\Subscriptions\Common;

use function dirname;
use function function_exists;
use function ini_get;
use function sprintf;

use const ENT_QUOTES;
use const XOOPS_ROOT_PATH;

/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright   2000-2025 XOOPS Project (https://xoops.org)
 * @license     GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @author      mamba <mambax7@gmail.com>
 */
trait ServerStats
{
    /**
     * Returns an HTML block showing PHP/server environment information.
     * Uses CSS classes (admin.css) plus inline-style fallbacks so colours
     * render correctly even when the XOOPS admin theme overrides list styles.
     *
     * @return string
     */
    public static function getServerStats(): string
    {
        $moduleDirName = basename(dirname(__DIR__, 2));
        xoops_loadLanguage('common', $moduleDirName);

        $html = '<ul class="xm-check-list" style="list-style:none;padding:0;margin:0 0 0.75rem;';
        $html .= 'border:1px solid #e5e7eb;border-radius:0.5rem;overflow:hidden;">';

        // ---- GD Library -------------------------------------------------------
        if (function_exists('gd_info')) {
            $gdlib = gd_info();
            $version = ! empty($gdlib['GD Version'])
                ? htmlspecialchars($gdlib['GD Version'], ENT_QUOTES)
                : 'installed';
            $html .= self::_serverCheckRow('pass', '✔', 'GD Library', $version);
        } else {
            $html .= self::_serverCheckRow('warn', '⚠', 'GD Library', 'Not installed — optional, used for image processing');
        }

        // ---- File Uploads -----------------------------------------------------
        $uploadsOn = (bool) ini_get('file_uploads');
        $html .= self::_serverCheckRow(
            $uploadsOn ? 'pass' : 'warn',
            $uploadsOn ? '✔' : '⚠',
            'File Uploads',
            $uploadsOn ? 'Enabled' : 'Disabled'
        );

        // ---- PHP limits -------------------------------------------------------
        $html .= self::_serverCheckRow('info', 'ℹ', 'Max Upload File Size', ini_get('upload_max_filesize'));
        $html .= self::_serverCheckRow('info', 'ℹ', 'Max POST Size', ini_get('post_max_size'));
        $html .= self::_serverCheckRow('info', 'ℹ', 'PHP Memory Limit', ini_get('memory_limit'));

        // ---- XOOPS root path --------------------------------------------------
        $html .= self::_serverCheckRow('info', '📁', 'XOOPS Root Path', htmlspecialchars(XOOPS_ROOT_PATH, ENT_QUOTES));

        $html .= '</ul>';

        return $html;
    }

    /**
     * Builds a single <li> row for the check list.
     * Protected so subclasses (Utility) can reuse it for getConfigCheck().
     *
     * Inline styles are intentional — XOOPS admin themes often override
     * custom CSS classes on <ul>/<li> elements.
     *
     * @param string $status Semantic state: pass | fail | warn | info
     * @param string $icon Emoji/character shown in the icon cell
     * @param string $label Human-readable description
     * @param string $value Technical value shown as <code>; omitted when empty
     * @param string $action Raw HTML for an optional action link; omitted when empty
     *
     * @return string
     */
    protected static function _serverCheckRow(
        string $status,
        string $icon,
        string $label,
        string $value = '',
        string $action = ''
    ): string {
        // Colour map — mirrors the CSS variables in admin.css
        $colours = [
            'pass' => ['border' => '#22c55e', 'bg' => 'transparent',         'icon' => '#22c55e'],
            'fail' => ['border' => '#ef4444', 'bg' => '#fef2f2',              'icon' => '#ef4444'],
            'warn' => ['border' => '#f59e0b', 'bg' => '#fffbeb',              'icon' => '#f59e0b'],
            'info' => ['border' => '#3b82f6', 'bg' => 'transparent',          'icon' => '#3b82f6'],
        ];
        $c = $colours[$status] ?? $colours['info'];

        $liStyle = sprintf(
            'display:flex;align-items:center;gap:0.5rem;padding:0.5rem 1rem;'
            . 'background:%s;border-bottom:1px solid #e5e7eb;border-left:4px solid %s;',
            $c['bg'],
            $c['border']
        );
        $iconStyle = sprintf('color:%s;font-size:1rem;flex-shrink:0;width:1.25rem;text-align:center;', $c['icon']);
        $labelStyle = 'flex:1;font-size:0.875rem;color:#1f2937;';
        $valueStyle = 'font-family:monospace;font-size:0.8125rem;color:#6b7280;'
            . 'background:#f3f4f6;border:1px solid #e5e7eb;border-radius:0.25rem;padding:0.1rem 0.4rem;white-space:nowrap;';

        $html = '<li class="xm-check-item xm-check-item--' . $status . '" style="' . $liStyle . '">';
        $html .= '<span class="xm-check__icon" aria-hidden="true" style="' . $iconStyle . '">' . $icon . '</span>';
        // Value is placed inline inside the label — keeps eyes on the left,
        // avoids the "value is far away on the right edge" problem on wide screens.
        $html .= '<span class="xm-check__label" style="' . $labelStyle . '">';
        $html .= $label;
        if ($value !== '') {
            $html .= ' <code class="xm-check__value" style="' . $valueStyle . '">' . $value . '</code>';
        }
        $html .= '</span>';
        if ($action !== '') {
            $html .= $action;
        }
        $html .= '</li>';

        return $html;
    }
}
