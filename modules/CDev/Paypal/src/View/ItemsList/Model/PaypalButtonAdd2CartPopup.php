<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\ItemsList\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\Add2CartPopup")
 */
class PaypalButtonAdd2CartPopup extends \CDev\Paypal\View\ItemsList\Model\PaypalButton
{
    /**
     * Types
     */
    public const TYPE_ADD2CART_POPUP = 'add2cart';

    /**
     * Get plain data
     *
     * @return array
     */
    protected function getPlainData()
    {
        $data = parent::getPlainData();

        $data[static::TYPE_ADD2CART_POPUP] = [
            'location'     => static::t('pp-button-location:Add2Cart popup'),
            'size'         => $this->getStyleValue(static::TYPE_ADD2CART_POPUP, 'size'),
            'color'        => $this->getStyleValue(static::TYPE_ADD2CART_POPUP, 'color'),
            'shape'        => $this->getStyleValue(static::TYPE_ADD2CART_POPUP, 'shape'),
        ];

        return $data;
    }
}
