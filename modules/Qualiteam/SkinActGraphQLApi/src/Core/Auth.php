<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core;


use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]

 */

class Auth extends \XLite\Core\Auth
{
    /**
     * Returns token to find cart in API operations
     *
     * @param string $id
     *
     * @return string
     */
    public function loginProfileById($id)
    {
        $this->resetProfileCache();
        \XLite\Core\Session::getInstance()->set('profile_id', $id);
        $this->resetProfileCache();
        return true;
    }
}