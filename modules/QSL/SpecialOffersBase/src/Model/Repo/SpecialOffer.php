<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBase\Model\Repo;

use XLite\Model\Profile;

/**
 * Repository class for the Special Offer model.
 */
class SpecialOffer extends \XLite\Model\Repo\Base\I18n
{
    public const SEARCH_ENABLED          = 'enabled';
    public const SEARCH_NAME             = 'name';
    public const SEARCH_ACTIVE           = 'active';
    public const SEARCH_VISIBLE_HOME     = 'visibleHome';
    public const SEARCH_VISIBLE_OFFERS   = 'visibleOffers';
    public const SEARCH_TYPE_ENABLED     = 'typeEnabled';
    public const SEARCH_MEMBERSHIP_ID    = 'membershipId';

    /**
     *
     * Allowed sort criteria
     */
    public const ORDER_BY_POSITION    = 's.position';
    public const ORDER_BY_NAME        = 's.name';
    public const ORDER_BY_ACTIVE_FROM = 's.activeFrom';
    public const ORDER_BY_ACTIVE_TILL = 's.activeTill';

    /**
     * Cached search criteria.
     *
     * @var \XLite\Core\CommonCell
     */
    protected $currentSearchCnd = null;

    /**
     * Returns active offers.
     *
     * @param Profile|null $profile
     *
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function findActiveOffers(Profile $profile = null)
    {
        return $this->search($this->getActiveOffersConditions($profile));
    }

    /**
     * Returns the default conditions to retrieve active offers.
     *
     * @param Profile|null $profile
     *
     * @return \XLite\Core\CommonCell
     */
    public function getActiveOffersConditions(Profile $profile = null)
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->{static::SEARCH_ENABLED} = true;
        $cnd->{static::SEARCH_ACTIVE} = true;
        $cnd->{static::P_ORDER_BY} = [static::ORDER_BY_POSITION, 'ASC'];
        $cnd->{static::SEARCH_MEMBERSHIP_ID} = null;

        if ($profile && $profile->getMembershipId()) {
            $cnd->{static::SEARCH_MEMBERSHIP_ID} = $profile->getMembershipId();
        }

        return $cnd;
    }

    /**
     * Create a new QueryBuilder instance that is prepopulated for this entity name
     *
     * @param string $alias   Table alias OPTIONAL
     * @param string $indexBy The index for the from.
     * @param string $code    Language code OPTIONAL
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    public function createQueryBuilder($alias = null, $indexBy = null, $code = null)
    {
        return parent::createQueryBuilder($alias, $indexBy, $code)
            ->linkInner('s.offerType', 't')
            ->addSelect('t');
    }

    /**
     * Prepare Enabled/Disabled search condition.
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndEnabled(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if (is_numeric($value) || is_bool($value)) {
            $queryBuilder->andWhere($value ? 's.enabled <> 0' : 's.enabled = 0');
        }
    }

    /**
     * Prepare "Visisble/Hidden on the home page" search condition.
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndVisibleHome(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if (is_numeric($value) || is_bool($value)) {
            $queryBuilder->andWhere($value ? 's.promoHome <> 0' : 's.promoHome = 0');
        }
    }

    /**
     * Prepare "Visisble/Hidden on the Current Offers page" search condition.
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndVisibleOffers(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if (is_numeric($value) || is_bool($value)) {
            $queryBuilder->andWhere($value ? 's.promoOffers <> 0' : 's.promoOffers = 0');
        }
    }

    /**
     * Prepare Offer Type Enabled/Disabled search condition.
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndTypeEnabled(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if (is_numeric($value) || is_bool($value)) {
            $queryBuilder->andWhere($value ? 't.enabled <> 0' : 't.enabled = 0');
        }
    }

    /**
     * Prepare Is Active search condition.
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndActive(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        // Skip special offers that has disabled offer types
        $queryBuilder->andWhere('t.enabled <> 0');

        if (is_numeric($value) || is_bool($value)) {
            $queryBuilder->andWhere(
                $value
                    ? '((s.activeFrom = 0) OR (s.activeFrom <= :current_time)) AND ((s.activeTill = 0) OR (s.activeTill >= :current_time))'
                    : '(s.activeFrom > :current_time) OR (s.activeTill < :current_time)'
            )->setParameter('current_time', \XLite\Core\Converter::time());
        }
    }

    /**
     * @return \Doctrine\ORM\Query\Expr\Orx
     */
    protected function defineMembershipOrCnd()
    {
        return new \Doctrine\ORM\Query\Expr\Orx();
    }

    /**
     * Prepare membership id search condition.
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndMembershipId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $membershipCnd = $this->defineMembershipOrCnd();
        if ($membershipCnd->getParts()) {
            $queryBuilder->andWhere($membershipCnd)
                ->setParameter('empty', serialize([]))
                ->setParameter('membership', serialize([]));

            if (!empty($value)) {
                $queryBuilder->setParameter('membership', '%"' . $value . '"%');
            }
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
    protected function prepareCndName(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            $queryBuilder->andWhere('s.name like :name_pattern')
                ->setParameter('name_pattern', sprintf('%%%s%%', $value));
        }
    }
}
