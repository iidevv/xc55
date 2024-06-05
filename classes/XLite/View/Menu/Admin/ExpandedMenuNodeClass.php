<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Menu\Admin;

use XLite\Core\View\DynamicWidgetInterface;

/**
 * ExpandedMenuNodeClass dynamic widget renders css classes on a menu node
 */
class ExpandedMenuNodeClass extends \XLite\View\AView implements DynamicWidgetInterface
{
    public const PARAM_DECIDER = 'decider';
    public const PARAM_NAME = 'name';

    /**
     * Display widget with the default or overriden template.
     *
     * @param $template
     */
    protected function doDisplay($template = null)
    {
        $target = \XLite\Core\Request::getInstance()->target;
        $name = $this->getParam(static::PARAM_NAME);

        $classes = [];
        if ($this->getSelectedDecider()->isSelected($target, $name)) {
            $classes[] = 'active';
        }
        if ($this->getSelectedDecider()->isExpanded($target, $name)) {
            $classes[] = 'active';
            $classes[] = 'expanded';
        } else {
            $classes[] = 'collapsed';
        }

        if (count($classes) > 0) {
            echo implode(' ', array_unique($classes));
        }
    }

    /**
     * @return SelectedDecider
     */
    protected function getSelectedDecider()
    {
        return $this->getParam(static::PARAM_DECIDER);
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
            static::PARAM_DECIDER => new \XLite\Model\WidgetParam\TypeObject(
                'SelectedDecider',
                null,
                false,
                '\XLite\View\Menu\Admin\SelectedDecider'
            ),
            static::PARAM_NAME => new \XLite\Model\WidgetParam\TypeString('Name', null),
        ];
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return null;
    }
}
