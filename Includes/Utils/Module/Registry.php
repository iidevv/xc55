<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Utils\Module;

use Includes\Utils\Converter;
use Includes\Utils\FileManager;
use Symfony\Component\Yaml\Parser;
use XCart\Domain\ModuleManagerDomain;

class Registry
{
    /**
     * @var array
     */
    private $runtimeCache;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var ModuleManagerDomain
     */
    private $moduleManagerDomain;

    public function __construct()
    {
        $this->parser = new Parser();

        $this->moduleManagerDomain = \XCart\Container::getContainer()->get('XCart\Domain\ModuleManagerDomain');
    }

    /**
     * @return array
     */
    public function getModules()
    {
        return $this->executeCachedRuntime(function () {
            $modules = $this->moduleManagerDomain->getAllModules();

            ksort($modules, SORT_NATURAL);

            return $modules;
        });
    }

    /**
     * @return array
     */
    public function getSkinModules()
    {
        return $this->executeCachedRuntime(function () {
            return array_filter($this->getModules(), static function ($module) {
                return $module['type'] === 'skin';
            });
        });
    }

    /**
     * @return array
     */
    public function getEnabledPaymentModuleIds()
    {
        return $this->executeCachedRuntime(function () {
            $result = [];

            foreach ($this->getModules() as $moduleId => $module) {
                if (
                    $module['type'] === 'payment'
                    && $module['isEnabled']
                ) {
                    $result[] = $moduleId;
                }
            }

            return $result;
        });
    }

    /**
     * @return array
     */
    public function getEnabledShippingModuleIds()
    {
        return $this->executeCachedRuntime(function () {
            $result = [];

            foreach ($this->getModules() as $moduleId => $module) {
                if (
                    $module['type'] === 'shipping'
                    && $module['isEnabled']
                ) {
                    $result[] = $moduleId;
                }
            }

            return $result;
        });
    }

    public function getEnabledModuleIds(bool $skipMainClassCheck = false): array
    {
        return $this->executeCachedRuntime(function () use ($skipMainClassCheck) {
            $enabledModuleIds = $this->moduleManagerDomain->getEnabledModuleIds();

            if ($skipMainClassCheck) {
                $result = $enabledModuleIds;
            } else {
                $result = array_filter($enabledModuleIds, static function ($moduleId) {
                    return class_exists(Module::getMainClassName($moduleId))
                        || FileManager::isFileReadable(Module::getMainDataFilePath($moduleId));
                });
            }

            sort($result, SORT_NATURAL);

            return $result;
        }, ['skipMainClassCheck' => $skipMainClassCheck]);
    }

    /**
     * @param string $author
     * @param string $name
     *
     * @return Module
     * @deprecated Use \XCart\Domain\ModuleManagerDomain::getModule
     */
    public function getModule($author, $name = null)
    {
        return $this->moduleManagerDomain->getModule(
            Module::buildId($author, $name)
        );
    }

    /**
     * @param string      $author
     * @param string|null $name
     *
     * @return array
     */
    public function getDependencies($author, $name = null)
    {
        return $this->getModule($author, $name) ? $this->getModule($author, $name)->dependsOn : [];
    }

    /**
     * @param string      $author
     * @param string|null $name
     *
     * @return bool
     * @deprecated Use \XCart\Domain\ModuleManagerDomain::isEnabled
     */
    public function isModuleEnabled(string $author, ?string $name = null): bool
    {
        [$author, $name] = Module::explodeModuleId($author, $name);

        return $this->moduleManagerDomain->isEnabled("{$author}-{$name}");
    }

    /**
     * @param string $path
     * @param array  $params
     *
     * @return string
     */
    public function getServiceURL($path, array $params = [])
    {
        return \XLite::getInstance()->getServiceURL(
            '#/'
            . $path
            . ($params ? ('?' . http_build_query($params)) : '')
        );
    }

    /**
     * @param string      $author
     * @param string|null $name
     *
     * @return string
     */
    public function getModuleServiceURL($author, $name = null)
    {
        [$author, $name] = Module::explodeModuleId($author, $name);

        $module = $this->getModule($author, $name);

        if ($module) {
            return \XLite::getInstance()->getServiceURL('#/installed-addons', null, ['moduleId' => $module['id']]);
        }

        return \XLite::getInstance()->getAppStoreUrl() . "xsku-{$author}-{$name}";
    }

    /**
     * @param string      $author
     * @param string|null $name
     *
     * @return string
     */
    public function getModuleSettingsUrl($author, $name = null)
    {
        $module = $this->getModule($author, $name);

        return $module
            ? Converter::buildURL('module', '', ['moduleId' => $module['id']])
            : null;
    }

    /**
     * @param string      $author
     * @param string|null $name
     *
     * @return string[]
     */
    public function getYamlFiles($author, $name = null)
    {
        $sourcePath = Module::getSourcePath($author, $name);

        $result = [
            $sourcePath . 'install.yaml',
        ];

        foreach (glob($sourcePath . 'install_*.yaml') ?: [] as $translationFile) {
            $result[] = $translationFile;
        }

        return $result;
    }

    /** Port of runtime cache trait */

    /**
     * @param callable $callback
     * @param null     $cacheKeyParts
     * @param bool     $force
     *
     * @return mixed
     */
    protected function executeCachedRuntime(callable $callback, $cacheKeyParts = null, $force = false)
    {
        if ($cacheKeyParts === null) {
            $cacheKeyParts = debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'];
        }

        $cacheKey = $this->getRuntimeCacheKey([$cacheKeyParts]);

        if (!isset($this->runtimeCache[$cacheKey]) || $force) {
            $this->runtimeCache[$cacheKey] = $callback();
        }

        return $this->runtimeCache[$cacheKey];
    }

    /**
     * Calculate key for cache storage
     *
     * @param mixed $cacheKeyParts
     *
     * @return string
     */
    protected function getRuntimeCacheKey($cacheKeyParts)
    {
        return is_scalar($cacheKeyParts) ? (string) $cacheKeyParts : md5(serialize($cacheKeyParts));
    }
}
