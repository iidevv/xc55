<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\Model\Repo;

/**
 * Repository class for Brand model.
 */
class Brand extends \XLite\Model\Repo\ARepo
{
    /**
     * Allowed search parameters
     */
    public const SEARCH_ORDER_BY      = 'orderBy';
    public const SEARCH_WITH_PRODUCTS = 'withProducts';
    public const SEARCH_STARTS_WITH   = 'startsWith';
    public const SEARCH_SUBSTRING     = 'substring';

    /**
     * Allowed sort criterions
     */
    public const SORT_BY_PRODUCT_COUNT = 'productCount';
    public const SORT_BY_BRAND_NAME    = 't.name';
    public const SORT_BY_ADMIN_DEFINED = 'b.position';

    /**
     * Finds a brand model associated with the product.
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return \QSL\ShopByBrand\Model\Brand
     */
    public function findProductBrand(\XLite\Model\Product $product)
    {
        $attribute = \XLite\Core\Database::getRepo('XLite\Model\Attribute')->findBrandAttribute();

        $av = $attribute ? \XLite\Core\Database::getRepo('XLite\Model\AttributeValue\AttributeValueSelect')
            ->findProductAttributeValue(
                $product,
                $attribute
            ) : null;

        return $av ? $this->findOneByOption($av->getAttributeOption()) : null;
    }

    /**
     * Count the total number of enabled brands.
     *
     * @param \XLite\Core\CommonCell $cnd Search condition
     *
     * @return int
     */
    public function countEnabledBrands($conditions = null)
    {
        if (!$conditions) {
            $conditions = new \XLite\Core\CommonCell();
        }

        if (\XLite\Core\Config::getInstance()->QSL->ShopByBrand->hide_brands_without_products) {
            $conditions->{static::SEARCH_WITH_PRODUCTS} = true;
        }

        return $this->search($conditions, true);
    }

    /**
     * Find one as a sitemap link.
     *
     * @param int $position Position
     *
     * @return \QSL\ShopByBrand\Model\Brand
     */
    public function findOneAsSitemapLink($position)
    {
        $qb = $this->getQueryBuilderForSearch();
        $this->prepareCndWithProducts($qb);
        $qb->groupBy('b.brand_id')->setMaxResults(1)->setFirstResult($position);
        $result = $qb->getSingleResult();

        return (is_array($result) && !empty($result)) ? $result[0] : $result;
    }

    /**
     * Search brands having products in the category.
     *
     * @param int                    $categoryId          ID of the category to search in OPTIONAL
     * @param bool                   $hideWithoutProducts Whether to hide brands without products, or include them OPTIONAL
     * @param int                    $limit               The maximum number of brands to return OPTIONAL
     * @param string                 $order               Order in which brands should be returned OPTIONAL
     * @param \XLite\Core\CommonCell $cnd                 Search condition
     *
     * @return array Pairs of array(0=>$brand, 'productCount'=>$productCount).
     */
    public function getCategoryBrandsWithProductCount($categoryId = 0, $hideWithoutProducts = true, $limit = 0, $order = self::SORT_BY_BRAND_NAME, \XLite\Core\CommonCell $cnd = null)
    {
        // Select brands with related options and option translations (prevent lazy loading)
        $qb = $this->getQueryBuilderForSearch();

        // Preload brand image models
        $qb->leftJoin('b.image', 'i')
            ->addSelect('i');

        // Inner Join or Left Join products depending on whether we need info on brands without products, or not
        if ($hideWithoutProducts) {
            $this->prepareCndWithProducts($qb);
        } else {
            $qb->leftJoin('o.attributeValueS', 'av')->leftJoin('av.product', 'p');
        }

        // Count brand products and order brands by the number of products
        $qb->addSelect('COUNT(DISTINCT p.product_id) as productCount');

        // Order
        if ($order) {
            $qb->orderBy(
                $order,
                $order === self::SORT_BY_PRODUCT_COUNT ? 'DESC' : 'ASC'
            );
        }

        // Limit the number of brands to return
        if ($limit) {
            $qb->setMaxResults($limit);
        }

        // Drop products outside of the specified category
        $id = (int) $categoryId;
        if ($id) {
            // Link categories
            if ($hideWithoutProducts) {
                $qb->linkInner('p.categoryProducts', 'cp')->linkInner('cp.category', 'c');
            } else {
                $qb->leftJoin('p.categoryProducts', 'cp')->leftJoin('cp.category', 'c');
            }
            // Limit to the category and its subcategories
            \XLite\Core\Database::getRepo('XLite\Model\Category')->addSubTreeCondition($qb, $id);
        }

        $this->searchState['queryBuilder'] = $qb;
        $this->searchState['searchMode']   = static::SEARCH_MODE_ENTITIES;

        if ($cnd) {
            $this->searchState['currentSearchCnd'] = $cnd ?: new \XLite\Core\CommonCell();
            foreach ($this->searchState['currentSearchCnd'] as $key => $value) {
                $this->callSearchConditionHandler($value, $key);
            }
        }

        // Run the query
        return $this->searchBrandsResult($qb);
    }

    /**
     * Search result routine.
     *
     * @param \Doctrine\ORM\QueryBuilder $qb Query builder routine
     *
     * @return \Doctrine\ORM\PersistentCollection|integer
     */
    public function searchBrandsResult(\Doctrine\ORM\QueryBuilder $qb)
    {
        return $this->postprocessSearchResultQueryBuilder($qb)->groupBy('b.brand_id')->getResult();
    }

    /**
     * Prepare certain search condition.
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param bool                       $value        Condition data OPTIONAL
     */
    protected function prepareCndWithProducts(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = true)
    {
        if ($value) {
            $queryBuilder->linkInner('o.attributeValueS', 'av')
                ->linkInner('av.product', 'p')
                ->andWhere('p.enabled = :productEnabled')
                ->setParameter('productEnabled', true);

            if (\XLite\Core\Config::getInstance()->General->show_out_of_stock_products === 'directLink') {
                $this->dropOutOfStockProducts($queryBuilder);
            }
        }
    }

    /**
     * Drops out of stock products from the query.
     *
     * @param \Doctrine\ORM\QueryBuilder $qb Query builder to prepare
     */
    protected function dropOutOfStockProducts(\Doctrine\ORM\QueryBuilder $qb)
    {
        $orCnd = new \Doctrine\ORM\Query\Expr\Orx();
        $orCnd->add('p.inventoryEnabled = :inventoryEnabled');
        $orCnd->add('p.amount > :zeroAmount');

        $qb->andWhere($orCnd)
            ->setParameter('inventoryEnabled', false)
            ->setParameter('zeroAmount', 0);
    }

    /**
     * Common search
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getQueryBuilderForSearch()
    {
        $qb = parent::getQueryBuilderForSearch()
            ->leftJoin('QSL\ShopByBrand\Model\BrandProducts', 'bp', 'WITH', 'bp.brand = b.brand_id')
            ->linkInner('b.option', 'o')
            ->addSelect('o')
            ->linkInner('o.translations', 't')
            ->addSelect('t.name');
        if (!\XLite::isAdminZone()) {
            $qb->where('b.enabled = :enabled')
               ->setParameter('enabled', 1);
        }

        return $qb;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param                            $value
     * @param                            $countOnly
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function prepareCndStartsWith(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        return $queryBuilder
            ->andWhere('t.name LIKE :pattern')
            ->setParameter('pattern', $value . '%');
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param                            $value
     * @param                            $countOnly
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function prepareCndSubstring(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        return $queryBuilder
            ->andWhere('t.name LIKE :pattern')
            ->setParameter('pattern', '%' . $value . '%');
    }
}
