<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMain\View\Button;


use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class AddNewCard extends \XPay\XPaymentsCloud\View\Button\AddNewCard
{

    protected function prepareURLParams()
    {
        $params = parent::prepareURLParams();
        if (\XLite::getController() instanceof \Qualiteam\SkinActMain\Controller\Customer\XpaymentsCardsFrame) {
            $params['XpaymentsCardsFrame'] = 1;
        }
        return $params;
    }

}