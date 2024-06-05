<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\View\FormField\Input;

/**
 * Rating field (rate product via stars)
 *
 */
class Stars extends \XLite\View\VoteBar
{
    public const PARAM_BAR_NAME   = 'barName';

    /**
     * getBarName
     *
     * @return string
     */
    public function getBarName()
    {
        return $this->getParam(self::PARAM_BAR_NAME);
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/QSL/CustomerSatisfaction/form_field/input/stars/stars.js';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/QSL/CustomerSatisfaction/form_field/input/stars/stars.css';

        return $list;
    }

    /**
     * Return field CSS class
     *
     * @return string
     */
    public function getClass()
    {
        return ' editable';
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
            self::PARAM_BAR_NAME => new \XLite\Model\WidgetParam\TypeString('Bar Name', $this->getDefaultBarName()),
        ];
    }

    /**
     * getDefaultBarName
     *
     * @return string
     */
    protected function getDefaultBarName()
    {

        return '';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/CustomerSatisfaction/form_field/input/stars/stars.twig';
    }
}
