<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductStickers\API\Endpoint\Product\Transformer;

use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use QSL\ProductStickers\Model\ProductSticker;
use QSL\ProductStickers\Model\Repo\ProductSticker as ProductStickerRepo;
use XCart\Extender\Mapping\Extender;
use XLite\API\Endpoint\Product\DTO\Input as InputDTO;
use XLite\API\Endpoint\Product\Transformer\InputTransformer as ExtendedInputTransformer;
use QSL\ProductStickers\API\Endpoint\Product\DTO\ProductInput as ModuleInputDTO;
use QSL\ProductStickers\API\Endpoint\Product\DTO\ProductOutput as ModuleOutputDTO;
use QSL\ProductStickers\Model\Product as Model;
use XLite\Model\Product as BaseModel;

/**
 * @Extender\Mixin
 */
class InputTransformer extends ExtendedInputTransformer
{
    /**
     * @param ModuleInputDTO $object
     */
    public function transform($object, string $to, array $context = []): BaseModel
    {
        /** @var Model $entity */
        $entity = parent::transform($object, $to, $context);

        $this->updateStickers($entity, $object->stickers);

        return $entity;
    }

    public function initialize(string $inputClass, array $context = [])
    {
        /** @var Model $entity */
        $entity = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? null;
        if (!$entity) {
            return new InputDTO();
        }

        /** @var ModuleOutputDTO $input */
        $input = parent::initialize($inputClass, $context);

        $input->stickers = [];
        /** @var \QSL\ProductStickers\Model\ProductSticker $sticker */
        foreach ($entity->getProductStickers() as $sticker) {
            $input->stickers[] = $sticker->getProductStickerId();
        }

        return $input;
    }

    protected function getProductStickerRepository(): ProductStickerRepo
    {
        return $this->entityManager->getRepository(ProductSticker::class);
    }

    public function updateStickers(Model $entity, array $idList): void
    {
        $collection = $entity->getProductStickers();

        foreach ($idList as $id) {
            $found = false;
            /** @var ProductSticker $subEntity */
            foreach ($collection as $subEntity) {
                $subEntityId = $subEntity->getProductStickerId();
                if ($subEntityId === $id) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                // Add
                $subEntity = $this->getProductStickerRepository()->find($id);
                if (!$subEntity) {
                    throw new InvalidArgumentException(sprintf('Sticker with ID %d not found', $id));
                }

                $collection->add($subEntity);
                $subEntity->getProducts()->add($entity);
            }
        }

        /** @var ProductSticker $subEntity */
        foreach ($collection as $subEntity) {
            $found = false;
            foreach ($idList as $id) {
                $subEntityId = $subEntity->getProductStickerId();
                if ($subEntityId === $id) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                // Remove
                $collection->removeElement($subEntity);
                $subEntity->getProducts()->removeElement($entity);
            }
        }
    }
}
