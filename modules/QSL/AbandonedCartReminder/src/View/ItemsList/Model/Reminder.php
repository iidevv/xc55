<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\View\ItemsList\Model;

/**
 * ItemsList widget for Cart Reminders.
 */
class Reminder extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Return search parameters.
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return [];
    }

    /**
     * Get a list of CSS files required to display the widget properly.
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/QSL/AbandonedCartReminder/cart_reminders/style.css';

        return $list;
    }

    /**
     * Register JS files.
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/QSL/AbandonedCartReminder/cart_reminders/switcher.js';
        $list[] = 'modules/QSL/AbandonedCartReminder/cart_reminders/coupon_checker.js';

        return $list;
    }

    /**
     * Define columns structure.
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'name' => [
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Name'),
                static::COLUMN_NO_WRAP  => true,
                static::COLUMN_LINK     => 'cart_reminder',
                static::COLUMN_ORDERBY  => 100,
            ],
            'cronDelay' => [
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Delay (hours)'),
                static::COLUMN_CLASS    => 'XLite\View\FormField\Inline\Input\Text\Integer',
                static::COLUMN_PARAMS    => [
                    \XLite\View\FormField\Input\Text\Integer::PARAM_MIN => 0,
                ],
                static::COLUMN_ORDERBY  => 200,
            ],
            'coupon' => [
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Coupon discount'),
                static::COLUMN_CLASS    => 'XLite\View\FormField\Inline\Input\PriceOrPercent',
                static::COLUMN_ORDERBY  => 300,
            ],
            'couponExpire' => [
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Expire (days)'),
                static::COLUMN_CLASS    => 'XLite\View\FormField\Inline\Input\Text\Integer',
                static::COLUMN_PARAMS    => [
                    \XLite\View\FormField\Input\Text\Integer::PARAM_MIN => 1,
                ],
                static::COLUMN_ORDERBY  => 400,
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
        return 'QSL\AbandonedCartReminder\Model\Reminder';
    }

    /**
     * @return integer
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_MOVE;
    }

    /**
     * Return position of the Create button.
     *
     * @return integer
     */
    protected function isCreation()
    {
        return static::CREATE_INLINE_TOP;
    }

    /**
     * Return URl for the Create button.
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return \XLite\Core\Converter::buildUrl('cart_reminder');
    }

    /**
     * Return label for the Create button.
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'New reminder';
    }

    /**
     * Mark list as removable.
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return true;
    }

    /**
     * Mark list as switchable (enable / disable).
     *
     * @return boolean
     */
    protected function isSwitchable()
    {
        return true;
    }

    /**
     * Get switcher field
     *
     * @return array
     */
    protected function getSwitcherField()
    {
        return [
            'class'  => 'QSL\AbandonedCartReminder\View\FormField\Inline\Input\Checkbox\Switcher\AutoRemind',
            'name'   => 'enabled',
            'params' => [],
        ];
    }

    /**
     * Get container class.
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' item-list-cart-reminders';
    }

    /**
     * Get panel class.
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'QSL\AbandonedCartReminder\View\StickyPanel\ItemsList\Reminder';
    }

    /**
     * Return params list to use for search.
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        foreach (static::getSearchParams() as $modelParam => $requestParam) {
            $paramValue = $this->getParam($requestParam);

            if ($paramValue !== '' && $paramValue !== 0) {
                $result->$modelParam = $paramValue;
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getOrderBy()
    {
        return [\QSL\AbandonedCartReminder\Model\Repo\Reminder::SORT_BY_POSITION, 'ASC'];
    }
}
