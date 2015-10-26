<?php

/**
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Joli\JoliNotif\tests\Notifier;

/**
 * Classes using this trait should extend NotifierTestCase.
 */
trait BinaryProviderTestTrait
{
    public function testRootDirectoryExists()
    {
        $notifier = $this->getNotifier();

        $this->assertTrue(is_dir($notifier->getRootDir()));
    }

    public function testEmbeddedBinaryExists()
    {
        $notifier = $this->getNotifier();

        $this->assertTrue(file_exists($notifier->getRootDir().DIRECTORY_SEPARATOR.$notifier->getEmbeddedBinary()));
    }

    public function testExtraFilesExist()
    {
        $notifier = $this->getNotifier();

        foreach ($notifier->getExtraFiles() as $file) {
            $this->assertTrue(file_exists($notifier->getRootDir().DIRECTORY_SEPARATOR.$file));
        }
    }
}
