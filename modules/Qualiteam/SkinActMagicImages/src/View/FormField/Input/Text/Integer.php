<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// vim: set ts=4 sw=4 sts=4 et:

namespace Qualiteam\SkinActMagicImages\View\FormField\Input\Text;

/**
 * Integer
 */
class Integer extends \XLite\View\FormField\Input\Text\Integer
{
    /**
     * Widget param names
     */
    const PARAM_DISABLED = 'disabled';

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();
        $this->widgetParams += [
            static::PARAM_DISABLED => new \XLite\Model\WidgetParam\TypeBool('Disabled', false),
        ];
    }

    /**
     * Get common attributes
     *
     * @return array
     */
    protected function getCommonAttributes()
    {
        $list = parent::getCommonAttributes();

        if ($this->isDisabled()) {
            $list['disabled'] = 'disabled';
        }

        return $list;
    }

    /**
     * Returns disabled state
     *
     * @return boolean
     */
    protected function isDisabled()
    {
        return $this->getParam(static::PARAM_DISABLED);
    }

    /**
     * Get default wrapper class
     *
     * @return string
     */
    protected function getDefaultWrapperClass()
    {
        $wrapperClass = parent::getDefaultWrapperClass();
        $wrapperClass = str_replace(' magictoolbox-magic360-input-text-integer', '', $wrapperClass);
        $wrapperClass = str_replace(' input-text-integer', '', $wrapperClass);
        $wrapperClass .= ' input-text-integer magictoolbox-magic360-input-text-integer';

        return $wrapperClass;
    }
}
