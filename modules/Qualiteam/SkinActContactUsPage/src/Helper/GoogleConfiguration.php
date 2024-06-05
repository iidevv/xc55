<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActContactUsPage\Helper;

use Qualiteam\SkinActContactUsPage\Core\Api\GoogleMap;
use XLite\Core\Database;
use XLite\Model\Config;

class GoogleConfiguration
{
    public function updateGoogleData()
    {
        $googleMap = new GoogleMap(\XLite\Core\Config::getInstance()->Qualiteam->SkinActContactUsPage->gmap_api_key);

        $this->updateConfig(
            'showroom1_coordinate',
            implode(',', $googleMap->getCoordinateByAddress(\XLite\Core\Config::getInstance()->Qualiteam->SkinActContactUsPage->showroom1_address)),
            false
        );

        $this->updateConfig(
            'showroom2_coordinate',
            implode(',', $googleMap->getCoordinateByAddress(\XLite\Core\Config::getInstance()->Qualiteam->SkinActContactUsPage->showroom2_address)),
            false
        );
    }

    protected function updateConfig($name, $value, $silent)
    {
        Database::getRepo(Config::class)->createOption(
            [
                'name'     => $name,
                'category' => 'Qualiteam\SkinActContactUsPage',
                'value'    => $value,
            ],
            $silent
        );
    }

    public function getLatitude($config)
    {
        return explode(',', $config)[0];
    }

    public function getLongitude($config)
    {
        return explode(',', $config)[1];
    }
}
