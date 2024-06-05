<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActWishlistUserExport\View;

use XCart\Extender\Mapping\ListChild;

/**
 * @ListChild (list="admin.center", zone="admin")
 */
class WishlistTableView extends \XLite\View\AView
{

    public static function getAllowedTargets()
    {
        return ['wishlist_table'];
    }


    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActWishlistUserExport/WishlistTable/table.twig';
    }

}