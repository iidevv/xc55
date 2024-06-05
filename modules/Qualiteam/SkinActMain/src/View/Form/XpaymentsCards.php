<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMain\View\Form;

use XCart\Extender\Mapping\Extender;


/**
 * @Extender\Mixin
 */
class XpaymentsCards extends \XPay\XPaymentsCloud\View\Form\XpaymentsCards
{

    protected function getDefaultParams()
    {
        $params = parent::getDefaultParams();

        if (\XLite::getController() instanceof \Qualiteam\SkinActMain\Controller\Customer\XpaymentsCardsFrame) {
            $params['XpaymentsCardsFrame'] = 1;
        }

        return $params;
    }


}