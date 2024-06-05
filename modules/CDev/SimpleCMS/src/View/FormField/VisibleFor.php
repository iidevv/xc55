<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\View\FormField;

/**
 * "Visible for" selector
 *
 */
class VisibleFor extends \XLite\View\FormField\Inline\Base\Single
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/CDev/SimpleCMS/form_field/visible_for.js';

        return $list;
    }

    /**
     * getContainerClass
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' inline-visible-for';
    }

    /**
     * defineFieldClass
     *
     * @return string
     */
    protected function defineFieldClass()
    {
        return 'CDev\SimpleCMS\View\FormField\Select\VisibleFor';
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getViewTemplate()
    {
        return 'modules/CDev/SimpleCMS/form_field/visible_for_view.twig';
    }

    /**
     * Title for the visible for value
     *
     * @return string
     */
    protected function getVisibleFormValue()
    {
        $values = $this->getValues();
        $value = $this->getEntity()->getVisibleFor();

        return $values[$value] ?? \CDev\SimpleCMS\View\FormField\Select\VisibleFor::ANY_VISITORS;
    }

    /**
     * Get default options
     *
     * @return array
     */
    protected function getValues()
    {
        return [
            'AL' => static::t(\CDev\SimpleCMS\View\FormField\Select\VisibleFor::ANY_VISITORS),
            'A'  => static::t(\CDev\SimpleCMS\View\FormField\Select\VisibleFor::ANONYMOUS_ONLY),
            'L'  => static::t(\CDev\SimpleCMS\View\FormField\Select\VisibleFor::LOGGED_IN_ONLY),
        ];
    }
}
