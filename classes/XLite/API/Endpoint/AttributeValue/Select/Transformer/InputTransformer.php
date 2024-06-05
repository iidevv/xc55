<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\AttributeValue\Select\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInitializerInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use XLite\API\Endpoint\AttributeValue\Select\Mapper\OutputPriceModifierTypeMapperInterface;
use XLite\API\Endpoint\AttributeValue\Select\Mapper\OutputWeightModifierTypeMapperInterface;
use XLite\API\Endpoint\AttributeValue\Select\DTO\AttributeValueSelectInput as InputDTO;
use XLite\API\Endpoint\AttributeValue\Select\Mapper\InputPriceModifierTypeMapperInterface;
use XLite\API\Endpoint\AttributeValue\Select\Mapper\InputWeightModifierTypeMapperInterface;
use XLite\Model\AttributeOption;
use XLite\Model\AttributeValue\AttributeValueSelect as Model;

class InputTransformer implements DataTransformerInitializerInterface, InputTransformerInterface
{
    protected EntityManagerInterface $entityManager;

    protected InputPriceModifierTypeMapperInterface $inputPriceModifierTypeMapper;

    protected InputWeightModifierTypeMapperInterface $inputWeightModifierTypeMapper;

    protected OutputPriceModifierTypeMapperInterface $outputPriceModifierTypeMapper;

    protected OutputWeightModifierTypeMapperInterface $outputWeightModifierTypeMapper;

    public function __construct(
        EntityManagerInterface $entityManager,
        InputPriceModifierTypeMapperInterface $inputPriceModifierTypeMapper,
        InputWeightModifierTypeMapperInterface $inputWeightModifierTypeMapper,
        OutputPriceModifierTypeMapperInterface $outputPriceModifierTypeMapper,
        OutputWeightModifierTypeMapperInterface $outputWeightModifierTypeMapper
    ) {
        $this->entityManager = $entityManager;
        $this->inputPriceModifierTypeMapper = $inputPriceModifierTypeMapper;
        $this->inputWeightModifierTypeMapper = $inputWeightModifierTypeMapper;
        $this->outputPriceModifierTypeMapper = $outputPriceModifierTypeMapper;
        $this->outputWeightModifierTypeMapper = $outputWeightModifierTypeMapper;
    }

    /**
     * @param InputDTO $object
     */
    public function transform($object, string $to, array $context = []): Model
    {
        $entity = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? new Model();
        $entity->setPosition($object->position);
        $entity->setPriceModifier($object->price_modifier);
        $entity->setPriceModifierType($this->inputPriceModifierTypeMapper->map($object->price_modifier_type));
        $entity->setWeightModifier($object->weight_modifier);
        $entity->setWeightModifierType($this->inputWeightModifierTypeMapper->map($object->weight_modifier_type));
        $entity->setDefaultValue($object->is_default);

        $repository = $this->getRepository();
        $option = $repository->find($object->option_id);
        if (!$option) {
            throw new InvalidArgumentException(sprintf('Attribute option with ID %d not found', $object->option_id));
        }
        $entity->setAttributeOption($option);

        return $entity;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Model) {
            return false;
        }

        return $to === Model::class && ($context['input']['class'] ?? null) !== null;
    }

    protected function getRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(AttributeOption::class);
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
        $input->option_id = $entity->getAttributeOption()->getId();
        $input->price_modifier = $entity->getPriceModifier();
        $input->price_modifier_type = $this->outputPriceModifierTypeMapper->map($entity->getPriceModifierType());
        $input->weight_modifier = $entity->getWeightModifier();
        $input->weight_modifier_type = $this->outputWeightModifierTypeMapper->map($entity->getWeightModifierType());
        $input->is_default = $entity->getDefaultValue();

        return $input;
    }
}
