<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View;

use XCart\Extender\Mapping\Extender;

/**
 * Common widget extention.
 * This widget is used only to link additional css and js files to the page
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\Add2CartPopup")
 */
class Common extends \XC\Add2CartPopup\View\Common
{
    /**
     * Add CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $cart = $this->getCart();
        if (\CDev\Paypal\Main::isExpressCheckoutEnabled($cart)) {
            $list[] = 'modules/CDev/Paypal/button/add2cart_popup/style.css';
        }

        return $list;
    }
}
