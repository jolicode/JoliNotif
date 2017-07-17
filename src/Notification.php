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

class Notification
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $body;

    /**
     * @var string
     */
    private $icon;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     *
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     *
     * @return $this
     */
    public function setIcon($icon)
    {
        // This makes the icon accessible for native commands when it's embedded inside a phar
        if (PharExtractor::isLocatedInsideAPhar($icon)) {
            $icon = PharExtractor::extractFile($icon);
        } else {
            // Makes the icon path absolute (expanding all symbolic links and resolving references like "/../")
            $icon = realpath($icon);
        }

        $this->icon = $icon;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function getOption($key)
    {
        if (!array_key_exists($key, $this->options)) {
            return null;
        }

        return $this->options[$key];
    }

    /**
     * @param string $key
     * @param string $option
     *
     * @return $this
     */
    public function addOption($key, $option)
    {
        $this->options[$key] = $option;

        return $this;
    }
}
