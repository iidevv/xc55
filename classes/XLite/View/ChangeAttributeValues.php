<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Change attribute values widget
 *
 * @ListChild (list="center")
 */
class ChangeAttributeValues extends \XLite\View\AView
{
    /**
     * Widget parameter names
     */
    public const PARAM_ITEM = 'item';

    /**
     * Error message
     *
     * @var string
     */
    protected static $errorMessage;

    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'change_attribute_values';

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
        $list[] = 'change_attribute_values/style.css';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'change_attribute_values/controller.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'change_attribute_values/body.twig';
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
            self::PARAM_ITEM => new \XLite\Model\WidgetParam\TypeObject('Item', null, false, '\XLite\Model\OrderItem'),
        ];
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $result = parent::isVisible() && \XLite::getController()->getItem();

        if ($result) {
            $this->widgetParams[self::PARAM_ITEM]->setValue(\XLite::getController()->getItem());

            $result = $this->getParam(self::PARAM_ITEM)->hasAttributeValues();
        }

        return $result;
    }

    /**
     * Return true if error message is defined
     *
     * @return boolean
     */
    protected function hasErrorMessage()
    {
        return $this->getErrorMessage();
    }

    /**
     * Get error message
     *
     * @return string
     */
    protected function getErrorMessage()
    {
        if (static::$errorMessage === null) {
            static::$errorMessage = \XLite\Core\Session::getInstance()->error_message;
            \XLite\Core\Session::getInstance()->error_message = null;
        }

        return static::$errorMessage;
    }
}
