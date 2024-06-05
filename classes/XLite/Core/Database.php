<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

use XCart\Doctrine\FixtureLoader;
use XLite\InjectLoggerTrait;

/**
 * Database
 */
class Database extends \XLite\Base\Singleton
{
    use InjectLoggerTrait;

    /**
     * Doctrine entity manager
     *
     * @var \XLite\Core\Doctrine\ORM\EntityManager
     */
    protected static $em;

    /**
     * connected
     *
     * @var boolean
     */
    protected $connected;

    /**
     * @return FixtureLoader
     */
    protected function getFixtureLoader()
    {
        return \XCart\Container::getContainer()->get(FixtureLoader::class);
    }

    /**
     * Get entity manager
     *
     * @return \XLite\Core\Doctrine\ORM\EntityManager
     */
    public static function getEM()
    {
        if (static::$em === null) {
            \XLite\Core\Database::getInstance();
        }

        return static::$em;
    }

    /**
     * Get repository (short method)
     *
     * @param string $entity Entity class name
     *
     * @return \XLite\Model\Repo\ARepo
     */
    public static function getRepo($entity)
    {
        $entity = static::getEntityClass($entity);

        return class_exists($entity)
            ? static::getEM()->getRepository($entity)
            : null;
    }

    /**
     * Calculate the class name for entity
     *
     * @param string $entity Entity
     *
     * @return string
     */
    public static function getEntityClass($entity)
    {
        return ltrim($entity, '\\');
    }

    /**
     * Get cache driver
     *
     * @return \Doctrine\Common\Cache\CacheProvider
     * @deprecated use \XLite\Core\Cache::getDriver instead
     */
    public static function getCacheDriver()
    {
        return \XLite\Core\Cache::getInstance()->getDriver();
    }

    /**
     * Get cache driver
     *
     * @return \Doctrine\Common\Cache\CacheProvider
     * @deprecated use \XLite\Core\Cache::getDriver instead
     */
    public static function getFreshCacheDriver()
    {
        return \XLite\Core\Cache::getInstance()->getDriver();
    }

    /**
     * Prepare array for IN () DQL function
     *
     * @param array  $data   Hash array
     * @param string $prefix Placeholder prefix OPTIONAL
     *
     * @return array (keys for IN () function & parameters hash array)
     */
    public static function prepareArray(array $data, $prefix = 'arr')
    {
        $keys       = [];
        $parameters = [];

        foreach ($data as $k => $v) {
            $k              = $prefix . $k;
            $keys[]         = ':' . $k;
            $parameters[$k] = $v;
        }

        return [$keys, $parameters];
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        if (!$this->connected) {
            $this->startEntityManager();
        }
    }

    /**
     * Start Doctrine entity manager
     *
     * @return void
     */
    public function startEntityManager()
    {
        static::$em = \XCart\Container::getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * Load fixtures from YAML file
     *
     * @param string $path    YAML file path
     * @param array  $options Options         OPTIONAL
     *
     * @return void
     * @deprecated use \XCart\Doctrine\FixtureLoader::loadYaml instead
     */
    public function loadFixturesFromYaml($path, $options = null)
    {
        $allowedModels  = $options['allowedModels'] ?? [];
        $excludedModels = $options['excludedModels'] ?? [];

        $this->getFixtureLoader()->loadYaml($path, $allowedModels, $excludedModels);
    }

    /**
     * postPersist event handler
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $arg Event argument
     *
     * @return void
     */
    public function postPersist(\Doctrine\ORM\Event\LifecycleEventArgs $arg)
    {
        $entity = $arg->getEntity();

        if ($entity instanceof \XLite\Model\AEntity) {
            $entity->checkCache();
        }
    }

    /**
     * postUpdate event handler
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $arg Event argument
     *
     * @return void
     */
    public function postUpdate(\Doctrine\ORM\Event\LifecycleEventArgs $arg)
    {
        $entity = $arg->getEntity();

        if ($entity instanceof \XLite\Model\AEntity) {
            $entity->checkCache();
        }
    }

    /**
     * postRemove event handler
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $arg Event argument
     *
     * @return void
     */
    public function postRemove(\Doctrine\ORM\Event\LifecycleEventArgs $arg)
    {
        $entity = $arg->getEntity();

        if ($entity instanceof \XLite\Model\AEntity) {
            $entity->checkCache();
        }
    }
}
