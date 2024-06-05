<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Operation;

use XLite\Core\Database;
use XLite\Model\Address;

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
class UpdateAddress
{
    /**
     * @param Address $address
     * @param array   $data
     *
     * @return Address
     * @throws \Exception
     */
    public function __invoke(Address $address, $data)
    {
        $countryCode = $data['country_code'] ?? null;
        $stateCode = $data['state_code'] ?? null;
        $stateName = $data['state_name'] ?? null;

        if ($countryCode) {
            $country = Database::getRepo('XLite\Model\Country')
                ->findOneByCode($countryCode);

            if ($country) {
                $address->setCountry($country);
            }

            if ($stateCode) {
                $state = Database::getRepo('XLite\Model\State')
                    ->findOneByCountryAndState($countryCode, $stateCode);

                $address->setForcedState($state ?? $stateName);
            }
        }

        foreach ($data as $name => $value) {
            switch ($name) {
                case 'state_code':
                case 'state_name':
                case 'country_code':
                case 'fax':
                case 'email':
                    continue 2;
                    break;
                default:
                    $address->setterProperty(
                        \Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper\Address::convertApiNameToXCartName($name),
                        $value
                    );
                    break;
            }
        }

        return $address;
    }
}