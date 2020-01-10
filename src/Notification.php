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

use Joli\JoliNotif\Util\OsHelper;
use Joli\JoliNotif\Util\PharExtractor;

class Notification
{
    /**
     * @var string|null
     */
    private $title;

    /**
     * @var string|null
     */
    private $body;

    /**
     * @var string|null
     */
    private $icon;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getBody()
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getIcon()
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

    /**
     * @return string|null
     */
    public function getOption(string $key)
    {
        if (!\array_key_exists($key, $this->options)) {
            return null;
        }

        return $this->options[$key];
    }

    public function addOption(string $key, string $option): self
    {
        $this->options[$key] = $option;

        return $this;
    }
}
