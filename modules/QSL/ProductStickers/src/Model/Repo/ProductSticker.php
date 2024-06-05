<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductStickers\Model\Repo;

class ProductSticker extends \XLite\Model\Repo\Base\I18n
{
    public const DEFAULT_TEXT_COLOR = 'ffffff';
    public const DEFAULT_BG_COLOR   = '000000';

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
     * @param array                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndLimit(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value)
    {
        $queryBuilder->setFrameResults($value);
    }

    /**
     * Define cache cells
     *
     * @return array
     */
    protected function defineCacheCells()
    {
        $list = parent::defineCacheCells();

        $list['all'] = [];

        $list['enabled'] = [
            self::ATTRS_CACHE_CELL => ['enabled'],
        ];

        return $list;
    }

    /**
     * Find all languages
     *
     * @return array
     */
    public function findAllProductStickers()
    {
        return $this->defineAllProductStickersQuery()->getResult();
    }

    /**
     * Define query builder for findAllProductStickers()
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineAllProductStickersQuery()
    {
        return $this->createQueryBuilder('s')
            ->where('s.isLabel = :isLabel')
            ->setParameter('isLabel', false);
    }

    /**
     * Find all enabled languages
     *
     * @return array
     */
    public function findActiveProductStickers()
    {
        return $this->defineActiveProductStickersQuery()->getResult();
    }

    /**
     * Define query builder for findActiveProductStickers()
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineActiveProductStickersQuery()
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.enabled = :true')
            ->setParameter('true', true);
    }

    /**
     * Find product label by name (any language)
     *
     * @param string  $name       Name
     * @param boolean $onlyActive Search only in enabled product labels OPTIONAL
     * @param boolean $countOnly  Count only OPTIONAL
     *
     * @return \QSL\ProductStickers\Model\ProductSticker|integer
     */
    public function findOneByName($name, $onlyActive = true, $countOnly = false)
    {
        return $countOnly
            ? count($this->defineOneByNameQuery($name, $onlyActive)->getResult())
            : $this->defineOneByNameQuery($name, $onlyActive)->getSingleResult();
    }

    /**
     * Define query builder for findOneByName() method
     *
     * @param string  $name       Name
     * @param boolean $onlyActive Search only in enabled product labels
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineOneByNameQuery($name, $onlyActive)
    {
        $qb = $this->createQueryBuilder('l')
            ->andWhere('translations.name = :name')
            ->setParameter('name', $name)
            ->setMaxResults(1);

        if ($onlyActive) {
            $qb->andWhere('l.enabled = :true');
            $qb->setParameter('true', true);
        }

        return $qb;
    }

    /**
     * @param $product_stickers
     *
     * @return array
     */
    public function getListByIdOrName($product_stickers)
    {
        $ids = array_filter($product_stickers, static function ($item) {
            return is_numeric($item);
        });

        $result = $this->findByIds($ids);

        foreach ($product_stickers as $product_sticker) {
            if (is_numeric($product_sticker)) {
                continue;
            }

            $result[] = $this->createProductStickerByName($product_sticker);
        }

        return $result;
    }

    /**
     * @param $sticker_name
     *
     * @return \QSL\ProductStickers\Model\ProductSticker
     */
    public function createProductStickerByName($sticker_name)
    {
        $repo = \XLite\Core\Database::getRepo('QSL\ProductStickers\Model\ProductSticker');
        $sticker = $repo->findOneByName($sticker_name);
        if (!$sticker) {
            $sticker = new \QSL\ProductStickers\Model\ProductSticker();
            $sticker->setName($sticker_name);
            $sticker->setTextColor(self::DEFAULT_TEXT_COLOR);
            $sticker->setBgColor(self::DEFAULT_BG_COLOR);
            $repo->insert($sticker, false);
        }

        return $sticker;
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
        parent::performUpdate($entity, $data);

        if ($entity->getProduct()) {
            $entity->getProduct()->updateQuickData();
        }
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param                            $value
     * @param                            $countOnly
     */
    protected function prepareCndIsLabel(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $alias = $this->getMainAlias($queryBuilder);
        $queryBuilder->andWhere($alias . '.isLabel = :isLabel')
            ->setParameter('isLabel', $value);
    }

    /**
     * @return array
     */
    public function getLabels()
    {
        return $this->findBy(['isLabel' => true]) ?: [];
    }
}
