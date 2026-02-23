<?php

declare(strict_types=1);

namespace XoopsModules\Subscriptions;

use RuntimeException;

use function extension_loaded;
use function function_exists;
use function in_array;
use function sprintf;

/**
 * Cloner — recursively copies this module to a new directory,
 * replacing all occurrences of the source dirname with the target dirname.
 *
 * Usage (in admin/clone.php):
 *   Cloner::cloneFileFolder($helper->path('/'));
 *
 * @copyright XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 */
class Cloner
{
    /**
     * Recursively clone a directory tree, renaming all files and replacing
     * all text content that matches the source dirname with the target dirname.
     *
     * Binary files (images, fonts, archives) are copied without content replacement.
     *
     * Relies on $patKeys / $patValues being set in the calling scope as globals.
     */
    public static function cloneFileFolder(string $path): void
    {
        global $patKeys, $patValues;

        $newPath = str_replace($patKeys[0], $patValues[0], $path);

        if (is_dir($path)) {
            if (! mkdir($newPath) && ! is_dir($newPath)) {
                throw new RuntimeException(sprintf('Directory "%s" could not be created', $newPath));
            }
            $handle = opendir($path);
            if ($handle) {
                while (false !== ($file = readdir($handle))) {
                    if (str_starts_with($file, '.')) {
                        continue;
                    }
                    self::cloneFileFolder("{$path}/{$file}");
                }
                closedir($handle);
            }
        } else {
            $binaryExtensions = ['jpeg', 'jpg', 'gif', 'png', 'zip', 'ttf', 'woff', 'woff2'];
            if (in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), $binaryExtensions, true)) {
                copy($path, $newPath);
            } else {
                $content = file_get_contents($path);
                $content = str_replace($patKeys, $patValues, $content);
                file_put_contents($newPath, $content);
            }
        }
    }

    /**
     * Attempt to generate a new logo PNG for the cloned module by writing
     * the new dirname text onto the base logo image.
     *
     * Returns false (silently) when the GD extension or required assets are absent.
     */
    public static function createLogo(string $dirname): bool
    {
        if (! extension_loaded('gd')) {
            return false;
        }

        $required = [
            'imagecreatefrompng', 'imagecolorallocate', 'imagefilledrectangle',
            'imagepng', 'imagefttext', 'imagealphablending', 'imagesavealpha',
        ];
        foreach ($required as $fn) {
            if (! function_exists($fn)) {
                return false;
            }
        }

        $logoPath = $GLOBALS['xoops']->path('modules/' . $dirname . '/assets/images/logoModule.png');
        $fontPath = $GLOBALS['xoops']->path('modules/' . $dirname . '/assets/images/VeraBd.ttf');
        if (! file_exists($logoPath) || ! file_exists($fontPath)) {
            return false;
        }

        $image = imagecreatefrompng($logoPath);
        imagealphablending($image, false);
        imagesavealpha($image, true);

        // Erase the old module name text area
        $grey = imagecolorallocate($image, 237, 237, 237);
        imagefilledrectangle($image, 5, 35, 85, 46, $grey);

        // Write the new dirname centred in the same area
        $black = imagecolorallocate($image, 0, 0, 0);
        $offset = (int) ((80 - mb_strlen($dirname) * 6.5) / 2);
        imagefttext($image, 8.5, 0, $offset, 45, $black, $fontPath, ucfirst($dirname), []);

        imagepng($image, $logoPath);

        return true;
    }
}
