<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCreateOrder\View\Button;

/**
 * Added previous product in popup
 */
class PopupAddedPreviouslyProduct extends \XLite\View\Button\APopupLink
{
    const PARAM_REDIRECT_URL = 'redirect_url';

    /**
     * getJSFiles
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/Qualiteam/SkinActCreateOrder/added_previously_product.js';

        return $list;
    }

    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {

        return [
            'target'        => $this->getSelectorTarget(),
            'widget'        => $this->getSelectorViewClass(),
            'order_number'  => $this->getOrder()->getOrderNumber(),
            'productIds'    => $this->getProductsInOrder(),
            'profileId'     => $this->getOrder()->getOrigProfileId() ?? 0,
            'substring'     => '',
        ];
    }

    protected function getProductsInOrder()
    {
        $list = [];

        foreach ($this->getOrder()->getItems() as $item) {
            $list[] = $item->getProductId();
        }

        return $list;
    }

    /**
     * Defines the target of the product selector
     * The main reason is to get the title for the selector from the controller
     *
     * @return string
     */
    protected function getSelectorTarget()
    {
        return 'added_previously_product';
    }

    /**
     * Defines the class name of the widget which will display the product list dialog
     *
     * @return string
     */
    protected function getSelectorViewClass()
    {
        return '\Qualiteam\SkinActCreateOrder\View\AddedPreviouslyProduct';
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_REDIRECT_URL => new \XLite\Model\WidgetParam\TypeString('URL to redirect to', ''),
        ];
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        return 'popup-added-previously-product';
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return static::t('SkinActCreateOrder Previously ordered products');
    }
}
