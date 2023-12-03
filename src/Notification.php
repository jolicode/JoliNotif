<?php

/*
 * This file is part of the JoliNotif project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\JoliNotif;

use Joli\JoliNotif\Util\PharExtractor;
use JoliCode\PhpOsHelper\OsHelper;

class Notification
{
    private ?string $title = null;
    private ?string $body = null;
    private ?string $icon = null;
    private array $options = [];

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): self
    {
        // This makes the icon accessible for native commands when it's embedded inside a phar
        if (PharExtractor::isLocatedInsideAPhar($icon)) {
            $icon = PharExtractor::extractFile($icon);
        } else {
            // Makes the icon path absolute (expanding all symbolic links and resolving references like "/../")
            $icon = OsHelper::isWindowsSubsystemForLinux()
                ? preg_replace('/^\/mnt\/([a-z])\//', '$1:\\', $icon, 1)
                : realpath($icon);
        }

        $this->icon = $icon;

        return $this;
    }

    public function getOption(string $key): ?string
    {
        return $this->options[$key] ?? null;
    }

    public function addOption(string $key, string $option): self
    {
        $this->options[$key] = $option;

        return $this;
    }
}
