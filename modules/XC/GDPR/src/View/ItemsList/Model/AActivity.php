<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\View\ItemsList\Model;

use XLite\Core\Converter;
use XC\GDPR\Model\Activity;

class AActivity extends \XLite\View\ItemsList\Model\Table
{
    protected function defineRepositoryName()
    {
        return '\XC\GDPR\Model\Activity';
    }

    public function getCSSFiles()
    {
        return array_merge(parent::getCSSFiles(), [
            'modules/XC/GDPR/activities.less',
        ]);
    }

    protected function defineColumns()
    {
        return [
            'description' => [
                static::COLUMN_NAME    => $this->getMainHeadTitle(),
                static::COLUMN_ORDERBY => 100,
            ],
            'date'        => [
                static::COLUMN_NAME     => static::t('Initial access'),
                static::COLUMN_ORDERBY  => 200,
                static::COLUMN_TEMPLATE => 'modules/XC/GDPR/activities/date.cell.twig',
            ],
        ];
    }

    protected function getMainHeadTitle()
    {
        return '';
    }

    /**
     * Format description column
     *
     * @param Activity $activity
     *
     * @return string
     */
    protected function getDescriptionColumnValue(Activity $activity)
    {
        $details = $activity->getDetails();

        return implode('<br>', array_map(static function ($key, $value) {
            return "{$key}: {$value}";
        }, array_keys($details), $details));
    }

    /**
     * Preprocess profile
     *
     * @param mixed                            $profile     Profile
     * @param array                            $column      Column data
     * @param \XLite\Model\Payment\Transaction $transaction Transaction
     *
     * @return string
     */
    protected function preprocessDate($timestamp, array $column, Activity $activity)
    {
        if ($timestamp) {
            return Converter::formatTime($timestamp);
        }

        return '—';
    }

    protected function isPanelVisible()
    {
        return false;
    }

    protected function isDisplayWithEmptyList()
    {
        return false;
    }
}
