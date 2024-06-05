<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductQuestions\View\Button\Customer;

/**
 * Add review button widget
 *
 */
class AskQuestion extends \XLite\View\Button\APopupButton
{
    /*
     * Widget param names
     */
    public const PARAM_PRODUCT = 'product';

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/QSL/ProductQuestions/button/js/ask_question.js';

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
        $list[] = 'modules/QSL/ProductQuestions/ask_question/ask_question.css';

        return $list;
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    public function getCommonFiles()
    {
        $list = parent::getCommonFiles();
        $list[static::RESOURCE_JS][] = 'js/tooltip.js';

        return $list;
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
            self::PARAM_PRODUCT => new \XLite\Model\WidgetParam\TypeObject('Product', null, false, '\XLite\Model\Product'),
        ];
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        return $this->getParam(self::PARAM_PRODUCT);
    }

    /**
     * Get productId
     *
     * @return integer
     */
    protected function getProductId()
    {
        return $this->getProduct()
            ? $this->getProduct()->getProductId()
            : null;
    }

    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        return [
            'target'        => 'product_question',
            'product_id'    => $this->getProductId(),
            'return_target' => \XLite\Core\Request::getInstance()->target,
            'widget'        => '\QSL\ProductQuestions\View\AskQuestionDialog',
        ];
    }

    /**
     * Return CSS class
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' regular-main-button ask-question ';
    }
}
