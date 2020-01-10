<?php

/*
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
     */
    public static function isLocatedInsideAPhar(string $filePath): bool
    {
        return 0 === strpos($filePath, 'phar://');
    }

    /**
     * Extract the file from the phar archive to make it accessible for native commands.
     *
     * The absolute file path to extract should be passed in the first argument.
     */
    public static function extractFile(string $filePath, bool $overwrite = false): string
    {
        $pharPath = \Phar::running(false);

        if (empty($pharPath)) {
            return '';
        }

        $relativeFilePath = substr($filePath, strpos($filePath, $pharPath) + \strlen($pharPath) + 1);
        $tmpDir = sys_get_temp_dir().'/jolinotif';
        $extractedFilePath = $tmpDir.'/'.$relativeFilePath;

        if (!file_exists($extractedFilePath) || $overwrite) {
            $phar = new \Phar($pharPath);
            $phar->extractTo($tmpDir, $relativeFilePath, $overwrite);
        }

        return $extractedFilePath;
    }
}
