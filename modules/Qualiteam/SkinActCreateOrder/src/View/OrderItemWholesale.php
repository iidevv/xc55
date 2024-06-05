<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCreateOrder\View;

use XCart\Extender\Mapping\ListChild;
use XLite\Core\Auth;
use XLite\Core\Database;

/**
 * @ListChild (list="itemsList.orderitem.cell.price", weight="100", zone="admin")
 */
class OrderItemWholesale extends \XLite\View\AView
{

    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActCreateOrder/order_item_wholesale.twig';
    }

    protected function getCart()
    {
        if ($this->cart) {
            return $this->cart;
        }

        return $this->entity->getOrder();
    }

    protected function getProduct()
    {
        if ($this->product) {
            return $this->product;
        }
        return $this->entity->getProduct();
    }

    protected function hasWholesalePrices()
    {
        if ($this->entity) {
            $product = $this->entity->getProduct();
        } else {
            $product = $this->product;
        }

        if (!$product || $product->getProductId() === null) {
            return false;
        }

        $prices =  Database::getRepo('CDev\Wholesale\Model\WholesalePrice')->getWholesalePrices(
            $product,
            $this->getCart()->getProfile()
                ? $this->getCart()->getProfile()->getMembership()
                : Auth::getInstance()->getMembership()
        );

        return !empty($prices) && $this->getCart()->getManuallyCreated();
    }

    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/Qualiteam/SkinActCreateOrder/OrderItemWholesale.js';
        return $list;
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/Qualiteam/SkinActCreateOrder/wholesale/product_price/style.css';

        return $list;
    }
}