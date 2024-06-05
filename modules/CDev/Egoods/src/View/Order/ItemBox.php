<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Egoods\View\Order;

use XCart\Extender\Mapping\ListChild;

/**
 * Order item box
 *
 * @ListChild (list="invoice.item.name", zone="customer")
 * @ListChild (list="invoice.item.name", interface="mail", zone="commonr")
 */
class ItemBox extends \XLite\View\AView
{
    /**
     * Widget param names
     */
    public const PARAM_ITEM = 'item';

   /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_ITEM => new \XLite\Model\WidgetParam\TypeObject('Order item', null, false, 'XLite\Model\OrderItem'),
        ];
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getAttachments();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/Egoods/invoice_egoods.twig';
    }

    /**
     * @inheritdoc
     */
    protected function getCommonFiles()
    {
        return array_merge_recursive(parent::getCommonFiles(), [
            static::RESOURCE_CSS => ['css/files.css']
        ]);
    }

    /**
     * Get attachments
     *
     * @return array
     */
    protected function getAttachments()
    {
        return $this->getItem()
            ? $this->getItem()->getDownloadAttachments()
            : [];
    }

    /**
     * Get order item
     *
     * @return \XLite\Model\OrderItem
     */
    protected function getItem()
    {
        return $this->getParam(static::PARAM_ITEM);
    }
}
