<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\ProfileAddress\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use XLite\API\Endpoint\ProfileAddress\DTO\CustomField\ProfileAddressCustomFieldOutput;
use XLite\API\Endpoint\ProfileAddress\DTO\ProfileAddressOutput as OutputDTO;
use XLite\Model\Address;
use XLite\Model\AddressFieldValue;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    /**
     * @param Address $object
     */
    public function transform($object, string $to, array $context = []): OutputDTO
    {
        $dto = new OutputDTO();
        $dto->id = $object->getAddressId();
        $dto->type = $object->getterProperty('type');
        $dto->title = $object->getTitle();
        $dto->firstname = $object->getFirstname();
        $dto->lastname = $object->getLastname();
        $dto->phone = $object->getPhone();
        $dto->street = $object->getStreet();
        $dto->city = $object->getCity();
        $dto->zipcode = $object->getZipcode();
        $dto->country_code = $object->getCountryCode();
        $dto->state = $object->getState() ? $object->getState()->getStateId() : null;
        $dto->state_name = $object->getStateName();
        $dto->is_billing = $object->getIsBilling();
        $dto->is_shipping = $object->getIsShipping();
        $dto->is_work = $object->getIsWork();

        $dto->custom_fields = [];
        /** @var AddressFieldValue $fieldValue */
        foreach ($object->getAddressFields() as $fieldValue) {
            if ($fieldValue->getAddressField()->getAdditional() && $fieldValue->getAddressField()->getEnabled()) {
                $customField = new ProfileAddressCustomFieldOutput();
                $customField->name = $fieldValue->getAddressField()->getServiceName();
                $customField->value = $fieldValue->getValue();
                $dto->custom_fields[] = $customField;
            }
        }

        return $dto;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === OutputDTO::class && $data instanceof Address;
    }
}
