<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo\Payment;

use Includes\Utils\Module\Manager;
use XCart\Doctrine\FixtureLoader;
use XLite\Model\QueryBuilder\AQueryBuilder;

/**
 * Payment method repository
 */
class Method extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Names of fields that are used in search
     */
    public const P_ENABLED             = 'enabled';
    public const P_MODULE_ENABLED      = 'moduleEnabled';
    public const P_ADDED               = 'added';
    public const P_POSITION            = 'position';
    public const P_TYPE                = 'type';
    public const P_PREDEFINED          = 'predefined';

    // Use the Force, Luke
    public const P_ORDER_BY_FORCE = 'orderByForce';

    public const P_NAME       = 'name';
    public const P_COUNTRY    = 'country';
    public const P_EX_COUNTRY = 'exCountry';

    /**
     * Name of the field which is used for default sorting (ordering)
     */
    public const FIELD_DEFAULT_POSITION = 'orderby';

    /**
     * Repository type
     *
     * @var string
     */
    protected $type = self::TYPE_SECONDARY;

    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = 'orderby';

    /**
     * Alternative record identifiers
     *
     * @var array
     */
    protected $alternativeIdentifier = [
        ['service_name'],
    ];

    /**
     * @return FixtureLoader
     */
    protected function getFixtureLoader()
    {
        return \XCart\Container::getContainer()->get(FixtureLoader::class);
    }

    /**
     * Add the specific joints with the translation table
     *
     * @param AQueryBuilder $queryBuilder
     * @param string        $alias
     * @param string        $translationsAlias
     * @param string        $code
     *
     * @return AQueryBuilder
     */
    protected function addTranslationJoins($queryBuilder, $alias, $translationsAlias, $code)
    {
        $queryBuilder
            ->linkLeft(
                $alias . '.translations',
                $translationsAlias,
                \Doctrine\ORM\Query\Expr\Join::WITH,
                $translationsAlias . '.code = :lng OR ' . $translationsAlias . '.code = :lng2'
            )
            ->setParameter('lng', $code)
            ->setParameter('lng2', 'en');

        return $queryBuilder;
    }

    /**
     * Update entity
     *
     * @param \XLite\Model\AEntity $entity Entity to update
     * @param array                $data   New values for entity properties
     * @param boolean              $flush  Flag OPTIONAL
     *
     * @return void
     */
    public function update(\XLite\Model\AEntity $entity, array $data = [], $flush = self::FLUSH_BY_DEFAULT)
    {
        $name = null;
        foreach ($entity->getTranslations() as $translation) {
            if ($translation->getName()) {
                $name = $translation->getName();
                break;
            }
        }

        if ($name) {
            foreach ($entity->getTranslations() as $translation) {
                if (!$translation->getName()) {
                    $translation->setName($name);
                }
            }
        }

        parent::update($entity, $data, $flush);
    }

    /**
     * Prepare certain search condition for module name
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag
     *
     * @return void
     */
    protected function prepareCndName(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value !== '') {
            $queryBuilder
                ->andWhere('translations.name LIKE :name')
                ->setParameter('name', "%" . $value . "%");
        }
    }

    /**
     * Prepare certain search condition for module name
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag
     *
     * @return void
     */
    protected function prepareCndCountry(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $alias = $this->getMainAlias($queryBuilder);

        $country = $value ?: \XLite\Core\Config::getInstance()->Company->location_country;
        $queryBuilder->linkLeft($alias . '.countryPositions', 'countryPosition', 'WITH', 'countryPosition.countryCode = :countryCode')
            ->setParameter('countryCode', $country);
        $queryBuilder->addSelect('(CASE WHEN countryPosition.adminPosition IS NULL THEN 1 ELSE 0 END) AS HIDDEN adminPosition');

        if (!empty($value)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $alias . '.countries LIKE :country',
                    $alias . '.countries = :emptyArray',
                    $alias . '.countries = :undefinedValue',
                    $alias . '.countries = :emptyValue'
                )
            )
                ->setParameter('country', '%"' . $value . '"%')
                ->setParameter('emptyArray', 'a:0:{}')
                ->setParameter('undefinedValue', 'N;')
                ->setParameter('emptyValue', '');
        }
    }

    /**
     * Prepare certain search condition for module name
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag
     *
     * @return void
     */
    protected function prepareCndExCountry(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if (!empty($value)) {
            $alias = $this->getMainAlias($queryBuilder);

            $queryBuilder->andWhere(
                $queryBuilder->expr()->not(
                    $alias . '.exCountries LIKE :country'
                )
            )
                ->setParameter('country', '%"' . $value . '"%');
        }
    }

    /**
     * Prepare certain search condition for enabled flag
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag
     *
     * @return void
     */
    protected function prepareCndEnabled(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $queryBuilder
            ->andWhere($this->getMainAlias($queryBuilder) . '.enabled = :enabled_value')
            ->setParameter('enabled_value', $value);
    }

    /**
     * Prepare certain search condition for moduleEnabled flag
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag
     *
     * @return void
     */
    protected function prepareCndModuleEnabled(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $enabledModules = array_map(
            static function ($moduleId) {
                [$author, $name] = explode('-', $moduleId);

                return "{$author}_{$name}";
            },
            array_merge(
                Manager::getRegistry()->getEnabledPaymentModuleIds(),
                Manager::getRegistry()->getEnabledShippingModuleIds()
            )
        );

        $enabledModules[] = '';

        $queryBuilder
            ->andWhere($queryBuilder->expr()->in($this->getMainAlias($queryBuilder) . '.moduleName', $enabledModules));
    }

    /**
     * Prepare certain search condition for added flag
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag
     *
     * @return void
     */
    protected function prepareCndAdded(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value !== null) {
            $queryBuilder
                ->andWhere($this->getMainAlias($queryBuilder) . '.added = :added_value')
                ->setParameter('added_value', $value);
        }
    }

    /**
     * Prepare certain search condition for predefined flag
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag
     *
     * @return void
     */
    protected function prepareCndPredefined(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value !== null) {
            $queryBuilder
                ->andWhere($this->getMainAlias($queryBuilder) . '.predefined = :predefined')
                ->setParameter('predefined', $value);
        }
    }

    /**
     * Prepare certain search condition for position
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag
     *
     * @return void
     */
    protected function prepareCndPosition(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value, $countOnly)
    {
        if (!$countOnly) {
            [$sort, $order] = $value;

            $queryBuilder->addOrderBy($this->getMainAlias($queryBuilder) . '.' . $sort, $order);
        }
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag
     *
     * @return void
     */
    protected function prepareCndType(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            $alias = $this->getMainAlias($queryBuilder);
            if (is_array($value)) {
                $queryBuilder->addInCondition($alias . '.type', $value);
            } else {
                $queryBuilder->andWhere($alias . '.type = :type')
                    ->setParameter('type', $value);
            }
        }
    }

    /**
     * Prepare certain search condition for position
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag
     *
     * @return void
     */
    protected function prepareCndOrderByForce(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value, $countOnly)
    {
        if (!$countOnly) {
            [$sort, $order] = $this->getSortOrderValue($value);

            $queryBuilder->orderBy($sort, $order);
            $this->assignDefaultOrderBy($queryBuilder);
        }
    }

    // }}}

    // {{{ Finders

    /**
     * Find all methods
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function findAllMethods()
    {
        return $this->defineAllMethodsQuery()->getResult();
    }

    /**
     * Find all active and ready for checkout payment methods.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function findAllActive()
    {
        return $this->defineAllActiveQuery()->getResult();
    }

    /**
     * Check - has active payment modules or not
     *
     * @return bool
     */
    public function hasActivePaymentModules()
    {
        return (bool) \count(Manager::getRegistry()->getEnabledPaymentModuleIds());
    }

    /**
     * Find offline method (not from modules)
     *
     * @return array
     */
    public function findOffline()
    {
        $list = [];

        foreach ($this->defineFindOfflineQuery()->getResult() as $method) {
            if (strpos('XLite', $method->getClass()) === 0) {
                $list[] = $method;
            }
        }

        return $list;
    }

    /**
     * Find offline method (only from modules)
     *
     * @return array
     */
    public function findOfflineModules()
    {
        $list = [];

        foreach ($this->defineFindOfflineQuery()->getResult() as $method) {
            if (preg_match('/\\\Module\\\/Ss', $method->getClass())) {
                $list[] = $method;
            }
        }

        return $list;
    }

    /**
     * Find payment methods by specified type for dialog 'Add payment method'
     *
     * @param string $type Payment method type
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function findForAdditionByType($type)
    {
        return $this->defineAdditionByTypeQuery($type)->getResult();
    }

    /**
     * Define query for findAdditionByType()
     *
     * @param string $type Payment method type
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineAdditionByTypeQuery($type)
    {
        $qb = $this->createPureQueryBuilder('m');

        $this->prepareCndType($qb, $type, false);
        $this->prepareCndOrderBy($qb, ['m.adminOrderby'], false);

        return $this->addOrderByForAdditionByTypeQuery($qb);
    }

    /**
     * Add ORDER BY for findAdditionByType() query
     *
     * @param \Doctrine\ORM\QueryBuilder $qb Query builder
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function addOrderByForAdditionByTypeQuery($qb)
    {
        return $qb->addOrderBy('m.moduleName', 'asc');
    }

    /**
     * Define query for findAllMethods() method
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineAllMethodsQuery()
    {
        return $this->createQueryBuilder();
    }

    /**
     * Define query for findAllActive() method
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineAllActiveQuery()
    {
        return $this->createQueryBuilder()
            ->andWhere('m.enabled = :true')
            ->andWhere('m.added = :true')
            ->setParameter('true', true);
    }

    /**
     * Define query for findOffline() method
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindOfflineQuery()
    {
        return $this->createPureQueryBuilder()
            ->setParameter('offline', \XLite\Model\Payment\Method::TYPE_OFFLINE);
    }

    // }}}

    // {{{ Update payment methods from marketplace

    /**
     * Update payment methods with data received from the marketplace
     *
     * @param array  $data List of payment methods received from marketplace
     * @param string $countryCode
     *
     * @return void
     */
    public function updatePaymentMethods($data, $countryCode = '')
    {
        if (!empty($data) && is_array($data)) {
            $methods                = [];
            $methodsFromMarketplace = [];

            // Get all payment methods list as an array
            $tmpMethods = $this->createQueryBuilder('m')
                ->select('m')
                ->getQuery()
                ->getArrayResult();

            if ($tmpMethods) {
                // Prepare associative array of existing methods with 'service_name' as a key
                foreach ($tmpMethods as $m) {
                    $methods[$m['service_name']] = $m;
                }
            }

            foreach ($data as $i => $extMethod) {
                if (!empty($extMethod['service_name'])) {
                    unset(
                        $extMethod['enabled'],
                        $extMethod['added'],
                        $extMethod['iconURL']
                    );

                    $data[$i]                 = $extMethod;
                    $methodsFromMarketplace[] = $extMethod['service_name'];

                    if (isset($methods[$extMethod['service_name']])) {
                        // Method already exists in the database

                        if (!$methods[$extMethod['service_name']]['fromMarketplace']) {
                            $data[$i] = [
                                'service_name'  => $extMethod['service_name'],
                                'countries'     => !empty($extMethod['countries']) ? $extMethod['countries'] : [],
                                'exCountries'   => !empty($extMethod['exCountries']) ? $extMethod['exCountries'] : [],
                                'orderby'       => !empty($extMethod['orderby']) ? $extMethod['orderby'] : 0,
                                'modulePageURL' => !empty($extMethod['modulePageURL']) ? $extMethod['modulePageURL'] : '',
                            ];
                        }
                    } else {
                        $data[$i]['fromMarketplace'] = 1;
                    }

                    if (isset($data[$i]['orderby'])) {
                        $data[$i]['adminOrderby'] = $data[$i]['orderby'];

                        $data[$i]['countryPositions'] = [
                            [
                                'countryCode'   => $countryCode,
                                'adminPosition' => $data[$i]['orderby'],
                            ],
                        ];

                        unset($data[$i]['orderby']);
                    }

                    if (isset($data[$i]['modulePageURL'])) {
                        $data[$i]['modulePageURL'] = \XLite::getAppStoreUrl() . $data[$i]['modulePageURL'];
                    }

                    $data[$i]['predefined'] = (bool) ($extMethod['predefined'] ?? false);
                } else {
                    // Wrong data row, ignore this
                    unset($data[$i]);
                }
            }

            // Save data as temporary yaml file
            $yaml = \Symfony\Component\Yaml\Yaml::dump(['XLite\\Model\\Payment\\Method' => $data]);

            $yamlFile = LC_DIR_TMP . 'pm.yaml';

            \Includes\Utils\FileManager::write(LC_DIR_TMP . 'pm.yaml', $yaml);

            // Update database from yaml file
            $this->getFixtureLoader()->loadYaml($yamlFile);

            $this->removeUnavailablePaymentMethods($methodsFromMarketplace);
        }
    }

    /**
     * Remove:
     * 1. Installed payment methods whose class doesn't exist
     * 2. Uninstalled payment methods which are no longer in the marketplace
     *
     * @param array $methodsFromMarketplace
     */
    protected function removeUnavailablePaymentMethods($methodsFromMarketplace)
    {
        foreach ($this->findAllMethods() as $method) {
            /** @var \XLite\Model\Payment\Method $method */
            if (
                !$method->isExisting()
                || (
                    $method->getFromMarketplace()
                    && !in_array($method->getServiceName(), $methodsFromMarketplace)
                )
            ) {
                $this->delete($method, false);
            }
        }

        \XLite\Core\Database::getEM()->flush();
    }

    // }}}
}
