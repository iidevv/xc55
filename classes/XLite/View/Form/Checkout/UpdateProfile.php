<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Checkout;

use XLite;
use XLite\Core\Request;
use XLite\Core\Validator\HashArray;
use XLite\Core\Validator\Pair\APair;
use XLite\Core\Validator\Pair\CountryState;
use XLite\Core\Validator\Pair\Simple;
use XLite\Core\Validator\String\Email;
use XLite\Core\Validator\String\Switcher;
use XLite\Core\Validator\TypeString;
use XLite\View\Model\Address\Address;

/**
 * Checkout update profile form
 */
class UpdateProfile extends \XLite\View\Form\Checkout\ACheckout
{
    /**
     * Get default form action
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'update_profile';
    }

    /**
     * Required form parameters
     *
     * @return array
     */
    protected function getCommonFormParams()
    {
        $list = parent::getCommonFormParams();
        $list['returnURL'] = $this->buildURL('checkout', 'update_profile');

        return $list;
    }

    /**
     * Get validator
     *
     * @return HashArray
     */
    protected function getValidator()
    {
        $validator = parent::getValidator();

        $validator->addPair(
            'email',
            new Email(),
            APair::SOFT
        );
        $validator->addPair(
            'password',
            new TypeString(),
            APair::SOFT
        );
        $validator->addPair(
            'create_profile',
            new Switcher(),
            APair::SOFT
        );
        $validator->addPair(
            'guest_agree',
            new Switcher(),
            APair::SOFT
        );
        $validator->addPair(
            'same_address',
            new Switcher(),
            APair::SOFT
        );

        $onlyCalculate = (bool) Request::getInstance()->only_calculate;
        $mode = $onlyCalculate
            ? APair::SOFT
            : APair::STRICT;
        $nonEmpty = !$onlyCalculate;

        // Shipping address
        $shippingAddress = $validator->addPair(
            'shippingAddress',
            new HashArray(),
            APair::SOFT
        );

        $addressFields = XLite::getController()->getAddressFields();

        $isCountryStateAdded = false;
        $isEmailAdded = in_array(
            'email',
            array_map(
                static fn (Simple $item): string => $item->getName(),
                $validator->getPairs()->getValues()
            ),
            true
        );

        foreach ($addressFields as $fieldName => $fieldData) {
            if (in_array($fieldName, ['country_code', 'state_id'])) {
                if (!$isCountryStateAdded) {
                    $shippingAddress->addPair(new CountryState());
                    $isCountryStateAdded = true;
                }
            } elseif ($fieldName !== 'email' || !$isEmailAdded) {
                $mode = ($onlyCalculate || !$fieldData[Address::SCHEMA_REQUIRED])
                    ? APair::SOFT
                    : APair::STRICT;
                $shippingAddress->addPair(
                    $fieldName,
                    new TypeString($nonEmpty && $fieldData[Address::SCHEMA_REQUIRED]),
                    $mode
                );
            }
        }

        $shippingAddress->addPair(
            'save_in_book',
            new Switcher(),
            APair::SOFT
        );

        // Billing address
        if (!Request::getInstance()->same_address) {
            $billingAddress = $validator->addPair(
                'billingAddress',
                new HashArray(),
                APair::SOFT
            );

            $isCountryStateAdded = false;

            foreach ($addressFields as $fieldName => $fieldData) {
                if (in_array($fieldName, ['country_code', 'state_id'])) {
                    if (!$isCountryStateAdded) {
                        $billingAddress->addPair(new CountryState());
                        $isCountryStateAdded = true;
                    }
                } else {
                    $billingAddress->addPair(
                        $fieldName,
                        new TypeString($nonEmpty && $fieldData[Address::SCHEMA_REQUIRED]),
                        $mode
                    );
                }
            }

            $billingAddress->addPair(
                'save_in_book',
                new Switcher(),
                APair::SOFT
            );
        }

        return $validator;
    }
}
