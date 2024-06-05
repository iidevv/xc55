<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Controller\Customer;

use XLite\Core\Request;
use Qualiteam\SkinActGraphQLApi\Controller\Features\GraphqlApiContextTrait;

/**
 * Target for cart restore action (separate from main API due to nonexistent session for API requests)
 */
class GraphqlApiCart extends \XLite\Controller\Customer\Cart
{
    use GraphqlApiContextTrait;

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
        return Request::getInstance()->getApiCartMode();
    }

    /**
     * @return string
     */
    protected function buildRedirectUrl()
    {
        return $this->getMode() === 'checkout'
            ? $this->buildRedirectUrlToCheckout()
            : $this->buildRedirectUrlToCart();
    }

    protected function buildRedirectUrlToCart()
    {
        return \XLite\Core\Converter::buildFullURL(
            'cart',
            '',
            [],
            \XLite::getCustomerScript()
        );
    }

    protected function buildRedirectUrlToCheckout()
    {
        return \XLite\Core\Converter::buildFullURL(
            'cart',
            'checkout',
            array(
                'action'        => 'update_profile',
                'email'         => '',
                'same_address'  => 1,
                'xcart_form_id' => \XLite::getFormId(true),
            ),
            \XLite::getCustomerScript()
        );
    }
}
