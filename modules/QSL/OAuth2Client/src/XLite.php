<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client;

use XCart\Extender\Mapping\Extender;

/**
 * XLite
 * @Extender\Mixin
 */
class XLite extends \XLite
{
    /**
     * @inheritdoc
     */
    protected static function dispatchRequest()
    {
        $result = parent::dispatchRequest();

        $request = \XLite\Core\Request::getInstance();
        if (
            !empty($request->code)
            && !empty($request->state)
            && $request->isGet()
        ) {
            $states = \XLite\Core\Session::getInstance()->oauth2state ?: [];
            foreach ($states as $provider => $state) {
                if ($state['state'] == $request->state) {
                    $result = 'oauth2return';
                    $request->provider = $provider;
                }
            }
        }

        return $result;
    }
}
