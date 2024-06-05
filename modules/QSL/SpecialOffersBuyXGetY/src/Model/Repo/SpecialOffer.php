<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBuyXGetY\Model\Repo;

use XCart\Extender\Mapping\Extender;
use QSL\SpecialOffersBuyXGetY\Logic\Order\SpecialOffer\GetMOfNItemsDiscounted;

/**
 * Repository class for the Special Offer model.
 * @Extender\Mixin
 */
class SpecialOffer extends \QSL\SpecialOffersBase\Model\Repo\SpecialOffer
{
    public const SEARCH_VISIBLE_BXGY_CATEGORY = 'visibleBxgyCategory';
    public const SEARCH_ENABLED_BXGY_CATEGORY = 'enabledBxgyCategory';

    /**
     * @return \Doctrine\ORM\Query\Expr\Orx
     */
    protected function defineMembershipOrCnd()
    {
        $membershipCnd = parent::defineMembershipOrCnd();
        $offerTypeId = \XLite\Core\Database::getRepo('QSL\SpecialOffersBase\Model\OfferType')->findTypeIdByClass(GetMOfNItemsDiscounted::class);
        if ($offerTypeId) {
            $membershipCnd->add('s.offerType = ' . $offerTypeId . ' AND (s.bxgyConditionMemberships = :empty OR s.bxgyConditionMemberships LIKE :membership)');
        }

        return $membershipCnd;
    }

    /**
     * Prepare "Visisble/Hidden on category pages" search condition.
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndVisibleBxgyCategory(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if (is_numeric($value) || is_bool($value)) {
            $queryBuilder->andWhere($value ? 's.bxgyPromoCategory <> 0' : 's.bxgyPromoCategory = 0');
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array|string               $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndEnabledBxgyCategory(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            $queryBuilder->linkLeft('s.bxgyConditionCategories', 'bxgyCc')
                ->andWhere('(bxgyCc.id IS NULL) OR (bxgyCc.category = :bxgyCategory)')
                ->setParameter('bxgyCategory', $value);
        }
    }
}
