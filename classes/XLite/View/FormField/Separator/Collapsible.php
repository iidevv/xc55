<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Separator;

/**
 * \XLite\View\FormField\Separator\Collapsible
 */
class Collapsible extends \XLite\View\FormField\Separator\ASeparator
{
    public const PARAM_SECTION = 'section';
    public const PARAM_COLLAPSED = 'collapsed';

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_SECTION => new \XLite\Model\WidgetParam\TypeString(
                'Section',
                ''
            ),
            self::PARAM_COLLAPSED => new \XLite\Model\WidgetParam\TypeBool(
                'Collapsed',
                false
            ),
        ];
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'collapsible.twig';
    }

    protected function isCollapsed()
    {
        return $this->getParam(static::PARAM_COLLAPSED);
    }

    protected function getSection()
    {
        return 'section-' . $this->getParam(static::PARAM_SECTION);
    }
}
