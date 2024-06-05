<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\View\ItemsList;

use Qualiteam\SkinActYotpoReviews\View\YotpoStarsProductItemList;
use XLite\Core\Database;
use XLite\Core\View\DynamicWidgetInterface;
use XLite\Model\Product;
use XLite\Model\WidgetParam\TypeInt;
use XLite\View\CacheableTrait;

class AverageRating extends YotpoStarsProductItemList implements DynamicWidgetInterface
{
    use CacheableTrait;

    /**
     * Widget parameters
     */
    public const PARAM_PRODUCT_ID = 'productId';

    /**
     * @var \XLite\Model\Product
     */
    protected $product;

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_PRODUCT_ID => new TypeInt('ProductId'),
        ];
    }

    /**
     * Get associated product's id.
     *
     * @return int
     */
    protected function getProductId()
    {
        return $this->getParam(self::PARAM_PRODUCT_ID);
    }

    /**
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        if (!isset($this->product)) {
            $this->product = Database::getRepo(Product::class)?->find($this->getProductId());

            $this->setWidgetParams([self::PARAM_PRODUCT => $this->product]);
        }

        return $this->product;
    }
}