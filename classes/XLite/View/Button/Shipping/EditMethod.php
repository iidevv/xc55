<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button\Shipping;

/**
 * Edit shipping method popup button
 */
class EditMethod extends \XLite\View\Button\APopupLink
{
    public const PARAM_METHOD = 'method';

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = 'button/js/shipping/edit_method.js';

        return $list;
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
            static::PARAM_METHOD => new \XLite\Model\WidgetParam\TypeObject(
                'Shipping method',
                null,
                true,
                'XLite\Model\Shipping\Method'
            ),
        ];
    }

    /**
     * Returns current shipping method
     *
     * @return \XLite\Model\Shipping\Method
     */
    protected function getMethod()
    {
        return $this->getParam(static::PARAM_METHOD);
    }

    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        $params = [
            'target'   => 'shipping_rates',
            'widget'   => 'XLite\View\Shipping\EditMethod'
        ];

        if ($this->getMethod()) {
            $params['methodId'] = $this->getMethod()->getMethodId();
        }

        return $params;
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        return $this->getParam(static::PARAM_STYLE) . ' edit-shipping-method-button';
    }

    /**
     * Return content for popup button
     *
     * @return string
     */
    protected function getButtonContent()
    {
        return $this->getParam(static::PARAM_LABEL);
    }
}
