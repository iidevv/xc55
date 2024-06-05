<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Mutations\System;

use Qualiteam\SkinActGraphQLApi\Model\Device;
use Qualiteam\SkinActGraphQLApi\Model\Profile;

trait DeviceHandlerTrait
{
    /**
     * @param array   $data
     * @param Profile $profile
     *
     * @return Device
     * @throws \Exception
     */
    public function registerDeviceData($data, $profile)
    {
        /** @var Device $device */
        $device = \XLite\Core\Database::getRepo(Device::class)->findOrCreateDeviceById($data['unique_id']);

        $device->map($data);

        if ($profile) {
            $profile->addDevice($device);
            $device->setProfile($profile);
        }

        \XLite\Core\Database::getEM()->flush();

        return $device;
    }
}