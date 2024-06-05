<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\FormField\Separator;

/**
 * \XLite\View\FormField\Separator\Regular
 */
class Selectable extends \XLite\View\FormField\Separator\ASeparator
{
    public const PARAM_GROUP_NAME = 'groupName';
    public const PARAM_SELECTED   = 'selected';

    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/selectable.css';

        return $list;
    }

    /**
     * Return name of the folder with templates
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/CDev/Paypal/form_field/separator';
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'selectable.twig';
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_GROUP_NAME => new \XLite\Model\WidgetParam\TypeString('Group name', $this->getDefaultName()),
            static::PARAM_SELECTED   => new \XLite\Model\WidgetParam\TypeBool('Selected', false),
        ];
    }
}
