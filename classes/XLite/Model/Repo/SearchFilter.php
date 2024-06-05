<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * SearchFilter repository
 */
class SearchFilter extends \XLite\Model\Repo\Base\I18n
{
    /**
     * @param string $name Name
     * @param string $filterKey Filter key
     *
     * @return \XLite\Model\SearchFilter
     */
    public function findOneByNameAndKey($name, $filterKey)
    {
        return $this->defineFindOneByNameQuery($name, $filterKey)->getSingleResult();
    }

    /**
     * Define query for findOneByName() method
     *
     * @param string $name Name
     * @param string $filterKey Filter key
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindOneByNameQuery($name, $filterKey)
    {
        return $this->createQueryBuilder('sf')
            ->andWhere('translations.name = :name')
            ->andWhere('sf.filterKey = :key')
            ->setParameter('name', $name)
            ->setParameter('key', $filterKey)
            ->setMaxResults(1);
    }
}
