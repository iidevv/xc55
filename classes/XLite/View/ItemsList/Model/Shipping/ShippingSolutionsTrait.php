<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model\Shipping;

use Includes\Utils\Module\Manager;
use XCart\Domain\ModuleManagerDomain;

trait ShippingSolutionsTrait
{
    /**
     * @param \XLite\Model\Shipping\Method $method
     *
     * @return string
     */
    protected function getName($method)
    {
        return $this->getModule($method)['moduleName'] ?? '';
    }

    /**
     * @param \XLite\Model\Shipping\Method $method
     *
     * @return string
     */
    protected function getModuleURL($method)
    {
        return Manager::getRegistry()->getModuleServiceURL(
            $method->getModuleName()
        );
    }

    /**
     * @param \XLite\Model\Shipping\Method $method
     *
     * @return string
     */
    protected function getDescription($method)
    {
        return $this->getModule($method)['description'] ?? '';
    }

    /**
     * @return ModuleManagerDomain
     */
    protected function getModuleManagerDomain()
    {
        return \XCart\Container::getContainer()->get(ModuleManagerDomain::class);
    }

    /**
     * @param \XLite\Model\Shipping\Method $method
     *
     * @return string
     */
    protected function isEnabled($method)
    {
        return $this->getModuleManagerDomain()->isEnabled(
            $method->getModuleName()
        );
    }

    /**
     * @param \XLite\Model\Shipping\Method $method
     *
     * @return array
     */
    protected function getModule($method)
    {
        return $this->getModuleManagerDomain()->getModule(
            $method->getModuleName()
        );
    }
}
