<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\View\Page\Admin;

use XCart\Extender\Mapping\ListChild;

/**
 * IP address page view
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Provider extends \XLite\View\AView
{
    /**
     * @inheritdoc
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['oauth2_client_provider']);
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/OAuth2Client/provider/body.twig';
    }
}
