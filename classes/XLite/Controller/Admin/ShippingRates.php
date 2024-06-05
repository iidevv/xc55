<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Shipping rates page controller
 */
class ShippingRates extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $method = $this->getModelForm()->getModelObject();

        return $method && $method->getMethodId()
            ? $method->getName()
            : static::t('Add New Shipping Method');
    }

    /**
     * Return class name for the controller main form
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'XLite\View\Model\Shipping\Offline';
    }

    /**
     * Do action update
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $this->getModelForm()->performAction('modify');

        $itemsList = new \XLite\View\ItemsList\Model\Shipping\Markups();
        $itemsList->processQuick();

        $this->setHardRedirect(true);
        $this->setReturnURL(
            $this->buildURL('shipping_methods')
        );

        \XLite\Core\Event::updateShippingMethods();
    }
}
