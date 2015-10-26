<?php

/*
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\JoliNotif\Notifier;

/**
 * Interface implemented by notifiers when they provide their own binaries in
 * case the executable is not natively supported.
 */
interface BinaryProvider
{
    /**
     * Return whether the embedded binary can be used on the current system.
     *
     * @return bool
     */
    public function canBeUsed();

    /**
     * Return the absolute path of the directory containing all the files.
     *
     * @return string
     */
    public function getRootDir();

    /**
     * Return the path of the embedded binary.
     *
     * The path should be relative to the directory pointed by getRootDir().
     *
     * @return array
     */
    public function getEmbeddedBinary();

    /**
     * Return an array of files that should be extracted when JoliNotif is
     * used inside a phar archive.
     *
     * All paths should be relative to the directory pointed by getRootDir().
     *
     * @return array
     */
    public function getExtraFiles();
}
