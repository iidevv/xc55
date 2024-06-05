<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Autocomplete extends \XLite\Controller\Admin\Autocomplete
{
    /**
     * @return bool
     */
    public function checkACL()
    {
        return parent::checkACL() || $this->isAllowedToRetrieveBrandNames();
    }

    /**
     * Check if the user is allowed to retrieve brand names.
     *
     * @return bool
     */
    protected function isAllowedToRetrieveBrandNames()
    {
        return \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage catalog')
            && (\XLite\Core\Request::getInstance()->dictionary === 'attributeOption');
    }
}
