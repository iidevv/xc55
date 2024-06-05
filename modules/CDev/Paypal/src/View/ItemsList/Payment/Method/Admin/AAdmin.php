<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\ItemsList\Payment\Method\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class AAdmin extends \XLite\View\ItemsList\Payment\Method\Admin\AAdmin
{
    /**
     * Defines JS files for widget
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/CDev/Paypal/items_list/payment/methods/controller.js';

        $api = \CDev\Paypal\Main::getRESTAPIInstance();
        if ($api->isInContextSignUpAvailable()) {
            $list[] = 'modules/CDev/Paypal/settings/signup.js';
        }

        return $list;
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/Paypal/items_list/payment/methods/style.css';

        return $list;
    }
}
