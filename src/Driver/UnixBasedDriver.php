<?php

/**
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JoliNotif\Driver;

use Symfony\Component\Process\ProcessBuilder;

abstract class UnixBasedDriver extends CliBasedDriver
{
    /**
     * {@inheritdoc}
     */
    public function isSupported()
    {
        if ('/' !== DIRECTORY_SEPARATOR) {
            return false;
        }

        // Do not use the which programm to check if a binary exists.
        // See also http://stackoverflow.com/questions/592620/check-if-a-program-exists-from-a-bash-script
        $builder = new ProcessBuilder([
            'command',
            '-v',
            $this->getBinary(),
            '>/dev/null',
            '2>&1',
        ]);
        $process = $builder->getProcess();
        $process->run();

        return $process->isSuccessful();
    }
}
