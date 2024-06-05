<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFreeGifts\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Invoice item attribute values
 *
 * @ListChild (list="invoice.item.name", weight="30", zone="admin")
 */
class InvoiceLabelGift extends \XLite\View\AView
{
    /**
     * Widget parameter names
     */
    public const PARAM_ITEM = 'item';


    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/Qualiteam/SkinActFreeGifts/label_gift.less';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActFreeGifts/label_gift.twig';
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
            self::PARAM_ITEM => new \XLite\Model\WidgetParam\TypeObject('Order item', null, false, '\\XLite\\\Model\\OrderItem'),
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
            && $this->getParam(self::PARAM_ITEM)->getFreeGift();
    }
}
