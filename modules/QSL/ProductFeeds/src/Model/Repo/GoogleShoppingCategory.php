<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\Model\Repo;

/**
 * Repository class for the GoogleShoppingCategory model.
 */
class GoogleShoppingCategory extends \XLite\Model\Repo\ARepo
{
    /**
     * Get the entire list of available GoogleShopping categories.
     *
     * @return mixed
     */
    public function getAll()
    {
        return $this->findBy([], ['name' => 'ASC']);
    }

    /**
     * Find one role by name
     *
     * @param string $name Name
     *
     * @return \QSL\ProductFeeds\Model\GoogleShoppingCategory
     */
    public function findOneByName($name)
    {
        return $this->defineFindOneByNameQuery($name)->getSingleResult();
    }

    /**
     * Define query for findOneByName() method
     *
     * @param string $name Name
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindOneByNameQuery($name)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.name = :name')
            ->setParameter('name', $name)
            ->setMaxResults(1);
    }
}
