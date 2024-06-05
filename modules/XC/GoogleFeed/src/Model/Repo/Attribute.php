<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GoogleFeed\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * Products repository
 * @Extender\Mixin
 */
abstract class Attribute extends \XLite\Model\Repo\Attribute
{
    /**
     * @param $ids
     * @param string $group
     */
    public function updateGroupInBatch($ids, $group = null)
    {
        $qb = $this->createPureQueryBuilder();
        $alias = $qb->getMainAlias();
        $qb->update('XLite\Model\Attribute', $alias)
            ->set("{$alias}.googleShoppingGroup", ':group')
            ->andWhere($qb->expr()->in("{$alias}.id", $ids))
            ->setParameter('group', $group);

        $qb->execute();
    }

    /**
     * @return array
     */
    public function getUsedGoogleGroupNames()
    {
        $qb = $this->createPureQueryBuilder();
        $alias = $qb->getMainAlias();
        $qb->select("DISTINCT {$alias}.googleShoppingGroup");

        $result = $qb->getResult();

        $result = array_map(static function ($elem) {
            return $elem['googleShoppingGroup'];
        }, $result);
        $result = array_filter($result, static function ($elem) {
            return !is_null($elem) && $elem !== '';
        });

        return $result;
    }

    /**
     * Find multiple attributes
     *
     * @param \XLite\Model\Product $product Product
     * @param array                $ids     Array of Ids
     *
     * @return array
     */
    public function getAttributesFeedData(\XLite\Model\Product $product, $ids)
    {
        $qb = $this->defineFindMultipleAttributesQuery($product, $ids);
        $qb->addSelect('a.googleShoppingGroup');

        return $ids
            ? $qb->getResult()
            : [];
    }
}
