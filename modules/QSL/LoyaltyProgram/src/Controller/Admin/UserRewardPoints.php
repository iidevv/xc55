<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\Controller\Admin;

use XLite\Controller\Admin\ProfilePageTitleTrait;
use QSL\LoyaltyProgram\Model\RewardHistoryEvent;

class UserRewardPoints extends \XLite\Controller\Admin\AAdmin
{
    use ProfilePageTitleTrait;

    /**
     * Check ACL permissions.
     *
     * @return boolean
     */
    public function checkACL()
    {
        $profile = $this->getProfile();

        return parent::checkACL()
            || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage users')
            || ($profile && $profile->getProfileId() == \XLite\Core\Auth::getInstance()->getProfile()->getProfileId());
    }

    /**
     * Return the current page title (for the content area).
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getTitleString(
            $this->getProfile()
        );
    }

    /**
     * Check if current page is accessible.
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return parent::checkAccess() && $this->isOrigProfile();
    }

    /**
     * Get the profile being edited.
     *
     * @return \XLite\Model\Profile
     */
    public function getProfile()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Profile')->find($this->getProfileId());
    }

    /**
     * Get ID of the profile being edited.
     *
     * @return integer
     */
    protected function getProfileId()
    {
        return ($this->getRequestProfileId() && \XLite\Core\Auth::getInstance()->isAdmin())
            ? $this->getRequestProfileId()
            : \XLite\Core\Auth::getInstance()->getProfile()->getProfileId();
    }

    /**
     * Get profile ID from the request.
     *
     * @return integer|void
     */
    protected function getRequestProfileId()
    {
        return \XLite\Core\Request::getInstance()->profile_id;
    }

    /**
     * Adjust the number of user's reward points.
     */
    protected function doActionAdjust()
    {
        $request = \XLite\Core\Request::getInstance();

        $newPoints = (int) $request->reward_points;
        $comments  = $request->reward_comments;

        $profile   = $this->getProfile();
        $oldPoints = $profile ? intval($profile->getRewardPoints()) : $newPoints;

        $adjustment = $newPoints - $oldPoints;

        if (abs($adjustment) > 0) {
            if ($adjustment > 0) {
                $profile->addRewardPoints($adjustment);
                \XLite\Core\Database::getRepo('QSL\LoyaltyProgram\Model\RewardHistoryEvent')->registerEvent(
                    $profile,
                    $adjustment,
                    RewardHistoryEvent::EVENT_REASON_CUSTOM,
                    $comments,
                    null
                );
            } elseif ($adjustment < 0) {
                $profile->redeemRewardPoints(abs($adjustment));
                \XLite\Core\Database::getRepo('QSL\LoyaltyProgram\Model\RewardHistoryEvent')->registerEvent(
                    $profile,
                    $adjustment,
                    RewardHistoryEvent::EVENT_REASON_CUSTOM,
                    $comments,
                    null
                );
            }

            \XLite\Core\Database::getEM()->flush();
        }
    }

    /**
     * Return true if profile is not related with any order (i.e. it's an original profile).
     *
     * @return boolean
     */
    protected function isOrigProfile()
    {
        return $this->getProfile() && !($this->getProfile()->getOrder());
    }
}
