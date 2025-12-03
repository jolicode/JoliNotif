<?php

/*
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Castor\Attribute\AsRawTokens;
use Castor\Attribute\AsTask;

use function Castor\guard_min_version;
use function Castor\import;
use function Castor\mount;
use function Castor\run;

guard_min_version('1.0.0');

import(__DIR__ . '/tools/php-cs-fixer/castor.php');
import(__DIR__ . '/tools/phpstan/castor.php');

mount(__DIR__ . '/tools/phar');

#[AsTask(description: 'Install dependencies')]
function install(): void
{
    run(['composer', 'install']);
    qa\cs\install();
    qa\phpstan\install();
}

#[AsTask(description: 'Run PHPUnit', ignoreValidationErrors: true)]
function phpunit(#[AsRawTokens] array $rawTokens): void
{
    run(['vendor/bin/phpunit', ...$rawTokens]);
}
