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
class SurveyStatuses extends \XLite\View\FormField\Select\Regular
{
    /**
     * Widget parameters names
     */
    public const PARAM_SHOW_ADDITIONAL = 'showAdditional';

    /**
     * Yes/No mode values
     */
    public const ALL = 'all';
    public const UNCLOSED  = 'unclosed';

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return \QSL\CustomerSatisfaction\Model\Survey::getSurveyStatuses();
    }

    /**
     * getOptions
     *
     * @return array
     */
    protected function getOptions()
    {
        if ($this->getParam(self::PARAM_SHOW_ADDITIONAL)) {
            $statuses = [
                static::ALL      => \XLite\Core\Translation::lbl('All Statuses'),
                static::UNCLOSED => \XLite\Core\Translation::lbl('All Unclosed')
            ];
        } else {
            $statuses = [];
        }

        return array_merge($statuses, $this->getParam(self::PARAM_OPTIONS));
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
            static::PARAM_SHOW_ADDITIONAL      => new \XLite\Model\WidgetParam\TypeBool('Show Additional', true, false),
        ];
    }
}
