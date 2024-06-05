<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\Logic\Import\Processor;

use XCart\Extender\Mapping\Extender;
use QSL\LoyaltyProgram\Model\RewardHistoryEvent;

/**
 * Import products
 * @Extender\Mixin
 */
abstract class Customers extends \XLite\Logic\Import\Processor\Customers
{
    /**
     * Get messages
     *
     * @return array
     */
    public static function getMessages()
    {
        return parent::getMessages()
            + [
                'USER-REWARD-POINTS-FMT' => 'Wrong format of the rewardPoints field',
            ];
    }

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['rewardPoints'] = [];

        return $columns;
    }

    /**
     * Verify 'rewardPoints' value.
     *
     * @param mixed $value  Value
     * @param array $column Column info
     */
    protected function verifyRewardPoints($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !preg_match('/^\d+$/', $value)) {
            $this->addWarning('USER-REWARD-POINTS-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Import 'rewardPoints' value
     *
     * @param \XLite\Model\Profile $model  Profile
     * @param string               $value  Value
     * @param array                $column Column info
     */
    protected function importRewardPointsColumn(\XLite\Model\Profile $model, $value, array $column)
    {
        if (trim($value) !== '') {
            $oldPoints = $model ? intval($model->getRewardPoints()) : intval($value);

            $adjustment = intval($value) - $oldPoints;

            if (abs($adjustment) > 0) {
                if ($adjustment > 0) {
                    $model->addRewardPoints($adjustment);
                    \XLite\Core\Database::getRepo('QSL\LoyaltyProgram\Model\RewardHistoryEvent')->registerEvent(
                        $model,
                        $adjustment,
                        RewardHistoryEvent::EVENT_REASON_IMPORT,
                        '',
                        null
                    );
                } elseif ($adjustment < 0) {
                    $model->redeemRewardPoints(abs($adjustment));
                    \XLite\Core\Database::getRepo('QSL\LoyaltyProgram\Model\RewardHistoryEvent')->registerEvent(
                        $model,
                        $adjustment,
                        RewardHistoryEvent::EVENT_REASON_IMPORT,
                        '',
                        null
                    );
                }
            }
        }
    }
}
