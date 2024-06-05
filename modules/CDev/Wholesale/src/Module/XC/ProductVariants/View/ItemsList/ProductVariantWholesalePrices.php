<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\Module\XC\ProductVariants\View\ItemsList;

use CDev\Wholesale\Model\Base\AWholesalePrice;
use CDev\Wholesale\Module\XC\ProductVariants\Model\ProductVariantWholesalePrice;
use CDev\Wholesale\Module\XC\ProductVariants\Model\Repo\ProductVariantWholesalePrice as ProductVariantWholesalePriceRepo;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;
use XLite\Core\Request;
use XLite\Model\Repo\ARepo;

/**
 * Wholesale prices items list (product variant)
 *
 * @Extender\Depend("XC\ProductVariants")
 */
class ProductVariantWholesalePrices extends \CDev\Wholesale\View\ItemsList\WholesalePrices
{
    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'CDev\Wholesale\Module\XC\ProductVariants\Model\ProductVariantWholesalePrice';
    }

    /**
     * createEntity
     *
     * @return \XC\ProductVariants\Model\ProductVariant
     */
    protected function createEntity()
    {
        $entity = parent::createEntity();

        $entity->setProductVariant($this->getProductVariant());

        return $entity;
    }

    // {{{ Data

    /**
     * Return wholesale prices
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $cnd->{ProductVariantWholesalePriceRepo::P_PRODUCT_VARIANT} = $this->getProductVariant();
        $cnd->{ProductVariantWholesalePriceRepo::P_ORDER_BY_MEMBERSHIP} = true;
        $cnd->{ARepo::P_ORDER_BY} = ['w.quantityRangeBegin', 'ASC'];

        $result = Database::getRepo(ProductVariantWholesalePrice::class)
            ->search($cnd, $countOnly);

        return $result;
    }

    /**
     * Return default price
     *
     * @return mixed
     */
    protected function getDefaultPriceValue()
    {
        return $this->getProductVariant()->getClearPrice();
    }

    // }}}

    /**
     * Get URL common parameters
     *
     * @return array
     */
    protected function getCommonParams()
    {
        $this->commonParams = parent::getCommonParams();
        $this->commonParams['id'] = Request::getInstance()->id;

        return $this->commonParams;
    }

    /**
     * Get tier by quantity and membership
     *
     * @param AWholesalePrice $entity
     *
     * @return AWholesalePrice
     */
    protected function getTierByWholesaleEntity($entity)
    {
        return $entity->getRepository()->findOneBy([
            'quantityRangeBegin' => $entity->getQuantityRangeBegin(),
            'membership'         => $entity->getMembership(),
            'productVariant'     => $this->getProductVariant(),
        ]);
    }
}
