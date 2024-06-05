<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;

use XcartGraphqlApi\DTO\UserDTO;
use Qualiteam\SkinActGraphQLApi\Controller\Customer\GraphqlApiUserAccount;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\XCartContext;

class User
{
    /**
     * @param \XLite\Model\Profile $profile
     * @param XCartContext         $context
     *
     * @return UserDTO
     */
    public function mapToDto(\XLite\Model\Profile $profile, $context, $token = null)
    {
        $dto = new UserDTO();

        $dto->profileModel = $profile;

        $dto->id = $profile->getProfileId();

        $dto->user_type = $context->getAuthService()->getAccessLevelForProfile($profile);
        $dto->title = $this->getFieldFromFirstAddress($profile, 'title');
        $dto->first_name = $this->getFieldFromFirstAddress($profile, 'firstname');
        $dto->last_name = $this->getFieldFromFirstAddress($profile, 'lastname');
        $dto->phone = $this->getPhone($profile);

        $dto->contact_us_url = $this->mapUrl($profile, $context->getCartService()->getCartApiToken(), GraphqlApiUserAccount::PAGE_CONTACT_US);
        $dto->account_details_url = $this->mapUrl($profile, $context->getCartService()->getCartApiToken(), GraphqlApiUserAccount::PAGE_DETAILS);
        $dto->orders_list_url = $this->mapUrl($profile, $context->getCartService()->getCartApiToken(), GraphqlApiUserAccount::PAGE_ORDERS);
        $dto->address_book_url = $this->mapUrl($profile, $context->getCartService()->getCartApiToken(), GraphqlApiUserAccount::PAGE_ADDRESS_BOOK);
        $dto->messages_url = $this->mapUrl($profile, $context->getCartService()->getCartApiToken(), GraphqlApiUserAccount::PAGE_MESSAGES);

        $dto->login = $profile->getLogin();
        $dto->email = $profile->getLogin();
        $dto->enabled = $profile->isEnabled();
        $dto->registered = !$profile->getAnonymous();
        $dto->registered_date = $profile->getAdded();
        $dto->last_login_date = $profile->getLastLogin();
        $dto->orders_count = $profile->getOrdersCount();
        $dto->vendor_info = $this->getVendorInfo($profile);
        $dto->language = $profile->getLanguage();

        if ($token) {
            $dto->auth_token = $token;
        }


        return $dto;
    }

    protected function mapUrl($profile, $token, $mode = GraphqlApiUserAccount::PAGE_DETAILS)
    {
        return \XLite\Core\Converter::buildFullURL(
            'graphql_api_user_account',
            '',
            [ '_token' => $token, 'mode' => $mode ],
            \XLite::getCustomerScript()
        );
    }

    /**
     * @param \XLite\Model\Profile $profile
     *
     * @return string
     */
    protected function getPhone(\XLite\Model\Profile $profile)
    {
        return $this->getFieldFromFirstAddress($profile, 'phone');
        // TODO Wtf? Module?
//        return trim($profile->getAuthPhoneCode() . ' ' . $profile->getAuthPhoneNumber());
    }

    /**
     * @param \XLite\Model\Profile $profile
     *
     * @return array
     */
    protected function getVendorInfo(\XLite\Model\Profile $profile)
    {
        return [];
    }

    /**
     * @param \XLite\Model\Profile $profile
     * @param string               $fieldName
     *
     * @return string
     */
    protected function getFieldFromFirstAddress(\XLite\Model\Profile $profile, $fieldName)
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
