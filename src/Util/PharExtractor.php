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

use Joli\JoliNotif\Exception\PharExtractionException;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @internal
 */
class PharExtractor
{
    private const STALE_AFTER = 30 * 24 * 3600;

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
        $lockPath = $extractionDir . '/.lock';
        $filesystem = new Filesystem();
        $lock = fopen($lockPath, 'c');

        if (false === $lock) {
            throw new PharExtractionException('Unable to open the PHAR extraction lock.');
        }

        try {
            if (!flock($lock, \LOCK_EX)) {
                throw new PharExtractionException('Unable to lock the PHAR extraction directory.');
            }

            // Mark the directory as still in use, so it does not get pruned.
            try {
                $filesystem->touch($lockPath);
            } catch (IOException) {
                // At worst, the directory will be extracted again after being pruned.
            }

            // Check after acquiring the lock: another process may have extracted the file.
            clearstatcache(true, $extractedFilePath);

            if (!$filesystem->exists($extractedFilePath) || $overwrite) {
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
        $extractionDir = $cacheDir . '/' . self::getPharHash($pharPath);

        if (!is_dir($extractionDir)) {
            self::pruneStaleDirectories($cacheDir);
        }

        self::createPrivateDirectory($extractionDir);

        return $extractionDir;
    }

    private static function getPharHash(string $pharPath): string
    {
        // The signature is already computed, no need to hash the whole archive again.
        return strtolower((new \Phar($pharPath))->getSignature()['hash']);
    }

    private static function getCacheDirectory(): string
    {
        $exception = null;

        foreach (self::getCacheDirectoryCandidates() as $directory) {
            try {
                self::createPrivateDirectory($directory);

                return $directory;
            } catch (PharExtractionException $exception) {
            }
        }

        throw $exception ?? new PharExtractionException('Unable to locate a cache directory.');
    }

    /**
     * @return list<string>
     */
    private static function getCacheDirectoryCandidates(): array
    {
        // The temporary directory is already private to the user on Windows.
        if ('Windows' === \PHP_OS_FAMILY) {
            return [sys_get_temp_dir() . '/jolinotif'];
        }

        $candidates = [];
        $cacheHome = getenv('XDG_CACHE_HOME');

        if (!$cacheHome || !str_starts_with($cacheHome, '/')) {
            $homeDir = getenv('HOME');
            $cacheHome = $homeDir && str_starts_with($homeDir, '/') && is_dir($homeDir) ? rtrim($homeDir, '/') . '/.cache' : null;
        }

        if ($cacheHome) {
            $candidates[] = rtrim($cacheHome, '/') . '/jolinotif';
        }

        // The home directory is not always available (cron, containers, system users, etc).
        $candidates[] = sys_get_temp_dir() . '/jolinotif-' . self::getCurrentUserId();

        return $candidates;
    }

    /**
     * Remove the directories left by other versions of the PHAR and not used for a while.
     */
    private static function pruneStaleDirectories(string $cacheDir): void
    {
        $limit = time() - self::STALE_AFTER;

        try {
            foreach (scandir($cacheDir) ?: [] as $name) {
                $directory = $cacheDir . '/' . $name;

                if ('.' === $name || '..' === $name || is_link($directory) || !is_dir($directory)) {
                    continue;
                }

                if (max((int) @filemtime($directory), (int) @filemtime($directory . '/.lock')) >= $limit) {
                    continue;
                }

                (new Filesystem())->remove($directory);
            }
        } catch (\Throwable) {
            // Pruning is only housekeeping, it should never prevent the extraction.
        }
    }

    private static function createPrivateDirectory(string $directory): void
    {
        // A concurrent creator is fine; validate the resulting directory in either case.
        try {
            (new Filesystem())->mkdir($directory, 0o700);
        } catch (IOException) {
        }

        clearstatcache(true, $directory);
        $stat = @lstat($directory);

        if (false === $stat || ($stat['mode'] & 0o170000) !== 0o040000) {
            throw new PharExtractionException(\sprintf('The cache path "%s" is not a real directory.', $directory));
        }

        // Windows uses inherited profile ACLs, not POSIX ownership or permission bits.
        if ('Windows' === \PHP_OS_FAMILY) {
            return;
        }

        if (($stat['mode'] & 0o777) !== 0o700 || $stat['uid'] !== self::getCurrentUserId()) {
            throw new PharExtractionException(\sprintf('The cache directory "%s" must be owned by the current user with permissions 0700.', $directory));
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
            throw new PharExtractionException('Unable to determine the current user ID.');
        }

        $stat = fstat($file);
        fclose($file);

        if (false === $stat) {
            throw new PharExtractionException('Unable to determine the current user ID.');
        }

        return $stat['uid'];
    }
}
