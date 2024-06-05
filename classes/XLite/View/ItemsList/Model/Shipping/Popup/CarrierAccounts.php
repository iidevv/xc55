<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model\Shipping\Popup;

use Includes\Utils\Module\Manager;
use XCart\Domain\ModuleManagerDomain;

/**
 * Carrier accounts list in the popup
 */
class CarrierAccounts extends \XLite\View\ItemsList\Model\Shipping\Popup\Carriers
{
    /**
     * @var ModuleManagerDomain|null
     */
    protected ?ModuleManagerDomain $moduleManagerDomain;

    public function __construct(array $params = [])
    {
        parent::__construct($params);

        $this->moduleManagerDomain = \XCart\Container::getContainer()->get(ModuleManagerDomain::class);
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        $result->{\XLite\Model\Repo\Shipping\Method::P_EXCL_PROCESSORS} = [
            'offline',
            'shipping_solution',
        ];

        return $result;
    }

    /**
     * @param \XLite\Model\Shipping\Method $method Shipping method
     *
     * @return boolean
     */
    protected function isShowAddButton(\XLite\Model\Shipping\Method $method)
    {
        return !$method->isAdded()
            && $this->moduleManagerDomain->isEnabled($method->getProcessorModule());
    }

    /**
     * @param \XLite\Model\Shipping\Method $method Shipping method
     *
     * @return boolean
     */
    protected function isShowInstallButton(\XLite\Model\Shipping\Method $method)
    {
        return !$this->moduleManagerDomain->isEnabled(
            $method->getProcessorModule()
        );
    }

    protected function isMethodModuleInstalled(\XLite\Model\Shipping\Method $method): bool
    {
        return $this->moduleManagerDomain->isInstalled($method->getProcessorModule());
    }

    protected function getModuleUrl(string $moduleId): string
    {
        return Manager::getRegistry()->getModuleServiceURL($moduleId);
    }

    /**
     * @param \XLite\Model\Shipping\Method $method Shipping method
     *
     * @return string
     */
    protected function getInstallButtonUrl(\XLite\Model\Shipping\Method $method)
    {
        $returnUrl = $this->buildFullURL(
            'shipping_methods',
            'add',
            ['id' => $method->getMethodId()]
        );

        return \XLite::getInstance()->getShopURL(
            'service.php?/installModule',
            null,
            [
                'returnUrl' => urlencode($returnUrl),
                'moduleId'  => $method->getProcessorModule(),
            ]
        );
    }
}
