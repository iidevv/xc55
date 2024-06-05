<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Banner\View\FormField\Select;

/**
 *    Banner System selector
 */
class SelectLocation extends \XLite\View\FormField\Select\Regular
{
    public const PARAM_AVAILABLE_BANNER_TYPES = 'availableBannerTypes';

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'modules/QSL/Banner/form_field/layout_type.less';

        return $list;
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = 'modules/QSL/Banner/form_field/js/layout_type.js';

        return $list;
    }

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            'MainColumn'      => static::t('Main sidebar menu column'),
            'SecondaryColumn' => static::t('Secondary sidebar menu column'),
            'WideTop'         => static::t('Top full-width'),
            'StandardTop'     => static::t('Top standard'),
            'WideBottom'      => static::t('Bottom full-width'),
            'StandardBottom'  => static::t('Bottom standard'),
        ];
    }

    /**
     * getOptions
     *
     * @return array
     */
    protected function getOptions()
    {
        $options = parent::getOptions();

        $result = [];
        foreach ($options as $type => $label) {
            $result[$type] = $label;
        }

        return $result;
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'modules/QSL/Banner/form_field/layout_banner_type.twig';
    }

    /**
     * @return string
     */
    protected function getDir()
    {
        return '';
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
            self::PARAM_AVAILABLE_BANNER_TYPES => new \XLite\Model\WidgetParam\TypeCollection(
                'Available banner types',
                [],
                false
            ),
        ];
    }

    /**
     * Get option classes
     *
     * @param mixed $value Value
     * @param mixed $text  Text
     *
     * @return array
     */
    protected function getOptionClasses($value, $text)
    {
        $result = 'layout-type ' . $value;
        if ($this->isOptionSelected($value)) {
            $result .= ' selected';
        }

        return $result;
    }

    /**
     * Returns layout type image
     *
     * @param string $value Layout type
     *
     * @return string
     */
    protected function getImage($value)
    {
        return $this->getSVGImage('modules/QSL/Banner/images/layout/' . $value . '.svg');
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible();
    }
}
