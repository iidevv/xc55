<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\View\ItemsList\Table;

use QSL\AbandonedCartReminder\Model\Repo\Email as Repo;

/**
 * ItemsList widget for Cart E-mail Statistics.
 */
class CartEmailStatistics extends ATable
{
    /**
     * Widget param names
     */
    public const PARAM_DATE_RANGE = 'dateRange';

    /**
     * Return a list of CSS files required to display the widget properly.
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/QSL/AbandonedCartReminder/email_stats/email_stats.css';

        return $list;
    }

    /**
     * Return an array of search parameters.
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return [
            Repo::SEARCH_DATE_SENT_RANGE => static::PARAM_DATE_RANGE,
        ];
    }

    /**
     * Define columns structure.
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'title' => [
                static::COLUMN_NAME     => '',
                static::COLUMN_NO_WRAP  => true,
            ],
            'sent' => [
                static::COLUMN_NAME     => static::t('Sent (abandoned cart e-mails)'),
            ],
            'clicked' => [
                static::COLUMN_NAME     => static::t('Clicked (abandoned cart e-mails)'),
            ],
            'ordered' => [
                static::COLUMN_NAME     => static::t('Ordered (abandoned cart e-mails)'),
            ],
            'paid' => [
                static::COLUMN_NAME     => static::t('Paid (abandoned cart e-mails)'),
            ],
        ];
    }

    /**
     * Get container class.
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' cart-emails';
    }

    /**
     * Define widget parameters.
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_DATE_RANGE => new \XLite\Model\WidgetParam\TypeString('Date range', ''),
        ];
    }

    /**
     * Retrieves models and group them into table rows.
     *
     * @return void
     */
    protected function initRawPageData()
    {
        $repo = $this->getRepository();
        $cnd = $this->getSearchCondition();

        $this->rawPageData = [
            [
                'title'   => static::t('E-mails (abandoned cart e-mails)'),
                'sent'    => $repo->countSentEmails($cnd),
                'clicked' => $repo->countClickedEmails($cnd),
                'ordered' => $repo->countPlacedEmails($cnd),
                'paid'    => $repo->countPaidEmails($cnd),
            ],
            [
                'title'   => static::t('Carts (abandoned cart e-mails)'),
                'sent'    => $repo->countSentOrders($cnd),
                'clicked' => $repo->countClickedOrders($cnd),
                'ordered' => $repo->countPlacedOrders($cnd),
                'paid'    => $repo->countPaidOrders($cnd),
            ],
            [
                'title'   => static::t('Users (abandoned cart e-mails)'),
                'sent'    => $repo->countSentUsers($cnd),
                'clicked' => $repo->countClickedUsers($cnd),
                'ordered' => $repo->countPlacedUsers($cnd),
                'paid'    => $repo->countPaidUsers($cnd),
            ],
        ];
    }

    /**
     * Define repository name.
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'QSL\AbandonedCartReminder\Model\Email';
    }

    /**
     * Whether to display the footer list.
     *
     * @return boolean
     */
    protected function isFooterVisible()
    {
        return false;
    }

    /**
     * Get search panel widget class
     *
     * @return string
     */
    protected function getSearchPanelClass()
    {
        return 'QSL\AbandonedCartReminder\View\SearchPanel\Admin\CartEmailStats';
    }

    /**
     * Get sticky panel class
     */
    protected function getPanelClass()
    {
        return 'QSL\AbandonedCartReminder\View\StickyPanel\ItemsList\SingleSettingLink';
    }

    /**
     * Check - search panel is visible or not
     *
     * @return boolean
     */
    public function isSearchVisible()
    {
        return true;
    }

    /**
     * Get search values storage
     *
     * @param boolean $forceFallback Force fallback to session storage
     *
     * @return \XLite\View\ItemsList\ISearchValuesStorage
     */
    public static function getSearchValuesStorage($forceFallback = false)
    {
        $storage = parent::getSearchValuesStorage($forceFallback);

        $dates = $storage->getValue(static::PARAM_DATE_RANGE);
        if (!$dates) {
            // Fallback to the default period in order to prevent
            // heavy SQL queries for a long period (the whole dates)
            $dates = \XLite\View\FormField\Input\Text\DateRange::abcrConvertToString([
                strtotime('-4 weeks', strtotime('today')), // start of the day 4 weeks ago
                strtotime('tomorrow') - 1,                 // end of today
            ]);
            $storage->setValue(static::PARAM_DATE_RANGE, $dates);
        }

        return $storage;
    }
}
