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

/**
 * @internal
 */
class PharExtractor
{
    /**
     * Return whether the file path is located inside a phar.
     */
    public static function isLocatedInsideAPhar(string $filePath): bool
    {
        return str_starts_with($filePath, 'phar://');
    }

    /**
     * Extract the file from the phar archive to make it accessible for native commands.
     *
     * The absolute file path to extract should be passed in the first argument.
     */
    public static function extractFile(string $filePath, bool $overwrite = false): string
    {
        $pharPath = \Phar::running(false);

        if (!$pharPath) {
            return '';
        }

        $pharPosition = strpos($filePath, $pharPath);

        if (false === $pharPosition) {
            // The file belongs to another phar than the running one
            return '';
        }

        $relativeFilePath = substr($filePath, $pharPosition + \strlen($pharPath) + 1);
        $extractionDir = self::getExtractionDirectory($pharPath);
        $extractedFilePath = $extractionDir . '/' . $relativeFilePath;
        $lock = fopen($extractionDir . '/.lock', 'c');

        if (false === $lock) {
            throw new \RuntimeException('Unable to open the PHAR extraction lock.');
        }

        try {
            if (!flock($lock, \LOCK_EX)) {
                throw new \RuntimeException('Unable to lock the PHAR extraction directory.');
            }

            // Check after acquiring the lock: another process may have extracted the file.
            clearstatcache(true, $extractedFilePath);

            if (!file_exists($extractedFilePath) || $overwrite) {
                $phar = new \Phar($pharPath);
                $phar->extractTo($extractionDir, $relativeFilePath, $overwrite);
            }
        } finally {
            fclose($lock);
        }

        return $extractedFilePath;
    }

    private static function getExtractionDirectory(string $pharPath): string
    {
        $cacheDir = self::getCacheDirectory();
        self::createPrivateDirectory($cacheDir);

        $hash = hash_file('sha256', $pharPath);

        if (false === $hash) {
            throw new \RuntimeException('Unable to hash the running PHAR.');
        }

        $extractionDir = $cacheDir . '/' . $hash;
        self::createPrivateDirectory($extractionDir);

        return $extractionDir;
    }

    private static function getCacheDirectory(): string
    {
        if ('Windows' === \PHP_OS_FAMILY) {
            $baseDir = getenv('LOCALAPPDATA');

            if (!$baseDir) {
                throw new \RuntimeException('Unable to locate the local application data directory.');
            }

            $baseDir = str_replace('\\', '/', $baseDir);

            // The per-user directory supplies the inherited Windows ACLs.
            return rtrim($baseDir, '/') . '/JoliNotif';
        }

        $homeDir = getenv('HOME');

        if (!$homeDir || !str_starts_with($homeDir, '/') || !is_dir($homeDir)) {
            throw new \RuntimeException('Unable to locate an existing absolute home directory.');
        }

        return rtrim($homeDir, '/') . '/.jolinotif';
    }

    private static function createPrivateDirectory(string $directory): void
    {
        // A concurrent creator is fine; validate the resulting directory in either case.
        @mkdir($directory, 0o700, true);
        clearstatcache(true, $directory);
        $stat = @lstat($directory);

        if (false === $stat || ($stat['mode'] & 0o170000) !== 0o040000) {
            throw new \RuntimeException(\sprintf('The cache path "%s" is not a real directory.', $directory));
        }

        // Windows uses inherited profile ACLs, not POSIX ownership or permission bits.
        if ('Windows' === \PHP_OS_FAMILY) {
            return;
        }

        if (($stat['mode'] & 0o777) !== 0o700 || $stat['uid'] !== self::getCurrentUserId()) {
            throw new \RuntimeException(\sprintf('The cache directory "%s" must be owned by the current user with permissions 0700.', $directory));
        }
    }

    private static function getCurrentUserId(): int
    {
        if (\function_exists('posix_geteuid')) {
            return posix_geteuid();
        }

        // POSIX is optional. A newly created file also identifies the effective owner.
        $file = tmpfile();

        if (false === $file) {
            throw new \RuntimeException('Unable to determine the current user ID.');
        }

        $stat = fstat($file);
        fclose($file);

        if (false === $stat) {
            throw new \RuntimeException('Unable to determine the current user ID.');
        }

        return $stat['uid'];
    }
}
