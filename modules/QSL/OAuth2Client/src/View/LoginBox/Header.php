<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\View\LoginBox;

/**
 * Login sign-in widget (header line)
 */
class Header extends \QSL\OAuth2Client\View\LoginBox\ALoginBox
{
    /**
     * @inheritdoc
     */
    protected function getPlacement()
    {
        return 'header';
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/OAuth2Client/login/header.twig';
    }
}
