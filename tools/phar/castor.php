<?php

/*
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace phar;

use Castor\Attribute\AsTask;

use function Castor\run;

#[AsTask(description: 'Build phar')]
function build()
{
    run('vendor/bin/box compile -c box.json');
}

#[AsTask(description: 'install dependencies')]
function install(): void
{
    run(['composer', 'install']);
}

#[AsTask(description: 'update dependencies')]
function update(): void
{
    run(['composer', 'update']);
}
