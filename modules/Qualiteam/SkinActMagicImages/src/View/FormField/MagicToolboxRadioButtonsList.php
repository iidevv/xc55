<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// vim: set ts=4 sw=4 sts=4 et:

namespace Qualiteam\SkinActMagicImages\View\FormField;

use Qualiteam\SkinActMagicImages\Classes\Magic360ModuleCoreClass;
use Qualiteam\SkinActMagicImages\Model\Config;
use Qualiteam\SkinActMagicImages\Traits\MagicImagesTrait;
use XLite\Model\WidgetParam\TypeInt;

/**
 * Radio buttons list
 */
class MagicToolboxRadioButtonsList extends \XLite\View\FormField\Select\RadioButtonsList\ARadioButtonsList
{
    use MagicImagesTrait;

    /**
     * Widget param names
     */
    const PARAM_STATUS = 'paramStatus';

    /**
     * Option's statuses
     */
    const OPTION_IS_INACTIVE      = Config::OPTION_IS_INACTIVE;
    const OPTION_IS_ACTIVE        = Config::OPTION_IS_ACTIVE;
    const OPTION_IS_ALWAYS_ACTIVE = Config::OPTION_IS_ALWAYS_ACTIVE;

    /**
     * Magic360 module core class
     *
     * @var   \Qualiteam\SkinActMagicImages\Classes\Magic360ModuleCoreClass
     *
     */
    static protected $toolCoreClass = null;

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();
        $this->widgetParams += [
            static::PARAM_STATUS => new TypeInt('Param\'s status', $this->getDefaultStatus()),
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
     * Get option attributes
     *
     * @param mixed $value Value
     * @param mixed $text  Text
     *
     * @return array
     */
    protected function getOptionAttributes($value, $text)
    {
        $attributes = parent::getOptionAttributes($value, $text);
        if ($this->isOptionInactive()) {
            $attributes['disabled'] = 'disabled';
        }

        return $attributes;
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
     * Get options list
     *
     * @return array
     */
    protected function getOptions()
    {
        if (null === self::$toolCoreClass) {
            self::$toolCoreClass = new Magic360ModuleCoreClass();
        }
        $options = [];
        foreach (self::$toolCoreClass->params->getValues($this->getName()) as $value) {
            $options[$value] = $value;
        }

        return $options;
    }

    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [];
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
