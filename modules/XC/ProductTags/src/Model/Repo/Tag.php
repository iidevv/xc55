<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductTags\Model\Repo;

class Tag extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Allowable search params
     */
    public const SEARCH_NAME         = 'name';
    public const SEARCH_EXCLUDING_ID = 'excludingId';

    /**
     * Repository type
     *
     * @var string
     */
    // protected $type = self::TYPE_SECONDARY;

    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = 'position';

    /**
     * @var \XC\ProductTags\Model\Tag[]
     */
    protected $insertedCache = [];

    // {{{ Search

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array|string               $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndName(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            $queryBuilder->andWhere('translations.name = :name')
                ->setParameter('name', $value);
        }
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
            $queryBuilder->andWhere('t.id <> :id')
                ->setParameter('id', $value);
        }
    }

    // }}}

    // {{{ findAllTags

    /**
     * Find all tags
     *
     * @param boolean $countOnly Count only OPTIONAL
     *
     * @return array
     */
    public function findAllTags($countOnly = false)
    {
        return !$countOnly
            ? $this->createQueryBuilder()->getResult()
            : $this->count();
    }

    // }}}

    // {{{ findOneByName and findByName

    /**
     * Find tag by name (any language)
     *
     * @param string $name Name
     *
     * @return \XC\ProductTags\Model\Tag|null
     */
    public function findOneByName($name)
    {
        return $this->defineFindByNameQuery($name)->getSingleResult();
    }

    /**
     * Find tags by name (any language)
     *
     * @param string $name Name
     *
     * @return \XC\ProductTags\Model\Tag|null
     */
    public function findByName($name)
    {
        return $this->defineFindByNameQuery($name)->getResult();
    }

    /**
     * Define query builder for findOneByName() and findByName() method
     *
     * @param string $name Name
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindByNameQuery($name)
    {
        return $this->createQueryBuilder()
            ->andWhere('translations.name = :name')
            ->setParameter('name', $name);
    }

    // }}}

    // {{{ findDuplicateNames

    /**
     * Find duplicate names
     *
     * @return array
     */
    public function findDuplicateNames()
    {
        return $this->defineFindDuplicateNames()->getResult();
    }

    /**
     * Define query builder for findDuplicateNames() method
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindDuplicateNames()
    {
        return $this->getQueryBuilder()
            ->select('entityTranslation.name')
            ->from($this->_entityName . 'Translation', 'entityTranslation')
            ->groupBy('entityTranslation.name')
            ->having('COUNT(entityTranslation.name) > :count')
            ->setParameter('count', 1);
    }

    // }}}

    // {{{ Search tags by category

    /**
     * Find tags by category
     *
     * @param \XLite\Model\Category $category Category
     *
     * @return \XC\ProductTags\Model\Tag[]
     */
    public function findCountByCategory($category)
    {
        return $this->defineByCategoryQuery($category)
            ->select('COUNT(DISTINCT t.id)')
            ->getSingleScalarResult();
    }

    /**
     * Find tags by category
     *
     * @param \XLite\Model\Category $category Category
     *
     * @return \XC\ProductTags\Model\Tag[]
     */
    public function findByCategory($category)
    {
        return $this->defineByCategoryQuery($category)->getOnlyEntities();
    }

    /**
     * Define query builder for findByCategory() method
     *
     * @param \XLite\Model\Category $category Category
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineByCategoryQuery($category)
    {
        return $this->createQueryBuilder()
            ->linkLeft('t.products')
            ->linkLeft('products.categoryProducts')
            ->andWhere('categoryProducts.category = :category')
            ->setParameter('category', $category);
    }

    // }}}

    // {{{

    public function createTagByName($tag)
    {
        $tagObject = $this->insertedCache[$tag] ?? \XLite\Core\Database::getRepo('XC\ProductTags\Model\Tag')->findOneByName($tag);

        if (!$tagObject) {
            $tagObject = new \XC\ProductTags\Model\Tag();
            $tagObject->setName($tag);
            \XLite\Core\Database::getRepo('XC\ProductTags\Model\Tag')->insert($tagObject, false);
            $this->insertedCache[$tag] = $tagObject;
        }

        return $tagObject;
    }

    public function getListByIdOrName($tags)
    {
        $ids = array_filter($tags, static function ($item) {
            return is_numeric($item);
        });

        $result = $this->findByIds($ids);

        foreach ($tags as $tag) {
            if (is_numeric($tag)) {
                continue;
            }

            if (mb_strpos($tag, '_') === 0) {
                $tag = mb_substr($tag, 1);
            }

            $newTag = $this->createTagByName($tag);

            if ($newTag) {
                $result[] = $newTag;
            }
        }

        return $result;
    }

    // }}}
}
