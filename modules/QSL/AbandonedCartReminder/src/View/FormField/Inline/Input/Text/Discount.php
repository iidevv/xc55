<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\View\FormField\Inline\Input\Text;

/**
 * Inline field for editing the coupon amount (nullable numeric followed with an optional % character).
 */
class Discount extends \XLite\View\FormField\Inline\Base\Single
{
    /**
     * Register CSS files.
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/QSL/AbandonedCartReminder/form_field/inline/input/text/discount.css';

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
        $list[] = 'modules/QSL/AbandonedCartReminder/form_field/inline/input/text/discount.js';

        return $list;
    }

    /**
     * Define form field.
     *
     * @return string
     */
    protected function defineFieldClass()
    {
        return 'QSL\AbandonedCartReminder\View\FormField\Input\Text\Discount';
    }

    /**
     * Get the view value.
     *
     * @param array $field Field
     *
     * @return mixed
     */
    protected function getViewValue(array $field)
    {
        $value = parent::getViewValue($field);
        $sign = 0 <= $value ? '' : '&minus;&#8197';

        return (is_numeric(strpos('%', $value)) || !$value)
            ? $value
            : ($sign . $this->getCurrency()->formatValue(abs($value)));
    }

    /**
     * Get container class.
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' inline-discount';
    }

    /**
     * Get view template.
     *
     * @return string
     */
    protected function getViewTemplate()
    {
        return 'modules/QSL/AbandonedCartReminder/form_field/inline/input/text/discount.twig';
    }

    /**
     * Get currency.
     *
     * @return \XLite\Model\Currency
     */
    protected function getCurrency()
    {
        return \XLite::getInstance()->getCurrency();
    }
}
