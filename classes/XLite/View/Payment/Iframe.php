<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Payment;

use XCart\Extender\Mapping\ListChild;

/**
 * IFRAME-based payment page
 *
 * @ListChild (list="center")
 */
class Iframe extends \XLite\View\AView
{
    /**
     * Common widget parameter names
     */
    public const PARAM_WIDTH  = 'width';
    public const PARAM_HEIGHT = 'height';
    public const PARAM_SRC    = 'src';
    public const PARAM_ADDITIONAL_ATTRIBUTES = 'additional_attributes';


    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $targets = parent::getAllowedTargets();

        $targets[] = 'checkoutPayment';

        return $targets;
    }

    /**
     * Set widget params
     *
     * @param array $params Handler params
     *
     * @return void
     */
    public function setWidgetParams(array $params)
    {
        if (is_array(\XLite\Core\Session::getInstance()->iframePaymentData)) {
            $params = array_merge($params, \XLite\Core\Session::getInstance()->iframePaymentData);
        }

        parent::setWidgetParams($params);
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Core\Session::getInstance()->iframePaymentData;
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
            self::PARAM_WIDTH  => new \XLite\Model\WidgetParam\TypeInt('Width', 400),
            self::PARAM_HEIGHT => new \XLite\Model\WidgetParam\TypeInt('Height', 400),
            self::PARAM_SRC    => new \XLite\Model\WidgetParam\TypeString('Source', ''),
            self::PARAM_ADDITIONAL_ATTRIBUTES => new \XLite\Model\WidgetParam\TypeCollection('Additional attributes', []),
        ];
    }

    /**
     * @return string
     */
    protected function getAdditionalAttributesString()
    {
        $attributes = $this->getParam(self::PARAM_ADDITIONAL_ATTRIBUTES);

        $result = '';
        foreach ($attributes as $name => $value) {
            $result .= $name . '="' . htmlspecialchars($value) . '" ';
        }

        return $result;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'payment/iframe.twig';
    }
}
