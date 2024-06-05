<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * Product classes repository
 */
class ProductClass extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = 'position';

    /**
     * Allowable search params
     */
    public const CND_PRODUCT      = 'product';
    public const CND_NAME         = 'name';
    public const CND_EXCLUDING_ID = 'excludingId';

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition OPTIONAL
     *
     * @return void
     */
    protected function prepareCndProduct(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        if ($value && !is_object($value)) {
            $ids = [];
            foreach ($value as $product) {
                if (
                    $product
                    && is_object($product)
                    && $product->getProductClass()
                ) {
                    $ids[$product->getProductClass()->getId()] = $product->getProductClass()->getId();
                }
            }

            if ($ids) {
                $queryBuilder->andWhere($queryBuilder->expr()->in('p.id', $ids));
            } else {
                $queryBuilder->andWhere('p.id is null');
            }
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array|string               $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndName(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            $queryBuilder->andWhere('translations.name = :name')
                ->setParameter('name', $value);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array|string               $value        Condition data
     *
     * @return void
     */
    protected function prepareCndExcludingId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder->andWhere('p.id <> :id')
                ->setParameter('id', $value);
        }
    }

    // }}}

    // {{{ Find one by name

    /**
     * Find product class by name (any language)
     *
     * @param string  $name      Name
     * @param boolean $countOnly Count only OPTIONAL
     *
     * @return \XLite\Model\ProductClass|integer
     */
    public function findOneByName($name, $countOnly = false)
    {
        return $countOnly
            ? count($this->defineFindByNameQuery($name)->getResult())
            : $this->defineFindByNameQuery($name)->getSingleResult();
    }

    /**
     * Find product classes by name (any language)
     *
     * @param string $name Name
     *
     * @return array
     */
    public function findByName($name)
    {
        return $this->defineFindByNameQuery($name)->getResult();
    }

    /**
     * Define query builder for findOneByName() and findByName() method
     *
     * @param string $name Name
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFindByNameQuery($name)
    {
        $qb = $this->createQueryBuilder()
            ->andWhere('translations.name = :name')
            ->setParameter('name', $name);

        return $qb;
    }

    // }}}

    // {{{ findDuplicateNames

    /**
     * Find duplicate names
     *
     * @return array
     */
    public function findDuplicateNames()
    {
        return $this->defineFindDuplicateNames()->getResult();
    }

    /**
     * Define query builder for findDuplicateNames() method
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindDuplicateNames()
    {
        return $this->getQueryBuilder()
            ->select('entityTranslation.name')
            ->from($this->_entityName . 'Translation', 'entityTranslation')
            ->groupBy('entityTranslation.name')
            ->having('COUNT(entityTranslation.name) > :count')
            ->setParameter('count', 1);
    }

    // }}}
}
