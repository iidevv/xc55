<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\Form;

/**
 * Paypal settings form
 */
class Settings extends \XLite\View\Form\AForm
{
    /**
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'paypal_settings';
    }

    /**
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'update';
    }

    /**
     * @return array
     */
    protected function getCommonFormParams()
    {
        $list = parent::getCommonFormParams();

        $list['method_id'] = \XLite\Core\Request::getInstance()->method_id;

        return $list;
    }

    /**
     * @return string
     */
    protected function getClassName()
    {
        return parent::getClassName() . ' use-inline-error';
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/Paypal/form/settings.less';

        return $list;
    }
}
