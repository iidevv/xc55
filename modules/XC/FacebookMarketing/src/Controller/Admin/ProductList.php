<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FacebookMarketing\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Products list controller
 * @Extender\Mixin
 */
class ProductList extends \XLite\Controller\Admin\ProductList
{
    /**
     * Enable feed for products
     *
     * @return void
     */
    protected function doActionFacebookProductFeedEnable()
    {
        $select = \XLite\Core\Request::getInstance()->select;
        if ($select && is_array($select)) {
            $data = array_fill_keys(
                array_keys($this->getSelected()),
                ['facebookMarketingEnabled' => true]
            );

            \XLite\Core\Database::getRepo('\XLite\Model\Product')->updateInBatchById($data);
            \XLite\Core\TopMessage::addInfo(
                'Products information has been successfully updated'
            );
        } else {
            \XLite\Core\TopMessage::addWarning('Please select the products first');
        }
    }
    /**
     * Disable feed for products
     *
     * @return void
     */
    protected function doActionFacebookProductFeedDisable()
    {
        $select = \XLite\Core\Request::getInstance()->select;
        if ($select && is_array($select)) {
            $data = array_fill_keys(
                array_keys($this->getSelected()),
                ['facebookMarketingEnabled' => false]
            );

            \XLite\Core\Database::getRepo('\XLite\Model\Product')->updateInBatchById($data);
            \XLite\Core\TopMessage::addInfo(
                'Products information has been successfully updated'
            );
        } else {
            \XLite\Core\TopMessage::addWarning('Please select the products first');
        }
    }
}
