<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Controller\Customer;

use Qualiteam\SkinActGraphQLApi\Controller\Features\GraphqlApiContextTrait;

/**
 * Target for cart restore action (separate from main API due to nonexistent session for API requests)
 */
class GraphqlApiUserAccount extends \XLite\Controller\Customer\Cart
{
    use GraphqlApiContextTrait;

    const PAGE_DETAILS      = 'profile';
    const PAGE_ORDERS       = 'order_list';
    const PAGE_ADDRESS_BOOK = 'address_book';
    const PAGE_MESSAGES     = 'messages';
    const PAGE_CONTACT_US   = 'contact_us';

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        $this->redirect(
            $this->buildRedirectUrl()
        );
    }

    /**
     * @return string
     */
    protected function getMode()
    {
        return \XLite\Core\Request::getInstance()->mode ?: self::PAGE_DETAILS;
    }

    /**
     * @return string
     */
    protected function buildRedirectUrl()
    {
        switch ($this->getMode()) {
            case self::PAGE_ORDERS:
                return $this->buildRedirectUrlToOrders();
            case self::PAGE_ADDRESS_BOOK:
                return $this->buildRedirectUrlToAddressBook();
            case self::PAGE_MESSAGES:
                return $this->buildRedirectUrlToMessages();
            case self::PAGE_CONTACT_US:
                return $this->buildRedirectUrlToContactUs();
            case self::PAGE_DETAILS:
            default:
                return $this->buildRedirectUrlToDetails();
        }
    }

    /**
     * @return array
     */
    protected function getPageParams()
    {
        return [];
    }

    /**
     * @return string
     */
    protected function buildRedirectUrlToDetails()
    {
        return \XLite\Core\Converter::buildFullURL(
            'profile',
            '',
            $this->getPageParams(),
            \XLite::getCustomerScript()
        );
    }

    /**
     * @return string
     */
    protected function buildRedirectUrlToAddressBook()
    {
        return \XLite\Core\Converter::buildFullURL(
            'address_book',
            '',
            $this->getPageParams(),
            \XLite::getCustomerScript()
        );
    }

    /**
     * @return string
     */
    protected function buildRedirectUrlToMessages()
    {
        return \XLite\Core\Converter::buildFullURL(
            'messages',
            '',
            $this->getPageParams(),
            \XLite::getCustomerScript()
        );
    }

    /**
     * @return string
     */
    protected function buildRedirectUrlToOrders()
    {
        return \XLite\Core\Converter::buildFullURL(
            'order_list',
            '',
            $this->getPageParams(),
            \XLite::getCustomerScript()
        );
    }

    /**
     * @return string
     */
    protected function buildRedirectUrlToContactUs()
    {
        return \XLite\Core\Converter::buildFullURL(
            'contact_us',
            '',
            $this->getPageParams(),
            \XLite::getCustomerScript()
        );
    }
}
