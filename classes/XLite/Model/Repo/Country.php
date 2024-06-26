<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

use XLite\Model\QueryBuilder\AQueryBuilder;

/**
 * Country repository
 */
class Country extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Allowable search params
     */
    public const P_SUBSTRING  = 'substring' ;
    public const P_HAS_STATES = 'hasStates' ;


    /**
     * Repository type
     *
     * @var string
     */
    protected $type = self::TYPE_SERVICE;

    /**
     * Alternative record identifiers
     *
     * @var array
     */
    protected $alternativeIdentifier = [
        ['code'],
    ];

    // {{{ defineCacheCells

    /**
     * Define cache cells
     *
     * @return array
     */
    protected function defineCacheCells()
    {
        $list = parent::defineCacheCells();
        $languages = \XLite\Core\Database::getRepo('XLite\Model\Language')->findAllLanguages();

        $codes = array_map(
            static function ($lng) {
                return $lng->getCode();
            },
            $languages
        );
        foreach ($codes as $code) {
            $list['all_' . $code] = [
                static::RELATION_CACHE_CELL => [
                    '\XLite\Model\State',
                ],
            ];
            $list['enabled_' . $code] = [
                static::RELATION_CACHE_CELL => [
                    '\XLite\Model\State',
                ],
            ];
        }

        $list['states'] = [
            static::RELATION_CACHE_CELL => [
                '\XLite\Model\State',
            ],
        ];
        $list['statesGrouped'] = [
            static::RELATION_CACHE_CELL => [
                '\XLite\Model\State',
            ],
        ];

        return $list;
    }

    // }}}

    // {{{ findAllEnabled

    /**
     * Find all enabled countries
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findAllEnabled()
    {
        $lng = '_' . $this->getTranslationCode();

        $data = $this->getFromCache('enabled' . $lng);
        if (!isset($data)) {
            $data = $this->defineAllEnabledQuery()->getOnlyEntities();

            $this->saveToCache($data, 'enabled' . $lng);
        }

        return $data;
    }

    /**
     * Define query builder for findAllEnabled()
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineAllEnabledQuery()
    {
        $qb = $this->createQueryBuilder()
            ->addSelect('translations')
            ->addSelect('s')
            ->leftJoin('c.states', 's')
            ->andWhere('c.enabled = :enable')
            ->setParameter('enable', true)
            ->addSelect('currency')
            ->leftJoin('c.currency', 'currency')
        ;

        $this->prepareCndOrderBy($qb, ['translations.country', 'ASC']);

        return $qb;
    }

    // }}}

    // {{{

    /**
     * Find all countries
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findAllCountries()
    {
        $lng = '_' . $this->getTranslationCode();

        $data = $this->getFromCache('all' . $lng);
        if (!isset($data)) {
            $data = $this->defineAllCountriesQuery()->getOnlyEntities();

            $this->saveToCache($data, 'all' . $lng);
        }

        return $data;
    }

    /**
     * Define query builder for findAllCountries()
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineAllCountriesQuery()
    {
        $qb = $this->createQueryBuilder()
            ->addSelect('translations')
            ->addSelect('s')
            ->leftJoin('c.states', 's')
            ->addSelect('currency')
            ->leftJoin('c.currency', 'currency')
        ;

        $this->prepareCndOrderBy($qb, ['translations.country', 'ASC']);

        return $qb;
    }

    // }}}

    // {{{ findCountriesStates

    /**
     * Get hash array (key - enabled country code, value - empty array)
     *
     * @return array
     */
    public function findCountriesStates()
    {
        $data = $this->getFromCache('states');

        if (!isset($data)) {
            $data = $this->defineCountriesStatesQuery()->getResult();
            $data = $this->postprocessCountriesStates($data);

            $this->saveToCache($data, 'states');
        }

        return $data;
    }

    /**
     * Get hash array (key - enabled country code, value - empty array)
     *
     * @return array
     */
    public function findCountriesStatesGrouped()
    {
        $data = $this->getFromCache('statesGrouped');

        if (!isset($data)) {
            $data = $this->defineCountriesStatesQuery()->getResult();
            $data = $this->postprocessCountriesStatesGrouped($data);

            $this->saveToCache($data, 'statesGrouped');
        }

        return $data;
    }

    /**
     * Define query builder for findCountriesStates()
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineCountriesStatesQuery()
    {
        return $this->createQueryBuilder()
            ->addSelect('s')
            ->leftJoin('c.states', 's')
            ->where('c.enabled = :enabled')
            ->addOrderBy('s.state', 'ASC')
            ->setParameter('enabled', true);
    }

    /**
     * Postprocess enabled dump countries
     *
     * @param array $data Countries
     *
     * @return array
     */
    protected function postprocessCountriesStates(array $data)
    {
        $result = [];

        foreach ($data as $row) {
            if (0 < count($row->getStates())) {
                $code = $row->getCode();
                $result[$code] = [];

                foreach ($row->getStates() as $state) {
                    $result[$code][] = [
                        'name' => $state->getState(),
                        'key'  => $state->getStateId()
                    ];
                }
            }
        }

        return $result;
    }

    /**
     * Postprocess enabled dump countries as grouped
     *
     * @param array $data Countries
     *
     * @return array
     */
    protected function postprocessCountriesStatesGrouped(array $data)
    {
        $result = [];

        foreach ($data as $row) {
            if (0 < count($row->getStates())) {
                $countryCode = $row->getCode();
                $result[$countryCode] = [];
                foreach ($row->getRegions() as $region) {
                    $result[$countryCode][$region->getCode()] = [
                        'label'     => $region->getName(),
                        'options'   => []
                    ];
                }
                foreach ($row->getStates() as $state) {
                    if ($state->getRegion() === null) {
                        $result[$countryCode][] = [
                            'name' => $state->getState(),
                            'key'  => $state->getStateId()
                        ];
                    } else {
                        $code = $state->getRegion()->getCode();
                        $option = [
                            'name' => $state->getState(),
                            'key'  => $state->getStateId()
                        ];
                        $result[$countryCode][$code]['options'][] = $option;
                    }
                }
            }
        }

        return $result;
    }

    // }}}

    /**
     * Common search
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function processQueryBuilder()
    {
        $queryBuilder = parent::processQueryBuilder();

        // Replace all group by added previously
        $queryBuilder->groupBy($this->getMainAlias($queryBuilder) . '.code');

        return $queryBuilder;
    }

    /**
     * Add join to all country translations
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

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndSubstring(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if (!empty($value)) {
            $or = new \Doctrine\ORM\Query\Expr\Orx();

            $or->add('translations.country LIKE :pattern')
                ->add('c.code = :pattern2');

            $queryBuilder->andWhere($or)
                ->setParameter('pattern', '%' . $value . '%')
                ->setParameter('pattern2', $value);
        }

        return $queryBuilder;
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndHasStates(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        return $queryBuilder->innerJoin('c.states', 's')
            ->addOrderBy('translations.country', 'ASC');
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
            if ($sort === 'translations.country') {
                $this->addSortByTranslation($queryBuilder, $sort, $order);
                $sort = 'calculatedCountry';
                $queryBuilder->addOrderBy($sort, $order);
            } else {
                parent::prepareCndOrderBy($queryBuilder, $value);
            }
        }
    }

    /**
     * Add 'sort by name' builder structures
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $sort         Sort field
     * @param string                     $order        Sort direction
     */
    protected function addSortByTranslation(\Doctrine\ORM\QueryBuilder $queryBuilder, $sort, $order)
    {
        $alias = $this->getMainAlias($queryBuilder);

        $currentCode = $this->getTranslationCode();

        $defaultCode = \XLite::getDefaultLanguage();

        parent::addTranslationJoins($queryBuilder, $alias, 'st', $currentCode);

        if ($currentCode !== $defaultCode) {
            $this->addTranslationJoins($queryBuilder, $alias, 'st2', $defaultCode);
            $queryBuilder->addSelect('IFNULL(st.country,IFNULL(st2.country,translations.country)) calculatedCountry');
        } else {
            $queryBuilder->addSelect('IFNULL(st.country,translations.country) calculatedCountry');
        }
    }

    /**
     * Find all countries with active language
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findAllEnabledCountriesWithActiveLanguage()
    {
        $data = $this->defineAllEnabledCountriesWithActiveLanguageQuery()
            ->getResult();

        return $data;
    }

    /**
     * Define query builder for findAllEnabledCountriesWithActiveLanguage()
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineAllEnabledCountriesWithActiveLanguageQuery()
    {
        return $this->createQueryBuilder()
            ->select('c.code')
            ->linkInner('c.language')
            ->where('language.added = 1')
            ->andWhere('c.enabled = 1');
    }

    // }}}
}
