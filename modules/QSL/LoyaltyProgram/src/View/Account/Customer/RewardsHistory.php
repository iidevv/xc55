<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\View\Account\Customer;

use XCart\Extender\Mapping\ListChild;

/**
 * Widget displaying the history of rewards earned by a customer.
 *
 * @ListChild (list="customer.account.rewards", weight="20")
 */
class RewardsHistory extends \QSL\LoyaltyProgram\View\Account\Customer\ARewardsWidget
{
    /**
     * Cached information on reward events.
     *
     * @var \Doctrine\ORM\PersistentCollection
     */
    protected $events;

    /**
     * Get reward events associated with the profile.
     *
     * @return mixed
     */
    public function getRewardEvents()
    {
        if (!isset($this->events)) {
            $this->events = $this->getRepo()->searchByProfile($this->getProfile());
        }

        return $this->events;
    }

    /**
     * Format the number of points as a string.
     *
     * @param integer $points Number of points to format.
     *
     * @return string
     */
    public function formatEventPoints($points)
    {
        return ($points > 0) ? "+$points" : "$points";
    }

    /**
     * Format the event notes before displaying.
     *
     * @param string $notes Notes to format.
     *
     * @return string
     */
    public function formatEventNotes($notes)
    {
        return preg_replace_callback(
            '/#(\d+)/',
            static function ($matches) {
                $link = \XLite\Core\Converter::buildURL('order', '', ['order_number' => $matches[1]]);

                return '<a href="' . $link . '">' . $matches[0] . '</a>';
            },
            $notes
        );
    }

    /**
     * Get a list of CSS files required to display the widget properly.
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'modules/QSL/LoyaltyProgram/account/reward_history/reward_history.css';

        return $list;
    }

    /**
     * Return the default template for the widget.
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/LoyaltyProgram/account/reward_history/body.twig';
    }

    /**
     * Get repository for reward history events.
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getRepo()
    {
        return \XLite\Core\Database::getRepo('QSL\LoyaltyProgram\Model\RewardHistoryEvent');
    }

    /**
     * Check if widget is visible.
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && (0 < count($this->getRewardEvents()));
    }
}
