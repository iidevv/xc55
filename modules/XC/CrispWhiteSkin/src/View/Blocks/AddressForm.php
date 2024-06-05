<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View\Blocks;

use XCart\Extender\Mapping\Extender;

/**
 * Checkout Address form
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\FastLaneCheckout")
 */
abstract class AddressForm extends \XC\FastLaneCheckout\View\Blocks\AddressForm
{
    /**
     * Get field placeholder
     *
     * @param string $name File short name
     *
     * @return string
     */
    protected function getFieldPlaceholder($name)
    {
        switch ($name) {
            case 'firstname':
                $result = static::t('Joe');
                break;

            case 'lastname':
                $result = static::t('Public');
                break;

            case 'street':
                $result = static::t('1000 Main Street');
                break;

            case 'city':
                $result = static::t('Anytown');
                break;

            case 'custom_state':
                $result = static::t('Anyland');
                break;

            case 'zipcode':
                $result = static::t('90001');
                break;

            case 'phone':
                $result = static::t('+15550000000');
                break;

            case 'email':
                $result = static::t('email@example.com');
                break;

            default:
                $result = '';
        }

        return $result;
    }
}
