<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Decorated Profile model.
 * @Extender\Mixin
 */
class Profile extends \XLite\Model\Profile
{
    /**
     * Merge flags
     */
    public const MERGE_REWARDS = 0x0800;

    /**
     * Number of rewards points on the user's account.
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $rewardPoints = 0;

    /**
     * Reward events registered for the shopper.
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="QSL\LoyaltyProgram\Model\RewardHistoryEvent", mappedBy="user")
     */
    protected $rewardEvents;

    /**
     * Reward events initiated by the shopper.
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="QSL\LoyaltyProgram\Model\RewardHistoryEvent", mappedBy="initiator")
     */
    protected $initiatedRewardEvents;

    /**
     * Check whether the user participates in the loyalty program, or not.
     *
     * @return boolean
     */
    public function isLoyaltyProgramEnabled()
    {
        return !$this->getAnonymous();
    }

    /**
     * Get the number of reward points on the user's account.
     *
     * @return integer
     */
    public function getRewardPoints()
    {
        return $this->rewardPoints;
    }

    /**
     * Set the number of reward points on the user's account.
     *
     * @param integer $points Number of reward points.
     *
     * @return integer Number of reward points on the user's account.
     */
    public function setRewardPoints($points)
    {
        $this->rewardPoints = $points;

        return $points;
    }

    /**
     * Add reward points to the user's account.
     *
     * @param integer $earnedPoints Number of reward points to add to the user's account.
     *
     * @return integer Number of reward points on the user's account after the operation.
     */
    public function addRewardPoints($earnedPoints)
    {
        return $this->setRewardPoints($this->getRewardPoints() + $earnedPoints);
    }

    /**
     * Deduct reward points from to the user's account.
     *
     * @param integer $redeemedPoints Number of reward points to deduct from the user's account.
     *
     * @return integer Number of reward points on the user's account after the operation.
     */
    public function redeemRewardPoints($redeemedPoints)
    {
        return $this->setRewardPoints($this->getRewardPoints() - $redeemedPoints);
    }

    /**
     * Check whether the user has reward points (above the specified level).
     *
     * @param integer $requiredPoints Number of reward points the user must have on his/her account OPTIONAL
     *
     * @return boolean
     */
    public function hasRewardPoints($requiredPoints = 0)
    {
        return $this->getRewardPoints() > $requiredPoints;
    }

    /**
     * Merge profile with another profile.
     *
     * @param \XLite\Model\Profile $profile Profile
     * @param integer              $flag    Peration flag OPTIONAL
     *
     * @return integer
     */
    public function mergeWithProfile(\XLite\Model\Profile $profile, $flag = self::MERGE_ALL)
    {
        $result = parent::mergeWithProfile($profile, $flag);

        // We move reward points only along with orders (otherwise order links in the reward history will be broken)
        if ($flag & static::MERGE_ORDERS) {
            // Move reward points
            $this->addRewardPoints($profile->getRewardPoints());
            $profile->setRewardPoints(0);

            // Move the reward history
            foreach ($profile->getRewardEvents() as $event) {
                $event->setUser($this);
                if ($event->getInitiator()) {
                    $event->setInitiator($this);
                }
            }

            $result |= static::MERGE_REWARDS;
        }

        return $result;
    }

    /**
     * Associates a reward event with the profile.
     *
     * @param \QSL\LoyaltyProgram\Model\RewardHistoryEvent $rewardEvents Reward event model
     *
     * @return Profile
     */
    public function addRewardEvents(\QSL\LoyaltyProgram\Model\RewardHistoryEvent $rewardEvents)
    {
        $this->rewardEvents[] = $rewardEvents;

        return $this;
    }

    /**
     * Returns reward events associated with the profile.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRewardEvents()
    {
        return $this->rewardEvents;
    }

    /**
     * Associates a reward event initiated by the user.
     *
     * @param \QSL\LoyaltyProgram\Model\RewardHistoryEvent $initiatedRewardEvents Reward event model
     *
     * @return Profile
     */
    public function addInitiatedRewardEvents(\QSL\LoyaltyProgram\Model\RewardHistoryEvent $initiatedRewardEvents)
    {
        $this->initiatedRewardEvents[] = $initiatedRewardEvents;

        return $this;
    }

    /**
     * Returns reward events initiated by the user.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInitiatedRewardEvents()
    {
        return $this->initiatedRewardEvents;
    }

    /**
     * Clone
     *
     * @return \XLite\Model\Profile
     */
    public function cloneEntity()
    {
        $newProfile = parent::cloneEntity();
        $newProfile->setRewardPoints(0);

        return $newProfile;
    }
}
