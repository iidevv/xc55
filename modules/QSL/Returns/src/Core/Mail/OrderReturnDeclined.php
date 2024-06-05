<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\Core\Mail;

class OrderReturnDeclined extends OrderReturnCreated
{
    public static function getDir()
    {
        return 'modules/QSL/Returns/return/declined';
    }
}
