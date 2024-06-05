<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\View\Login;

/**
 * Common login widget
 */
class Common extends \QSL\OAuth2Client\View\Login\ALogin
{
    /**
     * @inheritdoc
     */
    protected function getDefaultTemplate()
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    protected function getTemplate()
    {
        switch ($this->getPlacement()) {
            case 'signin':
            case 'checkout':
                $result = 'modules/QSL/OAuth2Client/login/provider/common.long.twig';
                break;

            default:
                $result = 'modules/QSL/OAuth2Client/login/provider/common.twig';
        }

        return $result;
    }
}
