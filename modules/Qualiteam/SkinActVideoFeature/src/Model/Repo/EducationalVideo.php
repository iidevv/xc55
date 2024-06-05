<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\Model\Repo;

use Doctrine\ORM\EntityManagerInterface;
use Qualiteam\SkinActVideoFeature\Model\VideoCategory as VideoCategoryModel;
use XLite\Model\QueryBuilder\AQueryBuilder;

class EducationalVideo extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Allowable search params
     */
    public const P_CATEGORY_ID       = 'categoryId';
    public const P_ENABLED            = 'enabled';
    public const P_SEARCH_IN_SUBCATS = 'searchInSubcats';
    public const P_VIDEO_DESCRIPTION = 'description';

    public const INCLUDING_ALL    = 'all';
    public const INCLUDING_ANY    = 'any';
    public const INCLUDING_PHRASE = 'phrase';

    public const DESCR_FIELD = 'translations.description';
    public const P_BY_DESCR = 'byDescr';

    /**
     * Create a new QueryBuilder instance that is prepopulated for this entity name
     *
     * @param string $alias   Table alias OPTIONAL
     * @param string $indexBy The index for the from. OPTIONAL
     * @param string $code    Language code OPTIONAL
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    public function createQueryBuilder($alias = null, $indexBy = null, $code = null)
    {
        $queryBuilder = parent::createQueryBuilder($alias, $indexBy, $code);

        $queryBuilder->groupBy($queryBuilder->getRootAliases()[0] . '.' . $this->getPrimaryKeyField());
        $queryBuilder->addGroupBy('translations.description');

        $alias = $alias ?: $queryBuilder->getRootAlias();
        $this->addEnabledCondition($queryBuilder, $alias);
        $this->addDateCondition($queryBuilder, $alias);

        return $queryBuilder;
    }

    /**
     * Count last updated videos
     *
     * @param integer $limit Time limit
     *
     * @return integer
     */
    public function countLastUpdated($limit)
    {
        return (int)$this->defineCountLastUpdatedQuery($limit)->getSingleScalarResult();
    }

    /**
     * Assign enabled condition for extenral query builders
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $qb    External query builder
     * @param string                                  $alias Video repository alias OPTIONAL
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    public function assignExternalEnabledCondition(\XLite\Model\QueryBuilder\AQueryBuilder $qb, $alias = 'e')
    {
        $this->addEnabledCondition($qb, $alias);
        $this->addDateCondition($qb, $alias);

        return $qb;
    }

    /**
     * Define query for countLastUpdated()
     *
     * @param integer $limit Time limit
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineCountLastUpdatedQuery($limit)
    {
        return $this->createQueryBuilder('e')
            ->select('COUNT(e.id) cnt')
            ->andWhere('e.updateDate >= :time')
            ->setParameter('time', $limit)
            ->setMaxResults(1);
    }

    /**
     * Excluded search conditions
     *
     * @return array
     */
    protected function getExcludedConditions()
    {
        $excludedConditions = array_merge(
            $this->getConditionBy(),
            [
                static::P_SEARCH_IN_SUBCATS,
            ]
        );

        return array_merge(
            parent::getExcludedConditions(),
            array_fill_keys(
                $excludedConditions,
                static::EXCLUDE_FROM_ANY
            )
        );
    }

    /**
     * Return conditions parameters that are responsible for videosubstring set of fields.
     *
     * @return array
     */
    protected function getConditionBy()
    {
        return [
            static::P_BY_DESCR,
        ];
    }

    /**
     * Return fields set for description search
     *
     * @return array
     */
    protected function getVideosubstringSearchFieldsByDescr()
    {
        return [
            static::DESCR_FIELD,
        ];
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndEnabled(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->andWhere('e.enabled = :enabled')
            ->setParameter('enabled', (int)(bool)$value);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndCategoryId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            if (is_object($value) && $value instanceof VideoCategoryModel) {
                $value = $value->getCategoryId();
            }

            $queryBuilder->linkLeft('e.categoryVideos', 'cp')
                ->linkLeft('cp.category', 'c');

            if ($value === 'no_category') {
                $queryBuilder->andWhere('e.categoryVideos is empty');
            } else if (empty($this->searchState['currentSearchCnd']->{static::P_SEARCH_IN_SUBCATS})) {
                $queryBuilder->andWhere('c.id = :categoryId')
                    ->setParameter('categoryId', $value);
            } else {
                \XLite\Core\Database::getRepo(VideoCategoryModel::class)->addSubTreeCondition($queryBuilder, $value);
            }
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndCategoryIds(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (!is_array($value) || count(array_filter($value)) === 0) {
            return;
        }

        $queryBuilder->linkLeft('e.categoryVideos', 'cp')
            ->linkLeft('cp.category', 'c');

        $exprs = [];

        if (($key = array_search('no_category', $value, true)) !== false) {
            $exprs[] = 'e.categoryVideos is empty';
            unset($value[$key]);
        }

        if (count($value) > 0) {
            $exprs[] = 'c.id IN (:categoryIds)';
            $queryBuilder->setParameter('categoryIds', $value);
        }

        $queryBuilder->andWhere(call_user_func_array([$queryBuilder->expr(), 'orX'], $exprs));
    }

    /**
     * Returns array of allowed values for 'includes' input variable
     *
     * @return array
     */
    protected function getAllowedIncludingValues()
    {
        return [static::INCLUDING_ALL, static::INCLUDING_ANY, static::INCLUDING_PHRASE];
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndDate(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (is_array($value)) {
            $value = array_values($value);
            $start = empty($value[0]) ? null : (int)$value[0];
            $end = empty($value[1]) ? null : (int)$value[1];

            if ($start) {
                $queryBuilder->andWhere('e.date >= :start')
                    ->setParameter('start', $start);
            }

            if ($end) {
                $queryBuilder->andWhere('e.date <= :end')
                    ->setParameter('end', $end);
            }
        }
    }

    protected function prepareCndDescription(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
                $queryBuilder->bindAndCondition('translations.description', '%' . $value . '%', 'LIKE');
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndDateRange(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {
            [$start, $end] = \XLite\View\FormField\Input\Text\DateRange::convertToArray($value);
            if ($start) {
                $queryBuilder->andWhere('e.date >= :start')
                    ->setParameter('start', $start);
            }

            if ($end) {
                $queryBuilder->andWhere('e.date <= :end')
                    ->setParameter('end', $end);
            }
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndOrderBy(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value)
    {
        if (!$this->isCountSearchMode()) {
            [$sort, $order] = $this->getSortOrderValue($value);

            if ($sort === 'translations.description') {
                $this->addSortByTranslation($queryBuilder, $sort, $order);
                $sort = 'calculatedName';
            }

            $queryBuilder->addOrderBy($sort, $order);

            if ($sort !== 'e.id') {
                $queryBuilder->addOrderBy('e.id', $order);
            }

            if ($sort === 'ct.name') {
                $queryBuilder->linkLeft('e.categoryVideos', 'cv');
                $queryBuilder->linkLeft('cv.category', 'c');
                $queryBuilder->linkLeft('c.translations', 'ct');
                $queryBuilder->addSelect('ct.name category');
                $sort = 'category';
                $queryBuilder->addOrderBy($sort, $order);
            }
        }
    }

    /**
     * Add 'sort by name' builder structures
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $sort         Sort field
     * @param string                     $order        Sort direction
     *
     * @return void
     */
    protected function addSortByTranslation(\Doctrine\ORM\QueryBuilder $queryBuilder, $sort, $order)
    {
        // Main alias
        $alias = $this->getMainAlias($queryBuilder);

        // The language code of current user session
        $currentCode = $this->getTranslationCode();

        // Default store language code
        $defaultCode = \XLite::getDefaultLanguage();

        // Add additional join to translations with current language code
        $this->addTranslationJoins($queryBuilder, $alias, 'st', $currentCode);
        $queryBuilder->addGroupBy('st.description');

        if ($currentCode !== $defaultCode) {
            // Add additional join to translations with default language code
            $this->addTranslationJoins($queryBuilder, $alias, 'st2', $defaultCode);
            $queryBuilder->addGroupBy('st2.description');

            // Add calculated field to the fields list to use this for sorting out
            $queryBuilder->addSelect('IFNULL(st.description,IFNULL(st2.description,translations.description)) calculatedName');
        } else {
            // Add calculated field to the fields list to use this for sorting out
            $queryBuilder->addSelect('IFNULL(st.description,translations.description) calculatedName');
        }
    }

    /**
     * Adds additional condition to the query for checking if video is enabled
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder object
     * @param string                     $alias        Entity alias OPTIONAL
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function addEnabledCondition(\Doctrine\ORM\QueryBuilder $queryBuilder, $alias = null)
    {
        if (!\XLite::isAdminZone()) {
            $this->assignEnabledCondition($queryBuilder, $alias);
        }

        return $queryBuilder;
    }

    /**
     * Assign enabled condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder
     * @param string                     $alias        Alias OPTIONAL
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function assignEnabledCondition(\Doctrine\ORM\QueryBuilder $queryBuilder, $alias = null)
    {
        $alias = $alias ?: $queryBuilder->getRootAlias();
        $queryBuilder->andWhere($alias . '.enabled = :enabled')
            ->setParameter('enabled', true);

        return $queryBuilder;
    }

    /**
     * Define calculated name definition DQL
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder Query builder
     * @param string                                  $alias        Main alias
     *
     * @return string
     */
    protected function defineCalculatedNameDQL(\XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder, $alias)
    {
        return 'translations.description';
    }

    /**
     * Adds additional condition to the query for checking if video is up-to-date
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder object
     * @param string                     $alias        Entity alias OPTIONAL
     *
     * @return void
     */
    protected function addDateCondition(\Doctrine\ORM\QueryBuilder $queryBuilder, $alias = null)
    {
        if (!\XLite::isAdminZone()) {
            $alias = $alias ?: $queryBuilder->getRootAlias();
            $queryBuilder->andWhere($alias . '.date < :now')
                ->setParameter('now', \XLite\Core\Converter::getDayEnd(\XLite\Base\SuperClass::getUserTime()));
        }
    }

    /**
     * Add the specific joints with the translation table
     *
     * @param AQueryBuilder $queryBuilder
     * @param string        $alias
     * @param string        $translationsAlias
     * @param string        $code
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function addTranslationJoins($queryBuilder, $alias, $translationsAlias, $code)
    {
        $queryBuilder->linkLeft(
            $alias . '.translations',
            $translationsAlias
        );

        return $queryBuilder;
    }
}