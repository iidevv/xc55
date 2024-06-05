<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Egoods\Core\Mail;

class EgoodsLinkCustomer extends \XLite\Core\Mail\Order\ACustomer
{
    public static function getDir()
    {
        return 'modules/CDev/Egoods/egoods_links';
    }
}
