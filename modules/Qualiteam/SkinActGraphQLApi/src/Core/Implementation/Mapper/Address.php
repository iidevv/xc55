<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;

class Address
{
    /**
     * @param \XLite\Model\Address $address
     * @param array                $filter
     *
     * @return mixed
     */
    public function mapToDto(\XLite\Model\Address $address, array $filter = [])
    {
        $result = $this->getDefaultMappedAddress();

        $result['id'] = $address->getAddressId();
        $result['email'] = $address->getProfile()->getEmail();

        /** @var \XLite\Model\AddressField[] $fields */
        $fields = \XLite\Core\Database::getRepo('XLite\Model\AddressField')->findAllEnabled();

        foreach ($fields as $field) {
            $serviceName = static::translateServiceNameForApi(
                $field->getServiceName()
            );

            if (
                $serviceName === 'custom_state' || (
                    !empty($filter)
                    && !in_array($serviceName, $filter, true)
                )
            ) {
                continue;
            }

            $result[$serviceName] = $this->mapAddressField($serviceName, $address, $field);
        }

        return $result;
    }

    protected function getDefaultMappedAddress()
    {
        return [
            'id'         => '',
            'type'       => '',
            'email'      => '',
            'title'      => '',
            'first_name' => '',
            'last_name'  => '',
            'country'    => null,
            'state'      => null,
            'county'     => '',
            'city'       => '',
            'address'    => '',
            'address2'   => '',
            'zip'        => '',
            'phone'      => '',
            'fax'        => '',
        ];
    }

    /**
     * @param string                    $serviceName
     * @param \XLite\Model\Address      $address
     * @param \XLite\Model\AddressField $field
     *
     * @return mixed|null|\XLite\Model\Country|\XLite\Model\State
     */
    protected function mapAddressField($serviceName, \XLite\Model\Address $address, \XLite\Model\AddressField $field)
    {
        return match ($serviceName) {
            'country' => $address->getCountry(),
            'state' => $address->getState(),
            default => $address->getterProperty($field->getServiceName()),
        };
    }

    /**
     * Translate registration field service name to default JSON API service name
     *
     * @param string $name Service name
     *
     * @return string
     */
    public static function translateServiceNameForApi($name)
    {
        $map = static::getFieldNamesMap();

        if (isset($map[$name])) {
            $name = $map[$name];
        }

        return $name;
    }

    /**
     * @param string $name Name
     *
     * @return string
     */
    public static function convertApiNameToXCartName($name)
    {
        $map = static::getFieldNamesMap();
        $map = array_flip($map);

        return $map[$name] ?? $name;
    }

    protected static function getFieldNamesMap()
    {
        return [
            'firstname'    => 'first_name',
            'lastname'     => 'last_name',
            'street'       => 'address',
            'country_code' => 'country',
            'state_id'     => 'state',
            'custom_state' => 'county',
            'zipcode'      => 'zip',
        ];
    }
}
