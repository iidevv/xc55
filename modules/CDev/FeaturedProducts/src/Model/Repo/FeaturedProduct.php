<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FeaturedProducts\Model\Repo;

class FeaturedProduct extends \XLite\Model\Repo\ARepo
{
    // {{{ Search

    public const SEARCH_CATEGORY_ID = 'categoryId';

    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = 'orderBy';


    /**
     * Get featured products list
     *
     * @param integer $categoryId Category ID
     *
     * @return array(\CDev\FeaturedProducts\Model\FeaturedProduct) Objects
     */
    public function getFeaturedProducts($categoryId)
    {
        return $this->findByCategoryId($categoryId);
    }

    /**
     * Find by type
     *
     * @param integer $categoryId Category ID
     *
     * @return array
     */
    protected function findByCategoryId($categoryId)
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->{static::SEARCH_CATEGORY_ID} = $categoryId;
        return $this->search($cnd);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     *
     * @return void
     */
    protected function prepareCndCategoryId(\Doctrine\ORM\QueryBuilder $qb, $value)
    {
        $f = $this->getMainAlias($qb);
        $qb = $qb->innerJoin($f . '.product', 'p')
            ->andWhere($f . '.category = :categoryId')
            ->setParameter('categoryId', $value);

        return \XLite\Core\Database::getRepo('XLite\Model\Product')->assignExternalEnabledCondition($qb, 'p');
    }

    // }}}
}
