<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

use Symfony\Component\HttpFoundation\Session\SessionBagInterface;

/**
 * Session dump
 */
class SessionDump implements \Symfony\Component\HttpFoundation\Session\SessionInterface
{
   /**
     * @var array
     */
    protected $data = [];

    public function has($name)
    {
        return isset($this->data[$name]);
    }

    public function get($name, $default = null)
    {
        return $this->data[$name] ?? $default;
    }

    public function set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function remove($name)
    {
        if (isset($this->data[$name])) {
            unset($this->data[$name]);
        }
    }

    public function all()
    {
        return $this->data;
    }

    public function clear()
    {
        $this->data = [];
    }

    public function start()
    {
        return true;
    }

    public function getId()
    {
        return '';
    }

    public function setId($id)
    {
    }

    public function getName()
    {
        return '';
    }

    public function setName($name)
    {
    }

    public function invalidate($lifetime = null)
    {
        return false;
    }

    public function migrate($destroy = false, $lifetime = null)
    {
        return false;
    }

    public function save()
    {
    }

    public function replace(array $attributes)
    {
    }

    public function isStarted()
    {
        return true;
    }

    public function registerBag(SessionBagInterface $bag)
    {
    }

    public function getBag($name)
    {
    }

    public function getMetadataBag()
    {
    }
}
