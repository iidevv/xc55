<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\View\FormField\Select\StatusesMapping\Inline;

use XLite\Core\Database;
use XLite\Model\Order\Status\Shipping;
use XLite\View\FormField\Select\ASelect;

class FullfilmentStatuses extends ASelect
{
    protected function getDefaultOptions()
    {
        /** @var Shipping[] $data */
        $data = Database::getRepo(Shipping::class)->findAll();

        $result = ['' => ''];

        foreach ($data as $datum) {
            $result[$datum->getId()] = $datum->getName();
        }

        return $result;
    }
}
