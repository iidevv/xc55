<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\View\FormField\Select;

use XPay\XPaymentsCloud\View\Card;

/**
 * Card number display format selector
 */
class CardNumberDisplayFormat extends \XLite\View\FormField\Select\Regular
{
    /**
     * Masked/unmasked display format
     */
    const FORMAT_UNMASKED = 'U';
    const FORMAT_MASKED   = 'M';

    /**
     * Return field value
     *
     * @return mixed
     */
    public function getValue()
    {
        $value = parent::getValue();

        if ($value !== static::FORMAT_MASKED) {
            $value = static::FORMAT_UNMASKED;
        }

        return $value;
    }

    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $sampleCard = array(
            'first6'      => '411111',
            'last4'       => '1111',
            'cardType'    => 'VISA',
            'expireMonth' => date('m'),
            'expireYear'  => date('Y', strtotime('+5 years')),
        );

        return array( 
            static::FORMAT_UNMASKED => $this->getWidget(
                array(
                    Card::PARAM_CARD                       => $sampleCard,
                    Card::PARAM_PLAIN_TEXT                 => true,
                    Card::PARAM_CARD_NUMBER_DISPLAY_FORMAT => static::FORMAT_UNMASKED,
                ),
                Card::class
            )->getContent(),
            static::FORMAT_MASKED => $this->getWidget(
                array(
                    Card::PARAM_CARD                       => $sampleCard,
                    Card::PARAM_PLAIN_TEXT                 => true,
                    Card::PARAM_CARD_NUMBER_DISPLAY_FORMAT => static::FORMAT_MASKED,
                ),
                Card::class
            )->getContent(),
        );
    }
}
