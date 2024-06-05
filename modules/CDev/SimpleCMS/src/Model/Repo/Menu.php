<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\Model\Repo;

use Exception;
use XLite\InjectLoggerTrait;

class Menu extends \XLite\Model\Repo\Base\I18n
{
    use InjectLoggerTrait;

    /**
     * Allowable search params
     */
    public const SEARCH_TYPE           = 'type';
    public const SEARCH_ENABLED        = 'enabled';
    public const SEARCH_VISIBLE_FOR    = 'visibleFor';

    public const SEARCH_PARENT         = 'parent';

    /**
     * Root menu
     *
     * @var \CDev\SimpleCMS\Model\Menu
     */
    protected static $rootMenu;

    /**
     * Previous lpos or rpos of menu
     *
     * @var integer
     */
    protected static $prevPos = 2;

    /**
     * Maximum right pos
     *
     * @var integer
     */
    protected $maxRightPos;

    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = 'position';

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition OPTIONAL
     *
     * @return void
     */
    protected function prepareCndType(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        if ($value) {
            $queryBuilder->andWhere('m.type = :type')
                ->setParameter('type', $value);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                    $value        Condition OPTIONAL
     *
     * @return void
     */
    protected function prepareCndEnabled(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        if (!is_null($value)) {
            $queryBuilder->andWhere('m.enabled = :enabled')
                ->setParameter('enabled', $value);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition OPTIONAL
     *
     * @return void
     */
    protected function prepareCndVisibleFor(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        if (!is_null($value)) {
            $queryBuilder->andWhere('m.visibleFor IN (\'' . implode('\', \'', $value) . '\')');
        }
    }

    // }}}

    /**
     * Return the reserved ID of root menu
     *
     * @return integer
     */
    public function getRootMenu()
    {
        if (!isset(static::$rootMenu)) {
            static::$rootMenu = $this->findOneByLpos(1);
            if (!isset(static::$rootMenu)) {
                $rootMenu = new \CDev\SimpleCMS\Model\Menu();
                $menuData = [
                    'lpos' => 1,
                    'rpos' => 2,
                    'depth' => -1,
                    'visibleFor' => 'AL',
                    'type' => \CDev\SimpleCMS\Model\Menu::MENU_TYPE_PRIMARY,
                    'link' => 'link',
                ];
                $rootMenu->map($menuData);
                \XLite\Core\Database::getEm()->persist($rootMenu);
                \XLite\Core\Database::getEm()->flush();
                static::$rootMenu = $rootMenu;

                $this->editExistingMenus();
            }
        } elseif (!\XLite\Core\Database::getEM()->contains(static::$rootMenu)) {
            static::$rootMenu = $this->findOneByLpos(1);
        }

        return static::$rootMenu ?: null;
    }

    /**
     * Deletes root menu
     *
     * @return void
     */
    public function deleteRootMenu()
    {
        $this->searchState['queryBuilder'] = $this->createPureQueryBuilder()
            ->select('m')
            ->andWhere('m.parent = :parent')
            ->setParameter('parent', $this->getRootMenu());
        $menus = $this->searchResult();

        foreach ($menus as $menu) {
            $menu->setParent(null);
            \XLite\Core\Database::getEm()->persist($menu);
        }
        \XLite\Core\Database::getEm()->flush();
        static::$rootMenu = $this->findOneByLpos(1);
        if (isset(static::$rootMenu)) {
            \XLite\Core\Database::getEm()->remove(static::$rootMenu);
            \XLite\Core\Database::getEm()->flush();
        }
    }

    /**
     * Edits existing menus
     *
     * @return void
     */
    public function editExistingMenus()
    {
        $queryBuilder = $this->createPureQueryBuilder()
            ->select('m');
        $menus = $queryBuilder->getResult();
        $maxRpos = 2;
        foreach ($menus as $item) {
            if ($item->getLpos() === null || $item->getLpos() == 0) {
                $item = $this->performInsert($item);
            }
            if ($item->getParent() === null && $item->getDepth() !== -1) {
                $item->setParent($this->getRootMenu());
            }
            \XLite\Core\Database::getEm()->persist($item);
            if ($item->getRpos() > $maxRpos) {
                $maxRpos = $item->getRpos();
                $maxRpos++;
            }
        }
        $item = $this->getRootMenu()->setRpos($maxRpos);
        \XLite\Core\Database::getEm()->persist($item);
        \XLite\Core\Database::getEm()->flush();
    }

    /**
     * Return the reserved ID of root menu
     *
     * @return integer
     */
    public function getRootMenuId()
    {
        $menu = $this->getRootMenu();

        return $menu ? $menu->getMenuId() : null;
    }

    /**
     * Create a new QueryBuilder instance that is prepopulated for this entity name
     *
     * @param string  $alias       Table alias OPTIONAL
     * @param string  $code        Language code OPTIONAL
     * @param boolean $excludeRoot Do not include root menu into the search result OPTIONAL
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function createQueryBuilder($alias = null, $code = null, $excludeRoot = true)
    {
        $queryBuilder = parent::createQueryBuilder($alias, $code);

        return $this->initializeQueryBuilder($queryBuilder, $alias, $excludeRoot);
    }

    /**
     * find() with cache
     *
     * @param integer $menuId Menu ID
     *
     * @return \CDev\SimpleCMS\Model\Menu
     */
    public function getMenu($menuId)
    {
        return $this->find($this->prepareMenuId($menuId));
    }

    /**
     * Return full list of menus
     *
     * @param integer $rootId ID of the subtree root OPTIONAL
     *
     * @return array
     */
    public function getMenus($rootId = null)
    {
        return $this->defineFullTreeQuery($rootId)->getResult();
    }

    /**
     * Return full list of menus
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param integer                $rootId    ID of the subtree root OPTIONAL
     *
     * @return array
     */
    public function getMenusPlainList($cnd, $rootId = null)
    {
        $rootId = $rootId ?: $this->getRootMenuId();

        return $this->getMenusPlainListChild($cnd, $rootId);
    }

    /**
     * Return menus subtree
     *
     * @param integer $menuId Menu Id
     *
     * @return array
     */
    public function getSubtree($menuId)
    {
        return $this->getMenu($menuId)
            ? $this->defineSubtreeQuery($menuId)->getResult()
            : [];
    }

    /**
     * Get menus path from root to the specified menu
     *
     * @param integer $menuId Menu Id
     *
     * @return array
     */
    public function getMenuPath($menuId)
    {
        return $this->getMenu($menuId)
            ? $this->defineMenuPathQuery($menuId)->getResult()
            : [];
    }

    /**
     * Return the array of the menu path
     *
     * @param integer $menuId ID menu
     *
     * @return array
     */
    public function getMenuNamePath($menuId)
    {
        return array_map([$this, 'getMenuName'], $this->getMenuPath($menuId));
    }

    /**
     * The method is used as a callback in the "$this->getMenuNamePath()" method
     *
     * @param \CDev\SimpleCMS\Model\Menu $menu Menu
     *
     * @return string
     */
    public function getMenuName(\CDev\SimpleCMS\Model\Menu $menu)
    {
        return $menu->getName();
    }

    /**
     * Get depth of the menu path
     *
     * @param integer $menuId Menu Id
     *
     * @return integer
     */
    public function getMenuDepth($menuId)
    {
        return $this->getMenu($menuId)
            ? $this->defineMenuDepthQuery($menuId)->getSingleScalarResult()
            : 0;
    }

    /**
     * Find one by path
     *
     * @param array $path Path
     *
     * @return \CDev\SimpleCMS\Model\Menu
     */
    public function findOneByPath(array $path)
    {
        $result = $this->getRootMenu();

        if (!empty($path)) {
            do {
                $name = array_shift($path);
                $result = $this->createQueryBuilder()
                    ->andWhere('m.parent = :parent')
                    ->andWhere('translations.name = :name')
                    ->setParameter('parent', $result)
                    ->setParameter('name', $name)
                    ->getSingleResult();
            } while ($result && $path);
        }

        return $result;
    }

    /**
     * Get plan list for tree
     *
     * @param integer $menuId Menu id OPTIONAL
     *
     * @return array
     */
    public function getPlanListForTree($menuId = null)
    {
        $menuId = $menuId ?: $this->getRootMenuId();

        $list = [];
        foreach ($this->getChildsPlainListForTree($menuId) as $menu) {
            $list[] = [
                'id'  => $menu['id'],
                'depth'        => $menu['depth'],
                'translations' => $menu['translations'],
            ];
            if ($menu['rpos'] > $menu['lpos'] + 1) {
                $list = array_merge($list, $this->getPlanListForTree($menu['id']));
            }
        }

        return $list;
    }

    /**
     * Get childs plain list for tree
     *
     * @param integer $menuId Menu id
     *
     * @return array
     */
    public function getChildsPlainListForTree($menuId)
    {
        return $this->defineChildsPlainListForTreeQuery($menuId)->getArrayResult();
    }

    /**
     * Add the conditions for the current subtree
     *
     * NOTE: function is public since it's needed to the Product model repository
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to modify
     * @param integer                    $menuId       Current menu ID
     * @param string                     $field        Name of the field to use OPTIONAL
     * @param integer                    $lpos         Left position OPTIONAL
     * @param integer                    $rpos         Right position OPTIONAL
     *
     * @return boolean
     */
    public function addSubTreeCondition(
        \Doctrine\ORM\QueryBuilder $queryBuilder,
        $menuId,
        $field = 'lpos',
        $lpos = null,
        $rpos = null
    ) {
        $menu = $this->getMenu($menuId);

        if ($menu) {
            $lpos = $lpos ?: $menu->getLpos();
            $rpos = $rpos ?: $menu->getRpos();

            $queryBuilder->andWhere($queryBuilder->expr()->between('m.' . $field, $lpos, $rpos));
        }

        return isset($menu);
    }

    /**
     * Initialize the query builder (to prevent the use of language query)
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to use
     * @param string                     $alias        Table alias OPTIONAL
     * @param boolean                    $excludeRoot  Do not include root menu into the search result OPTIONAL
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function initializeQueryBuilder($queryBuilder, $alias = null, $excludeRoot = true)
    {
        $this->addEnabledCondition($queryBuilder, $alias);
        $this->addOrderByCondition($queryBuilder, $alias);

        if ($excludeRoot) {
            $this->addExcludeRootCondition($queryBuilder, $alias);
        }

        return $queryBuilder;
    }

    /**
     * Define query for getChildsPlainListForTree()
     *
     * @param integer $menuId Menu id
     *
     * @return \XLite\Model\QueryBulder\AQueryBuilder
     */
    protected function defineChildsPlainListForTreeQuery($menuId)
    {
        return $this->createPureQueryBuilder()
            ->select('m')
            ->addSelect('translations')
            ->linkInner('m.translations')
            ->linkInner('m.parent')
            ->andWhere('parent.id = :cid')
            ->setParameter('cid', $menuId);
    }

    /**
     * Define the Doctrine query
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineMaxRightPosQuery()
    {
        return $this->createPureQueryBuilder()
            ->select('MAX(m.rpos)')
            ->setMaxResults(1);
    }

    /**
     * Define the Doctrine query
     *
     * @param integer $menuId Menu Id
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFullTreeQuery($menuId)
    {
        $queryBuilder = $this->createQueryBuilder()
            ->addSelect('translations');

        $this->addSubTreeCondition($queryBuilder, $menuId ?: $this->getRootMenuId());

        return $queryBuilder;
    }

    /**
     * Get menus plain list (child)
     *
     * @param \XLite\Core\CommonCell $cnd    Search condition
     * @param integer                $menuId Menu id
     *
     * @return array
     */
    protected function getMenusPlainListChild($cnd, $menuId)
    {
        $list = [];
        $this->searchState['queryBuilder'] = $this->defineSubmenusQuery($cnd, $menuId);
        $menus = $this->searchResult();

        foreach ($menus as $menu) {
            $list[] = $menu;
            if ($menu->getRpos() > $menu->getLpos() + 1) {
                $list = array_merge($list, $this->getMenusPlainListChild($cnd, $menu->getId()));
            }
        }

        return $list;
    }

    /**
     * Define the Doctrine query
     *
     * @param \XLite\Core\CommonCell $cnd    Search condition
     * @param integer                $menuId Menu Id
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineSubmenusQuery($cnd, $menuId, $countOnly = false)
    {
        $this->searchState['queryBuilder']      = $this->createPureQueryBuilder();
        $this->searchState['currentSearchCnd']  = $cnd;
        $queryBuilder = $this->initializeQueryBuilder($this->searchState['queryBuilder']);

        $queryBuilder->addSelect('quickFlags')
            ->linkInner('m.quickFlags', 'quickFlags');

        foreach ($cnd as $key => $value) {
            $this->callSearchConditionHandler($value, $key, $queryBuilder, $countOnly);
        }

        if ($menuId) {
            $queryBuilder
                ->innerJoin('m.parent', 'cparent')
                ->andWhere('cparent.id = :parentId')
                ->setParameter('parentId', $menuId);
        } else {
            $queryBuilder
                ->andWhere('m.parent IS NULL');
        }

        return $queryBuilder;
    }

    /**
     * Define the Doctrine query
     *
     * @param integer $menuId Menu Id
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineSubtreeQuery($menuId)
    {
        return $this->defineFullTreeQuery($menuId)
            ->andWhere('m.id <> :id')
            ->setParameter('id', $menuId);
    }

    /**
     * Define the Doctrine query
     *
     * @param integer $menuId Menu Id
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineMenuPathQuery($menuId)
    {
        $queryBuilder = $this->createQueryBuilder();
        $menu = $this->getMenu($menuId);

        if ($menu) {
            $this->addSubTreeCondition($queryBuilder, $menuId, 'lpos', 1, $menu->getLpos());

            $this->addSubTreeCondition(
                $queryBuilder,
                $menuId,
                'rpos',
                $menu->getRpos(),
                $this->getMaxRightPos()
            );

            $queryBuilder->orderBy('m.lpos', 'ASC');
        } else {
            // :TODO: - throw exception
        }

        return $queryBuilder;
    }

    /**
     * Define the Doctrine query
     *
     * @param integer $menuId Menu Id
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineMenuDepthQuery($menuId)
    {
        return $this->defineMenuPathQuery($menuId)
            ->select('COUNT(m.id) - 1')
            ->setMaxResults(1);
    }

    /**
     * Define the Doctrine query
     *
     * @param integer $productId Product Id
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineSearchByProductIdQuery($productId)
    {
        return $this->createQueryBuilder()
            ->innerJoin('m.menuProducts', 'cp')
            ->innerJoin('cp.product', 'product')
            ->andWhere('product.product_id = :productId')
            ->setParameter('productId', $productId)
            ->addOrderBy('cp.orderby', 'ASC');
    }

    /**
     * Define the Doctrine query
     *
     * @param string  $index        Field name
     * @param integer $relatedIndex Related index value
     * @param integer $offset       Increment OPTIONAL
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineUpdateIndexQuery($index, $relatedIndex, $offset = 2)
    {
        $expr = new \Doctrine\ORM\Query\Expr();

        return $this->createPureQueryBuilder('m', false)
            ->update($this->_entityName, 'm')
            ->set('m.' . $index, 'm.' . $index . ' + :offset')
            ->andWhere($expr->gt('m.' . $index, ':relatedIndex'))
            ->setParameters(
                [
                    'offset'       => $offset,
                    'relatedIndex' => $relatedIndex,
                ]
            );
    }

    /**
     * Adds additional condition to the query for checking if menu is enabled
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder object
     * @param string                     $alias        Entity alias OPTIONAL
     *
     * @return void
     */
    protected function addEnabledCondition(\Doctrine\ORM\QueryBuilder $queryBuilder, $alias = null)
    {
        if ($this->getEnabledCondition()) {
            $queryBuilder
                ->andWhere(($alias ?: $this->getDefaultAlias()) . '.enabled = :enabled')
                ->setParameter('enabled', true);
        }
    }

    /**
     * Adds additional condition to the query to order menus
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder object
     * @param string                     $alias        Entity alias OPTIONAL
     *
     * @return void
     */
    protected function addOrderByCondition(\Doctrine\ORM\QueryBuilder $queryBuilder, $alias = null)
    {
        $queryBuilder
        // We need POS ordering since POS and LPOS are orderings inside the same one level.
        // LPOS is formed by the system (by adding into the level)
        // POS  is formed manually by admin and must have priority
            ->addOrderBy(($alias ?: $this->getDefaultAlias()) . '.position', 'ASC')
            ->addOrderBy(($alias ?: $this->getDefaultAlias()) . '.id', 'ASC');
    }

    /**
     * Adds additional condition to the query to order menus
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder object
     * @param string                     $alias        Entity alias OPTIONAL
     *
     * @return void
     */
    protected function addExcludeRootCondition(\Doctrine\ORM\QueryBuilder $queryBuilder, $alias = null)
    {
        $alias = $alias ?: $this->getDefaultAlias();

        $queryBuilder
                ->andWhere($alias . '.id <> :rootId')
                ->setParameter('rootId', $this->getRootMenuId());
    }

    /**
     * Return maximum index in the "nested set" tree
     *
     * @return integer
     */
    protected function getMaxRightPos()
    {
        if (!isset($this->maxRightPos)) {
            $this->maxRightPos = $this->defineMaxRightPosQuery()->getSingleScalarResult();
        }

        return $this->maxRightPos;
    }


    // {{{ Correct categories structure methods

    /**
     * Correct categories structure: lpos, rpos and depth fields
     *
     * @return void
     */
    public function recalculateTreeStructure()
    {
        $nestedSetCorrector = new \XLite\Logic\NestedSet(
            $this->getMenusRawData()
        );

        try {
            [$data, $quickFlags] = $nestedSetCorrector->recalculateStructure();
        } catch (\Exception $exception) {
            $this->getLogger('CDev-SimpleCMS')->error('Something is wrong in menus nestedSet recalculation: ' . $exception->getMessage());
            return;
        }

        if ($data) {
            foreach ($data as $catId => $d) {
                $query = 'UPDATE ' . $this->getTableName()
                    . ' SET ' . implode(', ', array_map(static function ($v) {
                        return $v . ' = ?';
                    }, array_keys($d)))
                    . ' WHERE id = ?';
                array_push($d, $catId);
                \XLite\Core\Database::getEM()->getConnection()->executeUpdate($query, array_values($d));
            }
        }

        if ($quickFlags) {
            $qfKeys = [
                'menu_id',
                'submenus_count_all',
                'submenus_count_enabled',
            ];

            foreach ($quickFlags as $qfData) {
                $qfQuery = 'REPLACE INTO '
                    . \XLite\Core\Database::getRepo('CDev\SimpleCMS\Model\Menu\QuickFlags')->getTableName()
                    . ' (' . implode(', ', $qfKeys) . ')'
                    . ' VALUES (' . implode(', ', array_fill(0, count($qfData), '?')) . ')';
                \XLite\Core\Database::getEM()->getConnection()->executeUpdate($qfQuery, array_values($qfData));
            }
        }
    }

    /**
     * Simplified search for menus data
     *
     * @return array
     */
    protected function getMenusRawData()
    {
        $fields = [
            'm.id as id',
            'm.parent_id',
            'm.lpos',
            'm.rpos',
            'm.depth',
            'm.position as pos',
            'm.enabled',
            'qf.submenus_count_all      as subnodes_count_all',
            'qf.submenus_count_enabled  as subnodes_count_enabled',
        ];

        $query = 'SELECT ' . implode(',', $fields) . ' FROM ' . $this->getTableName() . ' m '
            . ' LEFT JOIN ' . \XLite\Core\Database::getRepo('\CDev\SimpleCMS\Model\Menu\QuickFlags')->getTableName()
            . ' qf ON m.id = qf.menu_id '
            . ' ORDER BY m.id';

        return $this->getEntityManager()->getConnection()->fetchAllAssociative($query);
    }

    /**
     * @param array $data Data
     */
    public function loadFixtures(array $data, \XLite\Model\AEntity $parent = null, array $parentAssoc = [])
    {
        parent::loadFixtures($data, $parent, $parentAssoc);

        \XLite\Core\Database::getEM()->flush();
        \XLite\Core\Database::getEM()->clear();

        $this->recalculateTreeStructure();
    }

    /**
     * Prepare data for a new menu node
     *
     * @param \CDev\SimpleCMS\Model\Menu $entity Menu object
     * @param \CDev\SimpleCMS\Model\Menu $parent Parent menu object OPTIONAL
     *
     * @return \CDev\SimpleCMS\Model\Menu
     */
    protected function prepareNewMenuData(\CDev\SimpleCMS\Model\Menu $entity, \CDev\SimpleCMS\Model\Menu $parent = null)
    {
        if (!isset($parent)) {
            $parent = $entity->getParent();
        }
        if (!isset($parent)) {
            $parent = $this->getRootMenu();
            $entity->setLpos($parent->getRpos());
            $entity->setRpos($parent->getRpos() + 1);
            $parent->setRpos($parent->getRpos() + 2);
        } else {
            $entity->setLpos($parent->getLpos() + 1);
            $entity->setRpos($parent->getLpos() + 2);
        }

        $entity->setParent($parent);
        $entity->setDepth((int)$parent->getDepth() + 1);

        return $parent;
    }

    /**
     * Prepare data for a the "updateQuickFlags()" method
     *
     * @param integer $smAll     The "submenus_count_all" flag value
     * @param integer $smEnabled The "submenus_count_enabled" flag value
     *
     * @return array
     */
    protected function prepareQuickFlags($smAll, $smEnabled)
    {
        return [
            'submenus_count_all'     => $smAll,
            'submenus_count_enabled' => $smEnabled,
        ];
    }

    /**
     * Insert single entity
     *
     * @param \XLite\Model\AEntity|array $entity Data to insert OPTIONAL
     *
     * @return \XLite\Model\AEntity|array
     */
    protected function performInsert($entity = null)
    {
        $entity   = parent::performInsert($entity);
        $parentID = $entity->getParentId();

        if (empty($parentID)) {
            // Insert root menu
            $parent = $this->prepareNewMenuData($entity);
        } else {
            // Get parent for non-root menu
            $parent = $this->getMenu($parentID);
            if ($parent) {
                // Update indexes in the nested set
                $this->defineUpdateIndexQuery('lpos', $parent->getLpos())->execute();
                $this->defineUpdateIndexQuery('rpos', $parent->getLpos())->execute();

                // Create record in DB
                $this->prepareNewMenuData($entity, $parent);
            } else {
                throw new Exception(__METHOD__ . ': menu #' . $parentID . ' not found');
            }
        }

        // Update quick flags
        if (isset($parent) && $entity->getMenuId() == null) {
            $this->updateQuickFlags($parent, $this->prepareQuickFlags(1, $entity->getEnabled() ? 1 : -1));
        }

        return $entity;
    }

    /**
     * Delete single entity
     *
     * @param \XLite\Model\AEntity $entity Entity to detach
     *
     * @return void
     */
    protected function performDelete(\XLite\Model\AEntity $entity)
    {
        // Update quick flags
        if ($entity->getParent()) {
            $this->updateQuickFlags($entity->getParent(), $this->prepareQuickFlags(-1, $entity->getEnabled() ? -1 : 0));
        }

        // Root menu cannot be removed. Only its subtree
        $onlySubtree = ($entity->getMenuId() == $this->getRootMenuId());

        // Calculate some variables
        $right = $entity->getRpos() - ($onlySubtree ? 1 : 0);
        $width = $entity->getRpos() - $entity->getLpos() - ($onlySubtree ? 1 : -1);

        // Update indexes in the nested set.
        // FIXME: must not use execute()
        $this->defineUpdateIndexQuery('lpos', $right, -$width)->execute();
        $this->defineUpdateIndexQuery('rpos', $right, -$width)->execute();

        if ($onlySubtree) {
            $this->deleteInBatch($this->getSubtree($entity->getMenuId()), false);
        } else {
            parent::performDelete($entity);
        }
    }

    /**
     * Update single entity
     *
     * @param \XLite\Model\AEntity $entity Entity to use
     * @param array                $data   Data to save OPTIONAL
     *
     * @return void
     */
    protected function performUpdate(\XLite\Model\AEntity $entity, array $data = [])
    {
        if (!empty($data)) {
            $changeset = [
                'enabled' => [
                    $entity->getEnabled(),
                    $data['enabled'] ?? null
                ]
            ];
        } else {
            $uow = \XLite\Core\Database::getEM()->getUnitOfWork();
            $uow->computeChangeSets();
            $changeset = $uow->getEntityChangeSet($entity);
        }

        if (!$changeset && $entity->getPreviousState()->enabled !== null) {
            $changeset = [
                'enabled' => [
                    (bool) $entity->getPreviousState()->enabled,
                    $entity->getEnabled(),
                ]
            ];
        }

        if (
            isset($changeset['enabled'][0]) && isset($changeset['enabled'][1])
            && $entity->getParent()
            && ($changeset['enabled'][0] xor ((bool) $changeset['enabled'][1]))
        ) {
            $this->updateQuickFlags(
                $entity->getParent(),
                $this->prepareQuickFlags(0, ($changeset['enabled'][0] ? -1 : 1))
            );
        }

        parent::performUpdate($entity, $data);
    }

    /**
     * Prepare passed ID
     * NOTE: see E:0038835 (external BT)
     *
     * @param mixed $menuId Menu ID
     *
     * @return integer|void
     */
    protected function prepareMenuId($menuId)
    {
        return abs(intval($menuId)) ?: null;
    }

    /**
     * Update quick flags for a menu
     *
     * @param \CDev\SimpleCMS\Model\Menu $entity Menu
     * @param array                                   $flags  Flags to set
     *
     * @return void
     */
    protected function updateQuickFlags(\CDev\SimpleCMS\Model\Menu $entity, array $flags)
    {
        $quickFlags = $entity->getQuickFlags();

        if (!isset($quickFlags)) {
            $quickFlags = new \CDev\SimpleCMS\Model\Menu\QuickFlags();
            $quickFlags->setMenu($entity);
            $entity->setQuickFlags($quickFlags);
        }

        foreach ($flags as $name => $delta) {
            $name = \Includes\Utils\Converter::convertToUpperCamelCase($name);
            $quickFlags->{'set' . $name}($quickFlags->{'get' . $name}() + $delta);
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
    protected function prepareCndParent(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder->andWhere('m.parent = :parent')
                ->setParameter('parent', $value);
        }
    }

    /**
     * Remove all submenus
     *
     * @param integer $menuId Main menu
     *
     * @return void
     */
    public function deleteSubmenus($menuId)
    {
        $this->deleteInBatch($this->getSubtree($menuId));
    }

    /**
     * Return the menu enabled condition
     *
     * @return boolean
     */
    public function getEnabledCondition()
    {
        return !\XLite::isAdminZone();
    }

    /**
     * Return the menu membership condition
     *
     * @return boolean
     */
    public function getMembershipCondition()
    {
        return !\XLite::isAdminZone();
    }
}
