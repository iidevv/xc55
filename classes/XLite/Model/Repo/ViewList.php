<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

use XLite\Core\Layout;

/**
 * View list repository
 */
class ViewList extends \XLite\Model\Repo\ARepo
{
    /**
     * Repository type
     *
     * @var string
     */
    protected $type = self::TYPE_INTERNAL;

    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = [
        'weight' => true,
        'child'  => true,
        'tpl'    => true,
    ];

    /**
     * Define cache cells
     *
     * @return array
     */
    protected function defineCacheCells()
    {
        $list = parent::defineCacheCells();
        $list['class_list'] = [
            static::ATTRS_CACHE_CELL => ['list', 'interface', 'zone'],
        ];

        return $list;
    }

    // {{{ Finders

    /**
     * Find class list
     *
     * @param string $list List name
     * @param string $interface
     * @param string $zone
     *
     * @return array
     */
    public function findClassList($list, $interface = \Xlite::INTERFACE_WEB, $zone = \XLite::ZONE_CUSTOMER)
    {
        $params = [
            'list' => $list,
            'interface' => $interface,
            'zone' => $zone,
            'preset' => $this->getCurrentPreset()
        ];

        if ($interface !== \XLite::INTERFACE_WEB) {
            $zone = '';
            unset($params['zone']);
        }

        $data = $this->getFromCache('class_list', $params);
        if (!isset($data)) {
            $data = $this->retrieveClassList($list, $interface, $zone);
            $this->saveToCache($data, 'class_list', $params);
        }

        return $data;
    }

    /**
     * Find class list
     *
     * @param string $list List name
     * @param string $interface
     * @param string $zone
     *
     * @return array
     */
    public function findClassListWithFallback($list, $interface = \XLite::INTERFACE_WEB, $zone = \XLite::ZONE_CUSTOMER)
    {
        $params = [
            'list' => $list,
            'interface' => $interface,
            'zone' => $zone
        ];

        if ($interface !== \XLite::INTERFACE_WEB) {
            $zone = '';
            unset($params['zone']);
        }

        $data = $this->getFromCache('class_list_with_fallback', $params);

        if (!isset($data)) {
            $data = $this->retrieveClassListWithFallback($list, $interface, $zone);
            $this->saveToCache($data, 'class_list_with_fallback', $params);
        }

        return $data;
    }

    /**
     * Find actual (with empty version) by list name
     *
     * @param string $list List name
     *
     * @return array
     */
    public function findActualByList($list)
    {
        return $this->createQueryBuilder()
            ->where('v.list = :list AND v.version IS NOT NULL')
            ->setParameter('list', $list)
            ->getResult();
    }

    /**
     * Perform Class list query
     *
     * @param string $list List name
     * @param string $interface
     * @param string $zone
     *
     * @return array
     */
    public function retrieveClassList($list, $interface, $zone)
    {
        return $this->defineClassListQuery($list, $interface, $zone)->getResult();
    }

    /**
     * Perform Class list query
     *
     * @param string $list List name
     * @param string $interface
     * @param string $zone
     *
     * @return array
     */
    public function retrieveClassListWithFallback($list, $interface, $zone)
    {
        $result = [];

        $actual = $this->defineClassListWithFallbackQuery($list, $interface, $zone)->getResult();

        foreach ($actual as $viewList) {
            $key = $viewList->getHashWithoutZone();

            if (
                !isset($result[$key])
                || $result[$key]->getZone() === \XLite::ZONE_COMMON
            ) {
                $result[$key] = $viewList;
            }
        }

        return $result;
    }

    /**
     * Define default query builder for findClassList() without zone parameter
     *
     * @param string $list Class list name
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineZoneAgnosticClassListQuery($list)
    {
        $qb = $this->createQueryBuilder()
            ->addSelect('CASE WHEN v.override_mode > 0 THEN v.weight_override ELSE v.weight END AS HIDDEN ORD')
            ->andWhere('IFELSE(v.list_override != :empty AND v.override_mode > 0, v.list_override, v.list) IN (:list)')
            ->andWhere('v.list_id NOT IN (SELECT DISTINCT IDENTITY(vl.parent) FROM XLite\Model\ViewList vl WHERE IDENTITY(vl.parent) IS NOT NULL AND vl.preset LIKE :preset AND vl.override_mode != :disable_preset_mode)')
            ->andWhere('v.list_id NOT IN (SELECT DISTINCT vll.list_id FROM XLite\Model\ViewList vll WHERE IDENTITY(vll.parent) IS NOT NULL AND (vll.preset NOT LIKE :preset OR vll.override_mode = :disable_preset_mode))')
            ->andWhere('v.version IS NULL')
            ->andWhere('v.override_mode IN (:modes)')
            ->andWhere('v.deleted = :deleted')
            ->orderBy('ORD', 'asc')
            ->setParameter('empty', '')
            ->setParameter('list', explode(',', $list))
            ->setParameter('preset', $this->getCurrentPreset())
            ->setParameter('modes', $this->getDisplayableModes())
            ->setParameter('deleted', false)
            ->setParameter('disable_preset_mode', \XLite\Model\ViewList::OVERRIDE_DISABLE_PRESET);

        return $qb;
    }

    /**
     * Define query builder for findClassList()
     *
     * @param string $list Class list name
     * @param string $interface
     * @param string $zone
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineClassListQuery($list, $interface, $zone)
    {
        $qb = $this->defineZoneAgnosticClassListQuery($list)
            ->andWhere('v.interface LIKE :interface')
            ->setParameter('interface', $interface);

        if ($zone) {
            $qb
                ->andWhere('v.zone LIKE :zone')
                ->setParameter('zone', $zone);
        }

        return $qb;
    }

    /**
     * Define query builder for findClassList()
     *
     * @param string $list Class list name
     * @param string $interface
     * @param string $zone
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineClassListWithFallbackQuery($list, $interface, $zone)
    {
        $qb = $this->defineZoneAgnosticClassListQuery($list)
            ->andWhere('v.interface = :interface')
            ->setParameter('interface', $interface);

        $qb
            ->andWhere('v.zone IN (:zone, :fallback)')
            ->setParameter('zone', $zone)
            ->setParameter('fallback', \XLite::ZONE_COMMON);

        return $qb;
    }

    /**
     * @return array
     */
    protected function getDisplayableModes()
    {
        return [
            \XLite\Model\ViewList::OVERRIDE_OFF,
            \XLite\Model\ViewList::OVERRIDE_MOVE
        ];
    }

    /**
     * @return string
     */
    protected function getCurrentPreset()
    {
        return Layout::getInstance()->getCurrentLayoutPreset();
    }

    // }}}

    // {{{ Operations

    /**
     * Delete obsolete view list childs
     *
     * @param string $currentVersion Current version
     *
     * @return void
     */
    public function deleteObsolete($currentVersion)
    {
        $this->defineDeleteObsoleteQuery($currentVersion)
            ->execute();
    }

    /**
     * Mark current view list childs as default
     *
     * @param string $currentVersion Current version
     *
     * @return void
     */
    public function markCurrentVersion($currentVersion)
    {
        $this->defineMarkCurrentVersionQuery($currentVersion)
            ->execute();
    }

    /**
     * Define query for deleteObsolete() method
     *
     * @param string $currentVersion Current version
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineDeleteObsoleteQuery($currentVersion)
    {
        return $this->createPureQueryBuilder('v', false)
            ->delete($this->_entityName, 'v')
            ->andWhere('v.version != :version OR v.version IS NULL')
            ->setParameter('version', $currentVersion);
    }

    /**
     * Define query for markCurrentVersion() method
     *
     * @param string $currentVersion Current version
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineMarkCurrentVersionQuery($currentVersion)
    {
        return $this->createPureQueryBuilder('v', false)
            ->update($this->_entityName, 'v')
            ->set('v.version', 'NULL')
            ->andWhere('v.version = :version')
            ->setParameter('version', $currentVersion);
    }

    // }}}

    /**
     * Find overridden view list items
     *
     * @return array
     */
    public function findOverridden()
    {
        return $this->defineOverriddenQueryBuilder()->getResult();
    }

    /**
     * Define overridden query builder
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    public function defineOverriddenQueryBuilder()
    {
        return $this->createQueryBuilder()
            ->where('v.override_mode > :off_mode')
            ->andWhere('v.version IS NULL')
            ->setParameter('off_mode', \XLite\Model\ViewList::OVERRIDE_OFF);
    }

    /**
     * Find first entity equal to $other
     *
     * @param \XLite\Model\ViewList $other     Other entity
     * @param boolean               $versioned Add `version is not null` condition
     *
     * @return \XLite\Model\ViewList|null
     */
    public function findEqual(\XLite\Model\ViewList $other, $versioned = false)
    {
        if (!$other) {
            return null;
        }

        $conditions = [
            'list'     => $other->getList(),
            'child'    => $other->getChild(),
            'entityId' => $other->getEntityId(),
            'tpl'      => $other->getTpl(),
            'zone'     => $other->getZone(),
            'weight'   => $other->getWeight(),
            'preset'   => $other->getPreset(),
        ];

        return $this->findEqualByData($conditions, $versioned);
    }

    /**
     * Find first entity equal to $other
     *
     * @param \XLite\Model\ViewList $other     Other entity
     * @param boolean               $versioned Add `version is not null` condition
     *
     * @return \XLite\Model\ViewList|null
     */
    public function findEqualParent(\XLite\Model\ViewList $other, $versioned = false)
    {
        if (!$other) {
            return null;
        }

        $conditions = [
            'list'          => $other->getList(),
            'child'         => $other->getChild(),
            'entityId'      => $other->getEntityId(),
            'tpl'           => $other->getTpl(),
            'zone'          => $other->getZone(),
            'parent'        => null,
            'override_mode' => \XLite\Model\ViewList::OVERRIDE_OFF,
        ];

        return $this->findEqualByData($conditions, $versioned);
    }

    /**
     * Find first entity equal to data
     *
     * @param array   $conditions
     * @param boolean $versioned Add `version is not null` condition
     *
     * @return \XLite\Model\ViewList|null
     */
    public function findEqualByData($conditions, $versioned = false)
    {
        $params = array_filter($conditions, static function ($item) {
            return $item !== null;
        });

        $qb = $this->createQueryBuilder()->setParameters($params);

        foreach ($conditions as $key => $condition) {
            if ($condition === null) {
                $qb->andWhere("v.{$key} IS NULL");
            } else {
                $qb->andWhere("v.{$key} = :{$key}");
            }
        }

        if ($versioned) {
            $qb->andWhere('v.version IS NOT NULL');
        }

        return $qb->getSingleResult();
    }

    /**
     * Update template rows for banner system
     *
     * @param array $childs childs to update
     *
     */
    public function updateOverrideModeByChilds($childs)
    {
        $expr = new \Doctrine\ORM\Query\Expr();
        $this->createPureQueryBuilder('p')
            ->update($this->_entityName, 'p')
            ->set('p.override_mode', ':override_mode')
            ->setParameter('override_mode', \XLite\Model\ViewList::OVERRIDE_DISABLE_PRESET)
            ->where($expr->in('p.child', $childs))
            ->andWhere('p.version IS NULL')
            ->andWhere('p.entityId IS NULL')
            ->andWhere('p.tpl = :tpl')->setParameter('tpl', '')// to use tclz key
            ->execute();
    }

    /**
     * Find view lists by child
     */
    public function findByChildTpl($child, $tpl)
    {
        return $this->createQueryBuilder()
            ->where('v.child = :child')
            ->setParameter('child', $child)
            ->andWhere('v.tpl = :tpl')->setParameter('tpl', $tpl)// to use tclz key
            ->getResult();
    }

    /**
     * @param array $criteria
     *
     * @return mixed
     */
    public function removeByCriteria(array $criteria)
    {
        $queryBuilder = $this->createPureQueryBuilder('v')
            ->delete($this->_entityName, 'v');

        $where = [];
        $params = [];
        foreach ($criteria as $field => $value) {
            $where[] = "v.{$field} = :{$field}";
            $params[$field] = $value;
        }

        return $queryBuilder->where(...$where)->setParameters($params)->execute();
    }
}
