<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Cache;

/**
 * CacheKeyPartsGenerator contains the common logic that is used by different widgets to obtain cache key parts such as membership and shipping zones.
 */
class CacheKeyPartsGenerator
{
    use ExecuteCachedTrait;

    /**
     * Get logged in customer's membership that can be used as a cache key part
     *
     * @return string|null
     */
    public function getMembershipPart()
    {
        return $this->executeCachedRuntime(static function () {
            $auth = \XLite\Core\Auth::getInstance();

            if (!$auth->isLogged()) {
                return null;
            }

            $profile = $auth->getProfile();

            return $profile->getMembership() ? $profile->getMembership()->getMembershipId() : null;
        });
    }

    /**
     * Get logged in customer's shipping zones string that can be used as a cache key part
     *
     * @return string|null
     */
    public function getShippingZonesPart()
    {
        return $this->executeCachedRuntime(static function () {

            $zones = [];

            $addresses = [];

            $profile = \XLite\Core\Auth::getInstance()->getProfile();

            if ($profile) {
                foreach (['Shipping', 'Billing'] as $aType) {
                    $method = 'get' . $aType . 'Address';
                    if (($address = $profile->$method()) && !isset($addresses[$address->getAddressId()])) {
                        $addresses[$address->getAddressId()] = $address->toArray();
                    }
                }
            }

            if (!$addresses) {
                $addresses[] = \XLite\Model\Shipping::getDefaultAddress();
            }

            $repo = \XLite\Core\Database::getRepo('XLite\Model\Zone');

            foreach ($addresses as $address) {
                foreach ($repo->findApplicableZones($address) as $zone) {
                    $zones[] = $zone->getZoneId();
                }
            }

            return implode(',', array_unique($zones));
        });
    }
}
