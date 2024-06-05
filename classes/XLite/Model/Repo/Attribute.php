<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * Attributes repository
 */
class Attribute extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Allowable search params
     */
    public const SEARCH_PRODUCT          = 'product';
    public const SEARCH_PRODUCT_CLASS    = 'productClass';
    public const SEARCH_ATTRIBUTE_GROUP  = 'attributeGroup';
    public const SEARCH_TYPE             = 'type';
    public const SEARCH_NAME             = 'name';
    public const SEARCH_EXCLUDING_ID     = 'excludingId';

    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = 'position';

    /**
     * Find multiple attributes
     *
     * @param \XLite\Model\Product $product Product
     * @param array                $ids     Array of Ids
     *
     * @return array
     */
    public function findMultipleAttributes(\XLite\Model\Product $product, $ids)
    {
        return $ids
            ? $this->defineFindMultipleAttributesQuery($product, $ids)->getResult()
            : [];
    }

    /**
     * Define query for findMultipleAttributes() method
     *
     * @param \XLite\Model\Product $product Product
     * @param array                $ids     Attribute ID list
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindMultipleAttributesQuery(\XLite\Model\Product $product, array $ids)
    {
        $qb = $this->createQueryBuilder('a');

        return $qb->leftJoin('a.attribute_properties', 'ap', 'WITH', 'ap.product = :product')
            ->addSelect('MAX(ap.position) position')
            ->addInCondition('a.id', $ids)
            ->addGroupBy('a.id')
            ->setParameter('product', $product);
    }

    /**
     * Define items iterator
     *
     * @param integer $position Position OPTIONAL
     *
     * @return \Doctrine\ORM\Internal\Hydration\IterableResult
     */
    public function getRemoveGlobalAttributesDataIterator($position = 0)
    {
        return $this->defineRemoveGlobalAttributesDataQueryBuilder($position)
            ->setMaxResults(\XLite\Core\EventListener\RemoveData::CHUNK_LENGTH)
            ->iterate();
    }

    /**
     * Define remove data iterator query builder
     *
     * @param integer $position Position
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineRemoveGlobalAttributesDataQueryBuilder($position)
    {
        $qb = $this->defineRemoveDataQueryBuilder($position);

        return $qb->andWhere($qb->getMainAlias() . '.product IS NULL');
    }

    /**
     * Count items for remove data
     *
     * @return integer
     */
    public function countForRemoveGlobalAttributesData()
    {
        return (int) $this->defineCountForRemoveGlobalAttributesDataQuery()->getSingleScalarResult();
    }

    /**
     * Define query builder for COUNT query
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineCountForRemoveGlobalAttributesDataQuery()
    {
        $qb = $this->defineCountForRemoveDataQuery();

        return $qb->andWhere($qb->getMainAlias() . '.product IS NULL');
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition OPTIONAL
     *
     * @return void
     */
    protected function prepareCndProduct(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        if ($value) {
            $queryBuilder->andWhere('a.product = :attributeProduct')
                ->setParameter('attributeProduct', $value);
        } else {
            $queryBuilder->andWhere('a.product is null');
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition OPTIONAL
     *
     * @return void
     */
    protected function prepareCndProductClass(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        if ($value === null) {
            $queryBuilder->andWhere('a.productClass is null');
        } elseif (is_object($value) && get_class($value) != 'Doctrine\ORM\PersistentCollection') {
            $queryBuilder->andWhere('a.productClass = :productClass')
                ->setParameter('productClass', $value);
        } elseif ($value) {
            $ids = [];
            foreach ($value as $id) {
                if ($id) {
                    $ids[] = is_object($id) ? $id->getId() : $id;
                }
            }

            if ($ids) {
                $queryBuilder->linkInner('a.productClass')
                    ->andWhere($queryBuilder->expr()->in('productClass.id', $ids));
            }
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition OPTIONAL
     *
     * @return void
     */
    protected function prepareCndAttributeGroup(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        if ($value) {
            $queryBuilder->andWhere('a.attributeGroup = :attributeGroup')
                ->setParameter('attributeGroup', $value);
        } else {
            $queryBuilder->andWhere('a.attributeGroup is null');
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition OPTIONAL
     *
     * @return void
     */
    protected function prepareCndType(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        if ($value) {
            if (is_array($value)) {
                $queryBuilder->andWhere('a.type IN (\'' . implode("','", $value) . '\')');
            } else {
                $queryBuilder->andWhere('a.type = :type')
                    ->setParameter('type', $value);
            }
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition OPTIONAL
     *
     * @return void
     */
    protected function prepareCndNotType(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        if ($value) {
            if (is_array($value)) {
                $queryBuilder->andWhere('a.type NOT IN (\'' . implode("','", $value) . '\')');
            } else {
                $queryBuilder->andWhere('a.type != :type')
                    ->setParameter('type', $value);
            }
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition OPTIONAL
     *
     * @return void
     */
    protected function prepareCndName(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        if ($value) {
            // Add additional join to translations with default language code
            $this->addDefaultTranslationJoins(
                $queryBuilder,
                $this->getMainAlias($queryBuilder),
                'defaults',
                \XLite::getDefaultLanguage()
            );

            $condition = $queryBuilder->expr()->orX();

            $condition->add('translations.name = :name');
            $condition->add('defaults.name = :name');
            if (\XLite::getDefaultLanguage() !== \XLite\Core\Translation::DEFAULT_LANGUAGE) {
                // Add additional join to translations with default-default ('en' at the moment) language code
                $this->addDefaultTranslationJoins(
                    $queryBuilder,
                    $this->getMainAlias($queryBuilder),
                    'defaultDefaults',
                    'en'
                );
                $condition->add('defaultDefaults.name = :name');
            }

            $queryBuilder->andWhere($condition)
                ->setParameter('name', $value);
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
    protected function prepareCndExcludingId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder->andWhere('a.id <> :id')
                ->setParameter('id', $value);
        }
    }

    // }}}

    // {{{ Export routines

    /**
     * Define query builder for COUNT query
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineCountForExportQuery()
    {
        $qb = $this->createPureQueryBuilder();

        return $qb->select(
            'COUNT(DISTINCT ' . $qb->getMainAlias() . '.' . $this->getPrimaryKeyField() . ')'
        );
    }

    /**
     * Define export iterator query builder
     *
     * @param integer $position Position
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineExportIteratorQueryBuilder($position)
    {
        return $this->createPureQueryBuilder()
            ->setFirstResult($position)
            ->setMaxResults(\XLite\Core\EventListener\Export::CHUNK_LENGTH);
    }

    // }}}

    /**
     * Generate attribute values
     *
     * @param \XLite\Model\Product $product         Product
     * @param boolean              $useProductClass Use product class OPTIONAL
     *
     * @return void
     */
    public function generateAttributeValues(\XLite\Model\Product $product, $useProductClass = null)
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->productClass = $useProductClass ? $product->getProductClass() : null;
        $cnd->product = null;
        $cnd->type = [
            \XLite\Model\Attribute::TYPE_CHECKBOX,
            \XLite\Model\Attribute::TYPE_SELECT,
            \XLite\Model\Attribute::TYPE_TEXT,
            \XLite\Model\Attribute::TYPE_HIDDEN,
        ];
        foreach ($this->search($cnd) as $a) {
            $a->addToNewProduct($product);
        }
    }

    /**
     * Get identifiers list for specified query builder object
     *
     * @param \Doctrine\ORM\QueryBuilder $qb    Query builder
     * @param string                     $name  Name
     * @param mixed                      $value Value
     *
     * @return void
     */
    protected function addImportCondition(\Doctrine\ORM\QueryBuilder $qb, $name, $value)
    {
        if ($name == 'productClass' && is_string($value)) {
            $alias = $qb->getMainAlias();
            $qb->linkInner($alias . '.productClass')
                ->linkInner('productClass.translations', 'productClassTranslations')
                ->andWhere('productClassTranslations.name = :productClass')
                ->setParameter('productClass', $value);
        } else {
            parent::addImportCondition($qb, $name, $value);
        }
    }

    /**
     * @param \XLite\Model\Attribute $attribute
     * @return int
     */
    public function countProductsWithValues(\XLite\Model\Attribute $attribute)
    {
        $valuesRepo = \XLite\Core\Database::getRepo(\XLite\Model\Attribute::getAttributeValueClass($attribute->getType()));
        $qb = $valuesRepo->createPureQueryBuilder();
        $alias = $qb->getMainAlias();
        $qb->select('COUNT (DISTINCT ' . $alias . '.product)')
            ->where($alias . '.attribute = :attribute')
            ->setParameter('attribute', $attribute);

        return (int) $qb->getSingleScalarResult();
    }

    // {{{ findDuplicateNames

    /**
     * Find duplicate names
     *
     * @param \XLite\Model\ProductClass   $productClass
     * @param \XLite\Model\AttributeGroup $attrGroup
     *
     * @return array
     */
    public function findDuplicateNames($productClass, $attrGroup)
    {
        return $this->defineFindDuplicateNames($productClass, $attrGroup)->getResult();
    }

    /**
     * Define query builder for findDuplicateNames() method
     *
     * @param \XLite\Model\ProductClass $productClass
     * @param \XLite\Model\AttributeGroup $attrGroup
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindDuplicateNames($productClass, $attrGroup)
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

        if ($attrGroup) {
            $qb->andWhere('a.attributeGroup = :attrGroup')
                ->setParameter('attrGroup', $attrGroup);
        } else {
            $qb->andWhere('a.attributeGroup is null');
        }

        return $qb;
    }

    // }}}

    /**
     * Find entity by name (any language), product class and attribute group
     *
     * @param string                      $name
     * @param \XLite\Model\ProductClass   $productClass
     * @param \XLite\Model\AttributeGroup $attrGroup
     *
     * @return \XLite\Model\AttributeGroup|integer
     */
    public function findByNameAndProductClassAndAttrGroup($name, $productClass, $attrGroup)
    {
        return $this->defineFindByNameAndProductClassAndAttrGroupQuery($name, $productClass, $attrGroup)
            ->getResult();
    }

    /**
     * Define query builder for findByNameAndProductClassAndAttrGroup() method
     *
     * @param string                      $name
     * @param \XLite\Model\ProductClass   $productClass
     * @param \XLite\Model\AttributeGroup $attrGroup
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFindByNameAndProductClassAndAttrGroupQuery($name, $productClass, $attrGroup)
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

        if ($attrGroup) {
            $qb->andWhere('a.attributeGroup = :attrGroup')
                ->setParameter('attrGroup', $attrGroup);
        } else {
            $qb->andWhere('a.attributeGroup is null');
        }

        return $qb;
    }

    public function getAttributesWithValues(\XLite\Model\Product $product, $type)
    {
        $attributeValueClass = \XLite\Model\Attribute::getAttributeValueClass($type);

        if (class_exists($attributeValueClass)) {
            $qb = $this->createQueryBuilder();
            $alias = $qb->getMainAlias();
            $qb->join(
                $attributeValueClass,
                'av',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'av.attribute = ' . $alias
            );
            $qb->andWhere('av.product = :product')
                ->setParameter('product', $product);

            if (is_a($attributeValueClass, \XLite\Model\AttributeValue\AttributeValueText::class, true)) {
                $qb->linkLeft('av.translations', 'av_translations')
                    ->andWhere('av.editable = :true OR av_translations.value != :empty')
                    ->setParameter('true', true)
                    ->setParameter('empty', '');
            }

            $data = $qb->getResult();

            return $data;
        }

        return [];
    }
}
