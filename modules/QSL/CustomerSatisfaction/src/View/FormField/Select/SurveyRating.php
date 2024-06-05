<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\View\FormField\Select;

/**
 * Rating selection widget
 *
 */
class SurveyRating extends \XLite\View\FormField\Select\CheckboxList\ACheckboxList
{
    /**
     * Widget parameters names
     */
    public const PARAM_VALUE           = 'value';
    public const PARAM_FIELD_NAME      = 'field';
    public const PARAM_ALL_OPTION      = 'allOption';
    public const PARAM_PENDING_OPTION  = 'pendingOption';

    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            5 => \XLite\Core\Translation::lbl('X stars_5', ['count' => 5]),
            4 => \XLite\Core\Translation::lbl('X stars_4', ['count' => 4]),
            3 => \XLite\Core\Translation::lbl('X stars_3', ['count' => 3]),
            2 => \XLite\Core\Translation::lbl('X stars_2', ['count' => 2]),
            1 => \XLite\Core\Translation::lbl('X star_1', ['count' => 1]),
        ];
    }

    /**
     * Set common attributes
     *
     * @param array $attrs Field attributes to prepare
     *
     * @return array
     */
    protected function setCommonAttributes(array $attrs)
    {
        $list = parent::setCommonAttributes($attrs);
        $list['data-placeholder'] = static::t('Any rating');

        return $list;
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_FIELD_NAME => new \XLite\Model\WidgetParam\TypeString('Field', 'rating', false),
            static::PARAM_VALUE      => new \XLite\Model\WidgetParam\TypeString('Value', '%', false),
            static::PARAM_ALL_OPTION => new \XLite\Model\WidgetParam\TypeBool('Display All option', false, false),
        ];
    }
}
