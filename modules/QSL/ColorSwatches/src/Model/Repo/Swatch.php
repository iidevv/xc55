<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ColorSwatches\Model\Repo;

/**
 * Swatch repository class
 */
class Swatch extends \XLite\Model\Repo\Base\I18n
{
    public const SEARCH_ORDERBY = 'orderBy';
    public const SEARCH_LIMIT   = 'limit';
    public const SEARCH_KEYWORDS = 'keywords';

    /**
     * Check - color swatches mechanism is available or not
     *
     * @return boolean
     */
    public function isAvailable()
    {
        return $this->count() > 0;
    }

    /**
     * Find all active swatches
     *
     * @return \QSL\ColorSwatches\Model\Swatch[]
     */
    public function findAllActive()
    {
        return $this->defineFindAllActiveQuery()->getResult();
    }

    /**
     * Has one by name / color
     *
     * @param string $name Name / color
     *
     * @return boolean
     */
    public function hasSwatch($name)
    {
        return $this->defineHasSwatchQuery($name)->count() > 0;
    }

    /**
     * Find one by name / color
     *
     * @param string $name Name / color
     *
     * @return \QSL\ColorSwatches\Model\Swatch
     */
    public function findOneByName($name)
    {
        return $this->defineFindOneByNameQuery($name)->getSingleResult();
    }

    /**
     * Define query builder for findAllActive() method
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindAllActiveQuery()
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.position');
    }

    /**
     * Define query builder for hasSwatch() method
     *
     * @param string $name Name / color
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineHasSwatchQuery($name)
    {
        return $this->defineFindOneByNameQuery($name);
    }

    /**
     * Define query builder for findOneByName() method
     *
     * @param string $name Name / color
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindOneByNameQuery($name)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('translations.name = :name OR s.color = :color')
            ->setParameter('name', $name)
            ->setParameter('color', $name)
            ->setMaxResults(1);
    }

    /**
     * @return mixed
     */
    public function getDefaultSwatch()
    {
        return \XLite\Core\Cache\ExecuteCached::executeCachedRuntime(function () {
            return $this->findOneBy(['defaultValue' => true]);
        }, ['getDefaultSwatch']);
    }

    /**
     * @return int|null
     */
    public function getDefaultSwatchId()
    {
        return $this->getDefaultSwatch() ? $this->getDefaultSwatch()->getId() : $this->getFirstSwatchId();
    }

    /**
     * @return int|null
     */
    public function getFirstSwatchId()
    {
        return \XLite\Core\Cache\ExecuteCached::executeCachedRuntime(function () {
            $swatch = $this->defineFindAllActiveQuery()->getSingleResult();
            return $swatch ? $swatch->getId() : null;
        }, ['getFirstSwatchId']);
    }

    // {{{ Search

    /**
     * @inheritdoc
     */
    public function getQueryBuilderForSearch()
    {
        $queryBuilder = parent::getQueryBuilderForSearch();
        if ($this->searchState['searchMode'] != static::SEARCH_MODE_COUNT) {
            $queryBuilder->groupBy('s.id')
                ->orderBy('s.position');
        }

        return $queryBuilder;
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array|string               $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses
     *                                                 if only count is needed.
     *
     * @return void
     */
    protected function prepareCndKeywords(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            $queryBuilder->andWhere('translations.name LIKE :keywords')
                ->setParameter('keywords', '%' . $value . '%');
        }
    }

    // }}}
}
