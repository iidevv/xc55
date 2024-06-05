<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Model\Repo;

/**
 * The Product model repository
 *
 * 
 */
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]
 * @Extender\Depend("XC\ProductTags")
 *
 */

class Product extends \XLite\Model\Repo\Product
{
    const P_TAG_NAME    = 'tagName';

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     *
     * @return void
     */
    protected function prepareCndTagName(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder->linkLeft('p.tags', 't');
            $this->addTranslationJoins($queryBuilder, 't', 'tt', $this->getTranslationCode());
            $queryBuilder
                ->andWhere('tt.name LIKE :tagName')
                ->setParameter('tagName', $value)
                ->groupBy('p.product_id');
        }
    }
}
