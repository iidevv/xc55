<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\View\LoginBox;

/**
 * Login sign-in widget (checkout)
 */
class Checkout extends \QSL\OAuth2Client\View\LoginBox\ALoginBox
{
    /**
     * @inheritdoc
     */
    protected function getPlacement()
    {
        return 'checkout';
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/OAuth2Client/login/checkout.twig';
    }
}
