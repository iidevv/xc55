<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo\Shipping;

use XCart\Doctrine\FixtureLoader;

/**
 * Shipping method
 */
class Method extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Search parameters
     */
    public const P_CARRIER              = 'carrier';
    public const P_ADDED                = 'added';
    public const P_ENABLED              = 'enabled';
    public const P_PROCESSOR            = 'processor';
    public const P_EXCL_PROCESSORS      = 'excludingProcessors';
    public const P_ALL_CARRIER_SERVICES = 'allCarrierServices';

    /**
     * Repository type
     *
     * @var string
     */
    protected $type = self::TYPE_SECONDARY;

    /**
     * Alternative record identifiers
     *
     * @var array
     */
    protected $alternativeIdentifier = [
        ['processor', 'code'],
    ];

    /**
     * max position of current fixtures loader
     *
     * @var int
     */
    protected $position = [];

    /**
     * @return FixtureLoader
     */
    protected function getFixtureLoader()
    {
        return \XCart\Container::getContainer()->get(FixtureLoader::class);
    }

    /**
     * Find all methods as options list
     *
     * @return array
     */
    public function findAsOptions()
    {
        return $this->defineFindAsOptionsQuery()->getResult();
    }

    /**
     * Returns shipping methods by specified processor Id
     *
     * @param string  $processorId Processor Id
     * @param boolean $enabledOnly Flag: Get only enabled methods (true) or all methods (false) OPTIONAL
     *
     * @return \XLite\Model\Shipping\Method[]
     */
    public function findMethodsByProcessor($processorId, $enabledOnly = true)
    {
        return $this->defineFindMethodsByProcessor($processorId, $enabledOnly)->getResult();
    }

    /**
     * Returns carrier service with greatest position by processor
     *
     * @param string $processorId Processor Id
     *
     * @return \XLite\Model\Shipping\Method
     */
    public function findOneMaxPositionByProcessor($processorId)
    {
        return $this->defineFindOneMaxPositionByProcessorQuery($processorId)->getSingleResult();
    }

    /**
     * Returns shipping method with greatest position
     *
     * @return \XLite\Model\Shipping\Method
     */
    public function findOneCarrierMaxPosition()
    {
        return $this->defineFindOneCarrierMaxPositionQuery()->getSingleResult();
    }

    /**
     * Returns shipping methods by ids
     *
     * @param array $ids Array of method_id values
     *
     * @return array
     */
    public function findMethodsByIds($ids)
    {
        return $this->defineFindMethodsByIds($ids)->getResult();
    }

    /**
     * Create shipping method
     *
     * @param array $data Shipping method data
     *
     * @return \XLite\Model\Shipping\Method
     */
    public function createShippingMethod($data)
    {
        // Array of allowed fields and flag required/optional
        $fields = $this->getAllowedFields();

        $errorFields = [];

        foreach ($fields as $field => $required) {
            if (isset($data[$field])) {
                $fields[$field] = $data[$field];
            } elseif ($required) {
                $errorFields[] = $field;
            }
        }

        if (!empty($errorFields)) {
            throw new \Exception(
                'createShippingMethod() failed: The following required fields are missed: ' .
                implode(', ', $errorFields)
            );
        }

        $method = $this->findMethodToUpdate($fields);

        if ($method) {
            $this->update($method, $fields);
        } else {
            $method = new \XLite\Model\Shipping\Method();
            $method->map($fields);
            $method = $this->insert($method);
        }

        return $method;
    }

    /**
     * Returns allowed fields and flag required/optional
     *
     * @return array
     */
    protected function getAllowedFields()
    {
        return [
            'processor' => 1,
            'carrier'   => 1,
            'code'      => 1,
            'enabled'   => 0,
            'position'  => 0,
            'name'      => 1,
        ];
    }

    /**
     * Search option to update
     *
     * @param array $data Data
     *
     * @return \XLite\Model\Config
     */
    protected function findMethodToUpdate($data)
    {
        return $this->findOneBy(['processor' => $data['processor'], 'code' => $data['code']]);
    }

    /**
     * Adds additional condition to the query for checking if method is enabled
     *
     * @param \Doctrine\ORM\QueryBuilder $qb    Query builder object
     * @param string                     $alias Entity alias OPTIONAL
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function addEnabledCondition(\Doctrine\ORM\QueryBuilder $qb, $alias = 'm')
    {
        if (!\XLite::getInstance()->isAdminZone()) {
            $qb->andWhere($alias . '.enabled = 1');
        }

        return $qb;
    }

    /**
     * Define query builder object for findMethodsByProcessor()
     *
     * @param string  $processorId Processor Id
     * @param boolean $enabledOnly Flag: Get only enabled methods (true) or all methods (false)
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFindMethodsByProcessor($processorId, $enabledOnly)
    {
        $qb = $this->createQueryBuilder('m')
            ->andWhere('m.processor = :processorId')
            ->andWhere('m.carrier != :carrier')
            ->setParameter('processorId', $processorId)
            ->setParameter('carrier', '');

        return $enabledOnly
            ? $this->addEnabledCondition($qb)
            : $qb;
    }

    /**
     * Define query builder object for findOneMaxPositionByProcessor()
     *
     * @param string $processorId Processor Id
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFindOneMaxPositionByProcessorQuery($processorId)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.processor =:processorId')
            ->setParameter('processorId', $processorId)
            ->addOrderBy('m.position', 'DESC')
            ->setMaxResults(1);
    }

    /**
     * Define query builder object for findOneMaxPositionByProcessor()
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFindOneCarrierMaxPositionQuery()
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.carrier = :carrier')
            ->setParameter('carrier', '')
            ->addOrderBy('m.position', 'DESC')
            ->setMaxResults(1);
    }

    /**
     * Define query builder for findAsOptions() method
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindAsOptionsQuery()
    {
        return $this->createQueryBuilder('m')
            ->addOrderBy('m.carrier', 'asc')
            ->addOrderBy('m.position', 'asc');
    }

    /**
     * Define query builder object for findMethodsByIds()
     *
     * @param array $ids Array of method_id values
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFindMethodsByIds($ids)
    {
        $qb = $this->createQueryBuilder('m');

        return $qb->andWhere($qb->expr()->in('m.method_id', $ids));
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     *
     * @return void
     */
    protected function prepareCndCarrier(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->andWhere('m.carrier = :carrier')
            ->setParameter('carrier', $value);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     *
     * @return void
     */
    protected function prepareCndProcessor(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->andWhere('m.processor = :processor')
            ->setParameter('processor', $value);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Processors
     *
     * @return void
     */
    protected function prepareCndExcludingProcessors(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->andWhere("m.processor NOT IN ('" . implode("','", $value) . "')");
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     *
     * @return void
     */
    protected function prepareCndAdded(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->andWhere('m.added = :added')
            ->setParameter('added', $value);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                    $value        Condition data
     *
     * @return void
     */
    protected function prepareCndEnabled(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder
            ->andWhere($this->getMainAlias($queryBuilder) . '.enabled = :enabled_value')
            ->setParameter('enabled_value', $value);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     *
     * @return void
     */
    protected function prepareCndAllCarrierServices(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder->andWhere('m.carrier IN (:processors) OR m.processor = :offline')
                ->setParameter('processors', $this->getEnabledAddedProcessors())
                ->setParameter('offline', 'offline');
        }
    }

    /**
     * @return array
     */
    protected function getEnabledAddedProcessors()
    {
        $processors = $this->createQueryBuilder('ma')
            ->select('DISTINCT ma.processor')
            ->andWhere('ma.added = :added')
            ->andWhere('ma.enabled = :enabled')
            ->andWhere('ma.carrier = :carrier')
            ->setParameter('added', true)
            ->setParameter('enabled', true)
            ->setParameter('carrier', '')
            ->getResult();

        return array_map(static fn (array $processor): string => $processor['processor'], $processors);
    }

    // {{{ Online methods

    /**
     * Returns online carriers
     *
     * @return \XLite\Model\Shipping\Method[]
     */
    public function findOnlineCarriers()
    {
        $qb = $this->defineFindOnlineCarriers();

        return $qb->getResult();
    }

    /**
     * Returns online carrier by processor id
     *
     * @param string $processorId Processor id
     *
     * @return \XLite\Model\Shipping\Method
     */
    public function findOnlineCarrier($processorId)
    {
        $qb = $this->defineFindOnlineCarrier($processorId);

        return $qb->getSingleResult();
    }

    /**
     * Returns query builder for online carriers request
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindOnlineCarriers()
    {
        $qb = $this->createQueryBuilder();
        $qb->andWhere('m.carrier = :carrier')
            ->andWhere('m.processor != :processor')
            ->setParameter('carrier', '')
            ->setParameter('processor', 'offline')
            ->addOrderBy('translations.name');

        return $qb;
    }

    /**
     * Returns query builder for online carriers request
     *
     * @param string $processorId Processor id
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindOnlineCarrier($processorId)
    {
        $qb = $this->createQueryBuilder();
        $qb->andWhere('m.carrier = :carrier')
            ->andWhere('m.processor = :processor')
            ->setParameter('carrier', '')
            ->setParameter('processor', $processorId);

        return $qb;
    }

    // }}}

    /**
     * @param array                     $record
     * @param \XLite\Model\AEntity|null $parent
     * @param array                     $parentAssoc
     *
     * @return \XLite\Model\AEntity
     */
    public function loadFixture(
        array $record,
        \XLite\Model\AEntity $parent = null,
        array $parentAssoc = []
    ) {
        $this->position[$record['processor']] = $this->getPositionCounter($record['processor']) + 10;
        $record['position'] = $this->position[$record['processor']];

        return parent::loadFixture($record, $parent, $parentAssoc);
    }

    /**
     * @param string $processor
     *
     * @return int
     */
    protected function getPositionCounter($processor)
    {
        if (empty($this->position[$processor])) {
            $this->position[$processor] = $this->getMaxPosition($processor);
        }

        return $this->position[$processor];
    }

    // {{{ Update shipping methods from marketplace

    /**
     * Update shipping methods with data received from the marketplace
     *
     * @param array $data List of shipping methods received from marketplace
     *
     * @return void
     */
    public function updateShippingMethods($data)
    {
        if (is_array($data)) {
            $existingMethods = $this->createQueryBuilder('m')
                ->select('m')
                ->getQuery()
                ->getArrayResult();

            $existingOnlineMethods = [];

            foreach ($existingMethods as $m) {
                if ($m['processor'] === 'shipping_solution') {
                    $this->deleteById($m['method_id'], false);
                } elseif ($m['processor'] !== 'offline' && $m['carrier'] === '') {
                    $existingOnlineMethods[$m['processor']] = $m;
                }
            }

            $shippingMethods = array_merge(
                $this->getOnlineShippingMethods($data['processors'] ?? [], $existingOnlineMethods),
                $this->getShippingSolutions($data['addons'] ?? [])
            );

            // Save data as temporary yaml file
            $yaml     = \Symfony\Component\Yaml\Yaml::dump(['XLite\\Model\\Shipping\\Method' => $shippingMethods]);
            $yamlPath = LC_DIR_TMP . 'shm.yaml';

            \Includes\Utils\FileManager::write($yamlPath, $yaml);

            // Update database from yaml file
            $this->getFixtureLoader()->loadYaml($yamlPath);
        }
    }

    /**
     * Get online shipping methods from the marketplace
     *
     * @param array $methodsFromMarketplace List of shipping methods received from the marketplace
     * @param array $existingOnlineMethods  List of shipping methods received from the database
     *
     * @return array
     */
    protected function getOnlineShippingMethods($methodsFromMarketplace, $existingOnlineMethods)
    {
        $result = [];

        foreach ($methodsFromMarketplace as $methodFromMarketplace) {
            $processor = $methodFromMarketplace['processor'];

            if ($processor !== '') {
                if (
                    isset($existingOnlineMethods[$processor])
                    && $existingOnlineMethods[$processor]['fromMarketplace']
                ) {
                    continue;
                }

                $methodFromMarketplace['fromMarketplace'] = 1;

                unset(
                    $methodFromMarketplace['added'],
                    $methodFromMarketplace['enabled'],
                    $methodFromMarketplace['position'],
                    $methodFromMarketplace['iconURL']
                );

                $result[] = $methodFromMarketplace;
            }
        }

        return $result;
    }

    /**
     * Get shipping solutions from the marketplace
     *
     * @param array $addonsFromMarketplace List of addons received from the marketplace
     *
     * @return array
     */
    protected function getShippingSolutions($addonsFromMarketplace)
    {
        $result = [];

        foreach ($addonsFromMarketplace as $addonId) {
            $result[] = [
                'processor'       => 'shipping_solution',
                'moduleName'      => $addonId,
                'fromMarketplace' => 1,
            ];
        }

        return $result;
    }

    // }}}

    /**
     * return last offline pos if next pos not equal 100-200-etc
     *
     * @param string $processor
     *
     * @return int
     */
    public function getMaxPosition($processor)
    {
        $processorPos = $this->createPureQueryBuilder('m')
            ->select('MAX(m.position)')
            ->andWhere('m.processor = :processor')
            ->setParameter('processor', $processor)
            ->setMaxResults(1)
            ->getSingleScalarResult();

        if (empty($processorPos)) {
            if ($processor === 'offline') {
                return 0;
            }

            $pos = $this->createPureQueryBuilder('m')
                ->select('MAX(m.position)')
                ->setMaxResults(1)
                ->getSingleScalarResult();

            return empty($pos) ? 1000 : ceil($pos / 1000) * 1000;
        }

        return $processorPos;
    }
}
