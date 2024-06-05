<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Selected product attributes widget (minicart)
 *
 * @ListChild (list="minicart.horizontal.item", weight="30")
 * @ListChild (list="minicart.vertical.item", weight="30")
 */
class MinicartAttributeValues extends \XLite\View\AView
{
    /**
     * Widget parameter names
     */

    public const PARAM_ITEM  = 'item';

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'mini_cart/horizontal/parts/item.attribute_values.twig';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_ITEM    => new \XLite\Model\WidgetParam\TypeObject('Item', null, false, '\XLite\Model\OrderItem'),
        ];
    }

    /**
     * Get order item
     *
     * @return \XLite\Model\OrderItem
     */
    protected function getItem()
    {
        return $this->getParam(self::PARAM_ITEM);
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getItem()->hasAttributeValues();
    }

    // {{{ Template methods

    /**
     * @param \XLite\Model\OrderItem $item Order item
     *
     * @return boolean
     */
    protected function needMoreAttributesLink(\XLite\Model\OrderItem $item)
    {
        return $item->getAttributeValuesCount() > $this->getMaxAttributesCount();
    }

    /**
     * Max attributes count to show in minicart
     *
     * @return integer
     */
    protected function getMaxAttributesCount()
    {
        return 10;
    }

    // }}}
}
