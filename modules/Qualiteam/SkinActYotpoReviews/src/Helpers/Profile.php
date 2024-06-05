<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Helpers;

use XLite\Model\Profile as ProfileModel;

class Profile
{
    /**
     * @var \XLite\Model\Profile|null
     */
    protected ?ProfileModel $profile;

    /**
     * @param \XLite\Model\Profile|null $profile
     */
    public function __construct(?ProfileModel $profile)
    {
        $this->profile = $profile;
    }

    /**
     * @return int
     */
    public function getCustomerExternalId(): int
    {
        return $this->profile ? $this->profile->getProfileId() : 0;
    }

    /**
     * @return string
     */
    public function getCustomerEmail(): string
    {
        return $this->profile ? $this->profile->getLogin() : '';
    }

    /**
     * @return string
     */
    public function getCustomerPhoneNumber(): string
    {
        $result = '';

        if ($this->profile) {
            $phoneNumber = $this->getFieldFromFirstAddress($this->profile, 'phone');

            if ($phoneNumber[0] !== '+') {
                $result = '+' . $phoneNumber;
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getCustomerFirstName(): string
    {
        return $this->profile ? $this->getFieldFromFirstAddress($this->profile, 'firstname') : '';
    }

    /**
     * @return string
     */
    public function getCustomerLastName(): string
    {
        return $this->profile ? $this->getFieldFromFirstAddress($this->profile, 'lastname') : '';
    }

    /**
     * @param \XLite\Model\Profile $profile
     * @param string               $fieldName
     *
     * @return string
     */
    protected function getFieldFromFirstAddress(ProfileModel $profile, string $fieldName): string
    {
        $res = '';

        $address = $profile->getFirstAddress();

        if ($address) {
            $fieldValue = $address->getFieldValue($fieldName);
            if ($fieldValue) {
                $res = $fieldValue->getValue();
            }
        }

        return $res;
    }
}