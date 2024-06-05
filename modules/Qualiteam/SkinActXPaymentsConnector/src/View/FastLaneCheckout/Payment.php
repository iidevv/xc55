<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\View\FastLaneCheckout;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Config;

/**
 * Payment section
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\FastLaneCheckout")
 */
class Payment extends \XC\FastLaneCheckout\View\Sections\Payment
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        if ($this->getCart()->isXpcMethodsAvailable()) {
            if ('1.1' == Config::getInstance()->Qualiteam->SkinActXPaymentsConnector->xpc_api_version) {
                $list[] = 'modules/Qualiteam/SkinActXPaymentsConnector/checkout/style_old.css';

            } else {
                $list[] = 'modules/Qualiteam/SkinActXPaymentsConnector/checkout/style.css';
                $list[] = 'modules/Qualiteam/SkinActXPaymentsConnector/checkout/popover/jquery.webui-popover.min.css';
            }
        }

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
        $list['css'][] = 'modules/Qualiteam/SkinActXPaymentsConnector/cc_type_sprites.css';
        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if ($this->getCart()->isXpcMethodsAvailable()) {
            if ('1.1' == Config::getInstance()->Qualiteam->SkinActXPaymentsConnector->xpc_api_version) {
                // TODO: adapt script for old API
                // $list[] = 'modules/Qualiteam/SkinActXPaymentsConnector/checkout/script_old.js';

            } else {
                $list[] = 'modules/Qualiteam/SkinActXPaymentsConnector/iframe_common.js';
                $list[] = 'modules/Qualiteam/SkinActXPaymentsConnector/checkout/popover/jquery.webui-popover.min.js';
                $list[] = 'modules/Qualiteam/SkinActXPaymentsConnector/checkout/card_address.js';
                $list[] = 'modules/Qualiteam/SkinActXPaymentsConnector/checkout/FastLaneCheckout/script.js';
            }
        }

        return $list;
    }

}
