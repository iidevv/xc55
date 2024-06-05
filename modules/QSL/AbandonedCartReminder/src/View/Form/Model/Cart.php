<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// TODO: remove this file????

namespace QSL\AbandonedCartReminder\View\Form\Model;

/**
 * Form class for the Edit Cart page.
 */
class Cart extends \XLite\View\Form\AForm
{
    /**
     * Register CSS files.
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/QSL/AbandonedCartReminder/cart/style.css';

        return $list;
    }

    /**
     * Return default value for the "target" parameter.
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'cart';
    }

    /**
     * Return default value for the "action" parameter.
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'update';
    }

    /**
     * Get default CSS class name.
     *
     * @return string
     */
    protected function getDefaultClassName()
    {
        return trim(parent::getDefaultClassName() . ' validationEngine cart');
    }

    /**
     * Return list of the form default parameters.
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        return [
            'id' => \XLite\Core\Request::getInstance()->id,
        ];
    }
}
