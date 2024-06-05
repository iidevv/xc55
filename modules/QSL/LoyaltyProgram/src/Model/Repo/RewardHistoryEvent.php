<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\Model\Repo;

/**
 * Repository class for Reward History Events.
 */
class RewardHistoryEvent extends \XLite\Model\Repo\ARepo
{
    /**
     * Allowable search params
     */
    public const P_PROFILE = 'profile';

    /**
     * Default search order value
     */
    protected $defaultOrderBy = [
        'date' => false, // false means "DESC"
    ];

    /**
     * Search reward events for the user.
     *
     * @param \XLite\Model\Profile $profile User profile
     *
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function searchByProfile(\XLite\Model\Profile $profile)
    {
        return $this->search($this->defineSearchByProfileCnd($profile));
    }

    /**
     * Register an event.
     *
     * @param \XLite\Model\Profile $user         Shopper's profile.
     * @param integer              $rewardPoints The change to the shopper's reward points.
     * @param string               $reason       The code for the event reason.
     * @param string               $comment      Comments to the event OPTIONAL
     * @param \XLite\Model\Order   $order        Order associated with the event OPTIONAL
     */
    public function registerEvent(\XLite\Model\Profile $user, $rewardPoints, $reason, $comment = '', \XLite\Model\Order $order = null)
    {
        $event = new \QSL\LoyaltyProgram\Model\RewardHistoryEvent(
            [
                'date'    => \XLite\Core\Converter::time(),
                'user'    => $user,
                'reason'  => $reason,
                'points'  => $rewardPoints,
                'comment' => $comment,
                'order'   => $order,
            ]
        );

        if (\XLite\Core\Auth::getInstance()->getProfile()) {
            $event->setInitiator(\XLite\Core\Auth::getInstance()->getProfile());
        }

        $this->insert($event);
    }

    /**
     * Defines the conditions to search for reward event by profile.
     *
     * @param \XLite\Model\Profile $profile User profile
     *
     * @return \XLite\Core\CommonCell
     */
    protected function defineSearchByProfileCnd(\XLite\Model\Profile $profile)
    {
        $cnd                      = new \XLite\Core\CommonCell();
        $cnd->{static::P_PROFILE} = $profile;

        return $cnd;
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param \XLite\Model\Profile       $value        Profile
     */
    protected function prepareCndProfile(\Doctrine\ORM\QueryBuilder $queryBuilder, \XLite\Model\Profile $value)
    {
        if (!empty($value)) {
            $queryBuilder->andWhere('r.user = :user')->setParameter('user', $value);
        }
    }
}
