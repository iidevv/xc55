<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductStickers\API\Endpoint\ProductSticker\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInitializerInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use QSL\ProductStickers\API\Endpoint\ProductSticker\DTO\ProductStickerInput as InputDTO;
use QSL\ProductStickers\Model\ProductSticker as Model;
use QSL\ProductStickers\Model\Repo\ProductSticker as ProductStickerRepo;

class InputTransformer implements DataTransformerInitializerInterface, InputTransformerInterface
{
    protected ProductStickerRepo $repository;

    public function __construct(ProductStickerRepo $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param InputDTO $object
     */
    public function transform($object, string $to, array $context = []): Model
    {
        /** @var Model $entity */
        $entity = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? new Model();

        $this->checkUniqueness($entity, $object);

        $entity->setName($object->name);
        $entity->setPosition($object->position);
        $entity->setEnabled($object->enabled);
        $entity->setTextColor($object->text_color);
        $entity->setBgColor($object->bg_color);
        $entity->setIsLabel(false);

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
        $input->name = $entity->getName();
        $input->position = $entity->getPosition();
        $input->enabled = $entity->getEnabled();
        $input->text_color = $entity->getTextColor();
        $input->bg_color = $entity->getBgColor();

        return $input;
    }

    protected function checkUniqueness(Model $entity, InputDTO $object): void
    {
        if (!$this->needUniqueness($entity, $object)) {
            return;
        }

        $count = $this->repository->createQueryBuilder()
            ->andWhere('translations.name = :name')
            ->setParameter('name', $object->name)
            ->count();
        if ($count > 0) {
            throw new InvalidArgumentException(sprintf('Sticker "%s" already exists', $object->name));
        }
    }

    protected function needUniqueness(Model $entity, InputDTO $object): bool
    {
        return $entity->getName() !== $object->name;
    }
}
