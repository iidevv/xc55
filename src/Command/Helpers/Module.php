<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Command\Helpers;

use XCart\Domain\ModuleManagerDomain;

class Module
{
    public const NOT_FOUND     = 'not_found';
    public const NOT_INSTALLED = 'not_installed';
    public const INSTALLED     = 'installed';
    public const ENABLED       = 'enabled';

    /**
     * @param $name
     *
     * @return string
     * @throws \Exception
     */
    public function getModuleStateByName($name)
    {
        if (substr_count($name, '\\') + 1 !== 2) {
            throw new \Exception("Module name $name has wrong format. Should be Author\\\\Name");
        }

        [$author, $name] = explode('\\', $name);
        $moduleId = $author . '-' . $name;
        $module = $this->getModuleManagerDomain()->getModule($moduleId);

        $result = static::NOT_FOUND;
        // @TODO ECOM-2456
        if ($module && !$this->getModuleManagerDomain()->isInstalled($moduleId)) {
            $result = static::NOT_INSTALLED;
        } elseif ($module) {
            $result = $module['isEnabled'] ? static::ENABLED : static::INSTALLED;
        }

        return $result;
    }

    /**
     * @return ModuleManagerDomain
     */
    protected function getModuleManagerDomain()
    {
        return \XCart\Container::getContainer()->get(ModuleManagerDomain::class);
    }
}
