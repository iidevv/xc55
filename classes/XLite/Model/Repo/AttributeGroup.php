<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * Attribute groups repository
 */
class AttributeGroup extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Allowable search params
     */
    public const SEARCH_PRODUCT_CLASS = 'productClass';
    public const SEARCH_NAME          = 'name';
    public const SEARCH_EXCLUDING_ID  = 'excludingId';

    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = 'position';

    // {{{ Search

    /**
     * Search by product class
     *
     * @param type $productClass
     *
     * @return array
     */
    public function findByProductClass($productClass)
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->productClass = $productClass;
        return $this->search($cnd);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition OPTIONAL
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function prepareCndProductClass(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        if ($value) {
            $queryBuilder->andWhere('a.productClass = :productClass')
                ->setParameter('productClass', $value);
        } else {
            $queryBuilder->andWhere('a.productClass is null');
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition OPTIONAL
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function prepareCndName(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        $queryBuilder->andWhere('translations.name = :name')
            ->setParameter('name', $value);
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
            $queryBuilder->andWhere('a.id <> :id')
                ->setParameter('id', $value);
        }
    }

    // }}}

    // {{{ Find one by name

    /**
     * Find entity by name (any language)
     *
     * @param string  $name      Name
     * @param boolean $countOnly Count only OPTIONAL
     *
     * @return \XLite\Model\AttributeGroup|integer
     */
    public function findOneByName($name, $countOnly = false)
    {
        return $countOnly
            ? count($this->defineOneByNameQuery($name)->getResult())
            : $this->defineOneByNameQuery($name)->getSingleResult();
    }

    /**
     * Define query builder for findOneByName() method
     *
     * @param string $name Name
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineOneByNameQuery($name)
    {
        $qb = $this->createQueryBuilder()
            ->andWhere('translations.name = :name')
            ->setParameter('name', $name);

        return $qb;
    }

    // }}}

    // {{{ findOneByNameAndProductClass and findByNameAndProductClass

    /**
     * Find entity by name (any language) and product class
     *
     * @param string                    $name      Name
     * @param \XLite\Model\ProductClass $productClass
     * @param boolean                   $countOnly Count only OPTIONAL
     *
     * @return \XLite\Model\AttributeGroup|integer
     */
    public function findOneByNameAndProductClass($name, $productClass, $countOnly = false)
    {
        return $countOnly
            ? count($this->defineFindByNameAndProductClassQuery($name, $productClass)->getResult())
            : $this->defineFindByNameAndProductClassQuery($name, $productClass)->getSingleResult();
    }

    /**
     * Find entity by name (any language) and product class
     *
     * @param string                    $name
     * @param \XLite\Model\ProductClass $productClass
     *
     * @return \XLite\Model\AttributeGroup|integer
     */
    public function findByNameAndProductClass($name, $productClass)
    {
        return $this->defineFindByNameAndProductClassQuery($name, $productClass)->getResult();
    }

    /**
     * Define query builder for findOneByNameAndProductClass() method
     *
     * @param string $name Name
     * @param \XLite\Model\ProductClass $productClass
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFindByNameAndProductClassQuery($name, $productClass)
    {
        $qb = $this->createQueryBuilder()
            ->andWhere('translations.name = :name')
            ->setParameter('name', $name);

        if ($productClass) {
            $qb->andWhere('a.productClass = :productClass')
                ->setParameter('productClass', $productClass);
        } else {
            $qb->andWhere('a.productClass is null');
        }

        return $qb;
    }

    // }}}

    // {{{ findDuplicateNames

    /**
     * Find duplicate names
     *
     * @param \XLite\Model\ProductClass $productClass
     *
     * @return array
     */
    public function findDuplicateNames($productClass)
    {
        return $this->defineFindDuplicateNames($productClass)->getResult();
    }

    /**
     * Define query builder for findDuplicateNames() method
     *
     * @param \XLite\Model\ProductClass $productClass
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindDuplicateNames($productClass)
    {
        $qb = $this->createQueryBuilder()
            ->select('translations.name')
            ->groupBy('translations.name')
            ->having('COUNT(translations.name) > :count')
            ->setParameter('count', 1);

        if ($productClass) {
            $qb->andWhere('a.productClass = :productClass')
                ->setParameter('productClass', $productClass);
        } else {
            $qb->andWhere('a.productClass is null');
        }

        return $qb;
    }

    // }}}
}
