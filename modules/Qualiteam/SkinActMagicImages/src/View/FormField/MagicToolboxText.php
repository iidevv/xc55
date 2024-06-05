<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// vim: set ts=4 sw=4 sts=4 et:

namespace Qualiteam\SkinActMagicImages\View\FormField;

use Qualiteam\SkinActMagicImages\Model\Config;
use Qualiteam\SkinActMagicImages\Traits\MagicImagesTrait;
use XLite\Model\WidgetParam\TypeBool;
use XLite\Model\WidgetParam\TypeInt;
use XLite\View\FormField\Input\Text;

/**
 * Form text field
 */
class MagicToolboxText extends Text
{
    use MagicImagesTrait;

    /**
     * Widget param names
     */
    const PARAM_STATUS      = 'paramStatus';
    const PARAM_IS_READONLY = 'isReadOnly';

    /**
     * Option's statuses
     */
    const OPTION_IS_INACTIVE      = Config::OPTION_IS_INACTIVE;
    const OPTION_IS_ACTIVE        = Config::OPTION_IS_ACTIVE;
    const OPTION_IS_ALWAYS_ACTIVE = Config::OPTION_IS_ALWAYS_ACTIVE;

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();
        $this->widgetParams += [
            static::PARAM_STATUS      => new TypeInt('Param\'s status', $this->getDefaultStatus()),
            static::PARAM_IS_READONLY => new TypeBool('Param is read only', false),
        ];
    }

    /**
     * Get default status
     *
     * @return string
     */
    protected function getDefaultStatus()
    {
        return static::OPTION_IS_ALWAYS_ACTIVE;
    }

    /**
     * Get common attributes
     *
     * @return array
     */
    protected function getCommonAttributes()
    {
        $list = parent::getCommonAttributes();
        if ($this->isOptionInactive()) {
            $list['disabled'] = 'disabled';
        }
        if ($this->getParam(static::PARAM_IS_READONLY)) {
            $list['readonly'] = 'readonly';
        }

        return $list;
    }

    /**
     * Check - specidifed option is inactive or not
     *
     * @return boolean
     */
    protected function isOptionInactive()
    {
        return ($this->getParam(static::PARAM_STATUS) === static::OPTION_IS_INACTIVE);
    }

    /**
     * Check if status switcher is visible
     *
     * @return boolean
     */
    protected function isStatusSwitcherVisible()
    {
        return in_array($this->getParam(static::PARAM_STATUS), [static::OPTION_IS_INACTIVE, static::OPTION_IS_ACTIVE]);
    }

    /**
     * Get status switcher tag attributes
     *
     * @return array
     */
    protected function getStatusSwitcherTagAttributes()
    {
        return [
            'class'   => [
                'status-switcher',
                ($this->getParam(static::PARAM_STATUS) ? 'on' : 'off'),
            ],
            'onclick' => [
                'return false;',
            ],
            'title'   => [
                'switching between this option and the appropriate option from the \'Defaults\' tab',
            ],
        ];
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getModulePath() . '/form_field.twig';
    }
}
