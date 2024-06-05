<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\View\FormField\Select\SkuVault;

use Qualiteam\SkinActSkuVault\Core\Endpoint\Endpoint;
use XCart\Container;
use XLite\View\FormField\Select\ASelect;

class Locations extends ASelect
{
    /**
     * @inheritDoc
     */
    protected function getDefaultOptions()
    {
        /** @var Endpoint $endpoint */
        $endpoint = Container::getContainer()->get('getLocations');

        $data = $endpoint->getData();

        if (!empty($data)) {
            $data = array_column($data['Items'], 'LocationCode');
            $data = array_combine($data, $data);
        }

        return $data;
    }
}
