<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OneClickUpsellAfterCheckout\View;

use XCart\Extender\Mapping\ListChild;
use XLite\Core\Config;

/**
 * Popup with related products widget runner
 *
 * @ListChild (list="checkout.success", zone="customer", weight="0")
 */
class PopupRunner extends \XLite\View\AView
{
    /**
     * @inheritdoc
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/OneClickUpsellAfterCheckout/list.twig';
    }

    /**
     * @inheritdoc
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/QSL/OneClickUpsellAfterCheckout/list.js';

        return $list;
    }

    /**
     * @inheritdoc
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/QSL/OneClickUpsellAfterCheckout/list.css';

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->hasProducts();
    }

    /**
     * Has related products or not
     *
     * @return boolean
     */
    protected function hasProducts()
    {
        if ($this->getOrderNumber()) {
            $upsellingProducts = $this->getUpsellingProducts();

            foreach ($upsellingProducts as $upsellingProduct) {
                /** @var \XC\Upselling\Model\UpsellingProduct $upsellingProduct */
                if ($this->isProductAvailable($upsellingProduct->getProduct())) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return array
     */
    protected function getUpsellingProducts()
    {
        return \XLite\Core\Database::getRepo('XC\Upselling\Model\UpsellingProduct')->search(
            new \XLite\Core\CommonCell(
                [
                    \XC\Upselling\Model\Repo\UpsellingProduct::SEARCH_ORDER_NUMBER => $this->getOrderNumber(),
                ]
            )
        );
    }

    protected function isProductAvailable(\XLite\Model\Product $product)
    {
        return $product->availableInDate()
            && (
                Config::getInstance()->General->show_out_of_stock_products === 'everywhere'
                || !$product->isOutOfStock()
            );
    }

    /**
     * @return string
     */
    protected function getOrderNumber()
    {
        return $this->getOrder()->getOrderNumber();
    }

    /**
     * @return mixed
     */
    protected function getOrder()
    {
        return \XLite::getController()->getOrder();
    }

    /**
     * @return string
     */
    protected function isRedirect()
    {
        return \XLite\Core\Config::getInstance()->QSL->OneClickUpsellAfterCheckout->redirect_to_checkout ? '1' : '0';
    }
}
