<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\FormField\Input;

class BuyNowButton extends \XLite\View\FormField\Input\Checkbox\OnOff
{
    /**
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'modules/CDev/Paypal/form_field/buy_now_button.twig';
    }

    /**
     * @return string
     */
    protected function getDir()
    {
        return '';
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/Paypal/form_field/buy_now_button.less';

        return $list;
    }
}
