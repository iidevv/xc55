<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Helpers;

use Qualiteam\SkinActKlarna\Core\Validators\Payments\Validator;
use XLite\Model\Profile as Model;

class Profile
{
    /**
     * @param \Qualiteam\SkinActKlarna\Core\Validators\Payments\Validator $validator
     * @param \Qualiteam\SkinActKlarna\Helpers\Converter                  $converter
     * @param \XLite\Model\Profile                                        $profile
     */
    public function __construct(
        private Validator $validator,
        private Converter $converter,
        private Model     $profile
    ) {
    }

    /**
     * @return array
     */
    public function getBillingAddress(): array
    {
        $billingAddress = $this->profile->getBillingAddress();

        return [
            'given_name'     => $billingAddress->getFirstname(),
            'family_name'    => $billingAddress->getLastname(),
            'email'          => $this->profile->getEmail(),
            'street_address' => $billingAddress->getStreet(),
            'postal_code'    => $billingAddress->getZipcode(),
            'city'           => $billingAddress->getCity(),
            'region'         => $billingAddress->getState()->getState(),
            'phone'          => $billingAddress->getPhone(),
            'country'        => $this->getCountryCode(),
        ];
    }

    /**
     * @param string|null $mode
     *
     * @return bool
     */
    protected function isModeForDefaultCountryCode(?string $mode): bool
    {
        return in_array($mode, $this->getModesToDefaultCountryCode(), true);
    }

    /**
     * @return array
     */
    protected function getModesToDefaultCountryCode(): array
    {
        return [
            "session",
        ];
    }

    /**
     * @return string
     */
    public function getDefaultCountryCode(): string
    {
        return "US";
    }

    /**
     * @param string|null $mode
     *
     * @return string
     */
    public function getCountryCode(?string $mode = null): string
    {
        return $this->validator->hasCountry() && !$this->isModeForDefaultCountryCode($mode)
            ? $this->converter->getCountryCode($this->profile)
            : $this->getDefaultCountryCode();
    }
}
