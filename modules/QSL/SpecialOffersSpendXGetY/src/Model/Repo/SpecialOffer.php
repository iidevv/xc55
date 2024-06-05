<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersSpendXGetY\Model\Repo;

use XCart\Extender\Mapping\Extender;
use QSL\SpecialOffersSpendXGetY\Logic\Order\SpecialOffer\SpendXGetNItemsDiscounted;

/**
 * Repository class for the Special Offer model.
 * @Extender\Mixin
 */
class SpecialOffer extends \QSL\SpecialOffersBase\Model\Repo\SpecialOffer
{
    public const SEARCH_VISIBLE_SXGY_CATEGORY = 'visibleSxgyCategory';
    public const SEARCH_ENABLED_SXGY_CATEGORY = 'enabledSxgyCategory';

    /**
     * @return \Doctrine\ORM\Query\Expr\Orx
     */
    protected function defineMembershipOrCnd()
    {
        $membershipCnd = parent::defineMembershipOrCnd();
        $offerTypeId = \XLite\Core\Database::getRepo('QSL\SpecialOffersBase\Model\OfferType')->findTypeIdByClass(SpendXGetNItemsDiscounted::class);
        if ($offerTypeId) {
            $membershipCnd->add('s.offerType = ' . $offerTypeId . ' AND (s.sxgyConditionMemberships = :empty OR s.sxgyConditionMemberships LIKE :membership)');
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
    protected function prepareCndVisibleSxgyCategory(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if (is_numeric($value) || is_bool($value)) {
            $queryBuilder->andWhere($value ? 's.sxgyPromoCategory <> 0' : 's.sxgyPromoCategory = 0');
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
    protected function prepareCndEnabledSxgyCategory(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            $queryBuilder->linkLeft('s.sxgyConditionCategories', 'sxgyCc')
                ->andWhere('(sxgyCc.id IS NULL) OR (sxgyCc.category = :sxgyCategory)')
                ->setParameter('sxgyCategory', $value);
        }
    }
}
