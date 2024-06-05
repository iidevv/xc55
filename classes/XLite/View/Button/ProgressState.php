<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * Progress state button
 */
class ProgressState extends \XLite\View\Button\AButton
{
    /**
     * Widget parameters to use
     */
    public const PARAM_STATE   = 'state';
    public const PARAM_JS_CODE = 'jsCode';

    public const STATE_STILL       = 'still';
    public const STATE_IN_PROGRESS = 'in_progress';
    public const STATE_SUCCESS     = 'success';
    public const STATE_FAIL        = 'fail';

    /**
     * Get a list of JavaScript files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'button/js/progress-state.js';

        return $list;
    }

    /**
     * Return CSS files list
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'button/css/progress-state.less';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'button/progress-state.twig';
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
            static::PARAM_STATE   => new \XLite\Model\WidgetParam\TypeString('Initial state', static::STATE_STILL),
            static::PARAM_JS_CODE => new \XLite\Model\WidgetParam\TypeString('JS code', null, true),
        ];
    }

    /**
     * Get class
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass()
            . ' progress-state'
            . ' ' . $this->getParam(static::PARAM_STATE);
    }

    /**
     * JavaScript: return specified (or default) JS code to execute
     *
     * @return string
     */
    protected function getJSCode()
    {
        return $this->getParam(static::PARAM_JS_CODE);
    }

    /**
     * Get attributes
     *
     * @return array
     */
    protected function getAttributes()
    {
        $list = parent::getAttributes();

        return array_merge($list, $this->getLinkAttributes());
    }

    /**
     * Onclick specific attribute is added
     *
     * @return array
     */
    protected function getLinkAttributes()
    {
        return $this->getJSCode()
            ? ['onclick' => 'javascript: ' . $this->getJSCode()]
            : [];
    }
}
