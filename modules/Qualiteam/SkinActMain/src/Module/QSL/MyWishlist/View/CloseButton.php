<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMain\Module\QSL\MyWishlist\View;

use XCart\Extender\Mapping\ListChild;
use XLite\View\AView;

/**
 * @ListChild (list="itemsList.product.list.customer.info", zone="customer", weight="1300")
 */
class CloseButton extends AView
{
    public static function getAllowedTargets()
    {
        return ['wishlist'];
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActMain/modules/QSL/MyWishlist/wishlist.less';

        return $list;
    }

    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActMain/modules/QSL/MyWishlist/empty.twig';
    }
}