<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model\Shipping\Popup;

use XLite\View\ItemsList\Model\Shipping\ShippingSolutionsTrait;

/**
 * Shipping solutions list in the popup
 */
class ShippingSolutions extends \XLite\View\ItemsList\Model\Shipping\Popup\Carriers
{
    use ShippingSolutionsTrait;

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        $result->{\XLite\Model\Repo\Shipping\Method::P_PROCESSOR} = 'shipping_solution';

        return $result;
    }

    /**
     * Return dir which contains the page body template
     *
     * @return string
     */
    protected function getPageBodyDir()
    {
        return 'shipping/popup_methods/shipping_solutions';
    }

    /**
     * @return string
     */
    public function getListCSSClasses()
    {
        return parent::getListCSSClasses() . ' shipping_solutions';
    }

    /**
     * @param \XLite\Model\Shipping\Method $method Shipping method
     *
     * @return string
     */
    protected function getInstallButtonUrl(\XLite\Model\Shipping\Method $method)
    {
        return \XLite::getInstance()->getAppStoreUrl();
    }

    protected function isMethodModuleInstalled(\XLite\Model\Shipping\Method $method): bool
    {
        return $this->getModuleManagerDomain()->isInstalled($method->getProcessorModule());
    }
}
