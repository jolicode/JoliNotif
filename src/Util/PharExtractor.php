<?php

/**
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\JoliNotif\Util;

class PharExtractor
{
    /**
     * Return whether the file path is located inside a phar.
     *
     * @param string $filePath
     *
     * @return bool
     */
    public static function isLocatedInsideAPhar($filePath)
    {
        return 0 === strpos($filePath, 'phar://');
    }

    /**
     * Extract the file from the phar archive to make it accessible for native commands.
     *
     * @param string $filePath  the absolute file path to extract
     * @param bool   $overwrite
     *
     * @return string
     */
    public static function extractFile($filePath, $overwrite = false)
    {
        $pharPath = \Phar::running(false);

        if (empty($pharPath)) {
            return '';
        }

        $relativeFilePath  = substr($filePath, strpos($filePath, $pharPath) + strlen($pharPath) + 1);
        $tmpDir            = sys_get_temp_dir().'/jolinotif';
        $extractedFilePath = $tmpDir.'/'.$relativeFilePath;

        if (!file_exists($extractedFilePath) || $overwrite) {
            $phar = new \Phar($pharPath);
            $phar->extractTo($tmpDir, $relativeFilePath, $overwrite);
        }

        return $extractedFilePath;
    }
}
