<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\ProfileAddress\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInitializerInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use XLite\API\Endpoint\ProfileAddress\DTO\CustomField\ProfileAddressCustomFieldInput;
use XLite\API\Endpoint\ProfileAddress\DTO\CustomField\ProfileAddressCustomFieldOutput;
use XLite\API\Endpoint\ProfileAddress\DTO\ProfileAddressInput as InputDTO;
use XLite\Model\Address;
use XLite\Model\Address as Model;
use XLite\Model\AddressField;
use XLite\Model\AddressFieldValue;
use XLite\Model\State;

class InputTransformer implements DataTransformerInitializerInterface, InputTransformerInterface
{
    protected EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * @param InputDTO $object
     */
    public function transform($object, string $to, array $context = []): Model
    {
        $entity = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? new Model();

        $this->checkRequiredFields($object);

        if ($this->isEnabledField('type')) {
            $entity->setterProperty('type', $object->type);
        }
        if ($this->isEnabledField('title')) {
            $entity->setterProperty('title', $object->title);
        }
        if ($this->isEnabledField('firstname')) {
            $entity->setterProperty('firstname', $object->firstname);
        }
        if ($this->isEnabledField('lastname')) {
            $entity->setterProperty('lastname', $object->lastname);
        }
        if ($this->isEnabledField('phone')) {
            $entity->setterProperty('phone', $object->phone);
        }
        if ($this->isEnabledField('street')) {
            $entity->setterProperty('street', $object->street);
        }
        if ($this->isEnabledField('city')) {
            $entity->setterProperty('city', $object->city);
        }
        if ($this->isEnabledField('zipcode')) {
            $entity->setterProperty('zipcode', $object->zipcode);
        }
        if ($this->isEnabledField('city')) {
            $entity->setterProperty('city', $object->city);
        }
        if ($this->isEnabledField('country_code') && !is_null($object->country_code)) {
            $entity->setCountryCode($object->country_code);
        }
        $entity->setIsBilling($object->is_billing);
        $entity->setIsShipping($object->is_shipping);
        $entity->setIsWork($object->is_work);

        if ($this->isEnabledField('state_id')) {
            $state = null;
            if ($object->state) {
                $state = $this->getStateRepository()->find($object->state);
                if (!$state) {
                    throw new InvalidArgumentException(sprintf('State with ID %d not found', $object->state));
                }
            } elseif ($object->state_name) {
                $state = $object->state_name;
            }
            $entity->setState($state);
        }

        $this->updateCustomFields($entity, $object->custom_fields);

        $countryHasStates = $entity->getCountry() && $entity->getCountry()->hasStates();
        /** @var AddressField $field */
        foreach ($this->getAddressFieldRepository()->findBy(['required' => true, 'enabled' => true]) as $field) {
            if (
                ($field->getServiceName() === 'state_id' && !$countryHasStates)
                || $field->getServiceName() === 'email'
            ) {
                continue;
            }

            $value = $field->getServiceName() === 'state_id'
                ? $entity->getStateId(true)
                : $entity->getterProperty($field->getServiceName());
            if (is_null($value) || (string)$value === '') {
                $name = $field->getServiceName() === 'state_id' && !$field->getAdditional()
                    ? 'state'
                    : $field->getServiceName();
                if ($field->getAdditional()) {
                    $name = 'custom_fields.' . $name;
                }
                throw new InvalidArgumentException(sprintf('Field "%s" is required', $name));
            }
        }

        return $entity;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Model) {
            return false;
        }

        return $to === Model::class && $context['input']['class'] === InputDTO::class;
    }

    /**
     * @return InputDTO
     */
    public function initialize(string $inputClass, array $context = [])
    {
        /** @var Model $entity */
        $entity = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? null;
        if (!$entity) {
            return new InputDTO();
        }

        $input = new InputDTO();
        $input->id = $entity->getAddressId();
        $input->type = $entity->getterProperty('address_type');
        $input->title = $entity->getTitle();
        $input->firstname = $entity->getFirstname();
        $input->lastname = $entity->getLastname();
        $input->phone = $entity->getPhone();
        $input->street = $entity->getStreet();
        $input->city = $entity->getCity();
        $input->zipcode = $entity->getZipcode();
        $input->country_code = $entity->getCountryCode();
        $input->state = $entity->getState() ? $entity->getState()->getStateId() : null;
        $input->state_name = $entity->getStateName();
        $input->is_billing = $entity->getIsBilling();
        $input->is_shipping = $entity->getIsShipping();
        $input->is_work = $entity->getIsWork();

        $input->custom_fields = [];
        /** @var AddressFieldValue $fieldValue */
        foreach ($entity->getAddressFields() as $fieldValue) {
            if ($fieldValue->getAddressField()->getAdditional() && $fieldValue->getAddressField()->getEnabled()) {
                $customField = new ProfileAddressCustomFieldOutput();
                $customField->name = $fieldValue->getAddressField()->getServiceName();
                $customField->value = $fieldValue->getValue();
                $input->custom_fields[] = $customField;
            }
        }

        return $input;
    }

    protected function getStateRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(State::class);
    }

    public function updateCustomFields(Address $entity, array $customFields): void
    {
        /** @var ProfileAddressCustomFieldInput $customField */
        foreach ($customFields as $customField) {
            /** @var AddressField $field */
            $field = $this->getAddressFieldRepository()->findOneBy(['serviceName' => $customField->name]);
            if (!$field) {
                throw new InvalidArgumentException(sprintf('Field with "%s" service name not found', $customField->name));
            }

            if (!$field->getAdditional()) {
                throw new InvalidArgumentException(sprintf('Field "%s" is not additional', $customField->name));
            }

            if (!$field->getEnabled()) {
                throw new InvalidArgumentException(sprintf('Field "%s" is disabled', $customField->name));
            }

            $entity->setterProperty($customField->name, $customField->value);
        }

        /** @var AddressFieldValue $subEntity */
        foreach ($entity->getAddressFields() as $subEntity) {
            if (!$subEntity->getAddressField()->getAdditional()) {
                continue;
            }

            $found = false;
            /** @var ProfileAddressCustomFieldInput $customField */
            foreach ($customFields as $customField) {
                if ($subEntity->getAddressField()->getServiceName() === $customField->name) {
                    $found = true;
                    break;
                }
            }

            // Remove
            if (!$found) {
                $this->entityManager->remove($subEntity);
                $entity->getAddressFields()->removeElement($subEntity);
            }
        }
    }

    protected function getAddressFieldRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(AddressField::class);
    }

    protected function checkRequiredFields(InputDTO $object): void
    {
        /** @var AddressField[] $fields */
        $fields = $this->getAddressFieldRepository()->findBy(['required' => true, 'enabled' => true, 'additional' => false]);
        foreach ($fields as $field) {
            $propertyName = $field->getServiceName();
            if (in_array($propertyName, ['state_id', 'custom_state', 'email'], true)) {
                continue;
            }

            if (is_null($object->$propertyName) || $object->$propertyName === '') {
                throw new InvalidArgumentException(sprintf('Field "%s" is required', $propertyName));
            }
        }
    }

    protected function isEnabledField(string $name): bool
    {
        /** @var AddressField $field */
        $field = $this->getAddressFieldRepository()->findOneBy(['serviceName' => $name]);
        if (!$field) {
            throw new InvalidArgumentException(sprintf('Field "%s" not found', $name));
        }

        return $field->getEnabled();
    }
}
