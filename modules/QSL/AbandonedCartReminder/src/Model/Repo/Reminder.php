<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Model\Repo;

/**
 * Repository for Reminder model.
 */
class Reminder extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Allowed search parameters
     */
    public const SEARCH_ENABLED  = 'enabled';

    /**
     * Allowed sort criteria
     */
    public const SORT_BY_POSITION     = 'r.position';
    public const SORT_BY_MODE_ENABLED = 'r.enabled';
    public const SORT_BY_MODE_DELAY   = 'r.cronDelay';

    /**
     * Search enabled/disabled reminders.
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Condition data
     *
     * @return void
     */
    protected function prepareCndEnabled(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (is_numeric($value) || is_bool($value)) {
            $queryBuilder->andWhere($value ? '(r.enabled <> 0)' : '(r.enabled = 0)');
        }
    }
}
