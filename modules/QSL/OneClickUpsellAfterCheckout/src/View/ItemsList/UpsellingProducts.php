<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OneClickUpsellAfterCheckout\View\ItemsList;

use XLite\Core\Config;

/**
 * Related products widget (customer area)
 */
class UpsellingProducts extends \XLite\View\ItemsList\Product\Customer\ACustomer
{
    public const WIDGET_TARGET = 'checkoutSuccess';
    public const PARAM_ORDER_NUMBER = 'order_number';

    /**
     * @inheritdoc
     */
    public static function getSearchParams()
    {
        return [
            \XC\Upselling\Model\Repo\UpsellingProduct::SEARCH_ORDER_NUMBER => static::PARAM_ORDER_NUMBER,
        ];
    }

    /**
     * @inheritdoc
     */
    protected static function getWidgetTarget()
    {
        return static::WIDGET_TARGET;
    }

    /**
     * @inheritdoc
     */
    protected function getHead()
    {
        return static::t('Anything else, maybe?');
    }

    /**
     * @inheritdoc
     */
    protected function getWidgetParameters()
    {
        $list = parent::getWidgetParameters();
        $list[static::PARAM_ORDER_NUMBER] = \XLite\Core\Request::getInstance()->order_number;

        return $list;
    }

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_ORDER_NUMBER => new \XLite\Model\WidgetParam\TypeString(
                'Order number',
                \XLite\Core\Request::getInstance()->order_number
            ),
        ];

        $this->widgetParams[self::PARAM_WIDGET_TYPE]->setValue(self::WIDGET_TYPE_CENTER);
        $this->widgetParams[self::PARAM_DISPLAY_MODE]->setValue(self::DISPLAY_MODE_ROTATOR);
        $this->widgetParams[self::PARAM_GRID_COLUMNS]->setValue(3);

        $this->widgetParams[self::PARAM_SHOW_DISPLAY_MODE_SELECTOR]->setValue(false);
        $this->widgetParams[self::PARAM_SHOW_SORT_BY_SELECTOR]->setValue(false);
    }

    /**
     * @inheritdoc
     */
    protected function getPagerClass()
    {
        return 'XLite\View\Pager\Infinity';
    }

    /**
     * @inheritdoc
     */
    protected function defineRepositoryName()
    {
        return 'XC\Upselling\Model\UpsellingProduct';
    }

    /**
     * @inheritdoc
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $result = parent::getData($cnd, $countOnly);

        if (!$countOnly) {
            $productsCount = Config::getInstance()->QSL->OneClickUpsellAfterCheckout->products_count;

            $products = [];
            foreach ($result as $upsellingProduct) {
                /** @var \XC\Upselling\Model\UpsellingProduct $upsellingProduct */
                $product = $upsellingProduct->getProduct();
                if ($this->isProductAvailable($product)) {
                    $products[$product->getProductId()] = $product;

                    if (count($products) >= $productsCount) {
                        break;
                    }
                }
            }

            return $products;
        }

        return $result;
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
     * @inheritdoc
     */
    public function getListCSSClasses()
    {
        return parent::getListCSSClasses() . ' upselling-products one-click-upselling';
    }
}
