<?php

/*
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\JoliNotif\tests\Driver;

use Joli\JoliNotif\Driver\BinaryProviderInterface;

/**
 * Classes using this trait should extend AbstractDriverTestCase.
 */
trait BinaryProviderTestTrait
{
    public function testRootDirectoryExists()
    {
        /** @var BinaryProviderInterface $driver */
        $driver = $this->getDriver();

        $this->assertDirectoryExists($driver->getRootDir());
    }

    public function testEmbeddedBinaryExists()
    {
        /** @var BinaryProviderInterface $driver */
        $driver = $this->getDriver();

        $this->assertFileExists($driver->getRootDir() . \DIRECTORY_SEPARATOR . $driver->getEmbeddedBinary());
    }

    public function testExtraFilesExist()
    {
        /** @var BinaryProviderInterface $driver */
        $driver = $this->getDriver();

        if (!$driver->getExtraFiles()) {
            // Nothing to test here
            $this->addToAssertionCount(1);

            return;
        }

        foreach ($driver->getExtraFiles() as $file) {
            $this->assertFileExists($driver->getRootDir() . \DIRECTORY_SEPARATOR . $file);
        }
    }
}
