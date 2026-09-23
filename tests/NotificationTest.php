<?php

/*
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\JoliNotif\tests;

use Joli\JoliNotif\Notification;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

class NotificationTest extends TestCase
{
    public function testItExtractsIconFromPhar(): void
    {
        $key = bin2hex(random_bytes(8));
        $iconContent = $key;
        $rootPackage = \dirname(__DIR__);
        $iconRelativePath = 'Resources/notification/icon-' . $key . '.png';
        $testDir = sys_get_temp_dir() . '/jolinotif-notification-' . $key;
        $homeDir = $testDir . '/home';
        $pharPath = $testDir . '/notification-extract-icon-' . $key . '.phar';

        mkdir($homeDir, 0o700, true);

        $bootstrap = <<<'PHAR_BOOTSTRAP'
            <?php

            require __DIR__.'/vendor/autoload.php';

            $iconPath = '/{{ THE_ICON }}';
            $notification = new \Joli\JoliNotif\Notification();
            $notification->setBody('My notification');
            $notification->setIcon(__DIR__.$iconPath);

            echo $notification->getIcon();
            PHAR_BOOTSTRAP;

        $files = (new Finder())
            ->in("{$rootPackage}/src")
            ->in("{$rootPackage}/tests/fixtures")
            ->in("{$rootPackage}/vendor")
            ->files()
        ;

        try {
            $phar = new \Phar($pharPath);
            $phar->buildFromIterator($files->getIterator(), $rootPackage);
            $phar->addFromString('bootstrap.php', str_replace(
                '{{ THE_ICON }}',
                $iconRelativePath,
                $bootstrap
            ));
            $phar->addFromString($iconRelativePath, $iconContent);
            $phar->setStub($phar->createDefaultStub('bootstrap.php'));

            $this->assertFileExists($pharPath);

            $process = new Process(
                [\PHP_BINARY, $pharPath],
                $testDir,
                ['HOME' => $homeDir, 'LOCALAPPDATA' => $homeDir],
            );
            $process->mustRun();
            $extractedIconPath = $process->getOutput();

            $this->assertStringStartsWith(
                str_replace('\\', '/', $homeDir) . '/',
                str_replace('\\', '/', $extractedIconPath),
            );
            $this->assertFileExists($extractedIconPath);
            $this->assertSame($iconContent, file_get_contents($extractedIconPath));
        } finally {
            unset($phar);

            if (file_exists($pharPath)) {
                \Phar::unlinkArchive($pharPath);
            }

            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($testDir, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST,
            );

            foreach ($files as $file) {
                $this->assertInstanceOf(\SplFileInfo::class, $file);

                if ($file->isDir() && !$file->isLink()) {
                    rmdir($file->getPathname());
                } else {
                    unlink($file->getPathname());
                }
            }

            rmdir($testDir);
        }
    }

    public function testItResolvesRealPathToIcon(): void
    {
        $notification = new Notification();
        $notification->setIcon(__DIR__ . '/../tests/fixtures/image.gif');

        $this->assertSame(realpath(__DIR__ . '/fixtures/image.gif'), $notification->getIcon());
    }
}
