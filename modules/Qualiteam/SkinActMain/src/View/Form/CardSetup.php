<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMain\View\Form;


use XCart\Extender\Mapping\Extender;
use XLite\Core\Request;

/**
 * @Extender\Mixin
 */
class CardSetup extends \XPay\XPaymentsCloud\View\Form\CardSetup
{

    protected function getDefaultParams()
    {
        $params = parent::getDefaultParams();

        if (Request::getInstance()->XpaymentsCardsFrame) {
            $params['XpaymentsCardsFrame'] = 1;
        }

        return $params;
    }

}