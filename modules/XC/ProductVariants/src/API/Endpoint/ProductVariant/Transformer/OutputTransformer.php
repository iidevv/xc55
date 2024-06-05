<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\API\Endpoint\ProductVariant\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use Doctrine\Common\Collections\Collection;
use Exception;
use XC\ProductVariants\API\Endpoint\ProductVariant\DTO\Image\ImageOutput;
use XC\ProductVariants\API\Endpoint\ProductVariant\DTO\ProductVariantOutput as OutputDTO;
use XC\ProductVariants\Model\Image\ProductVariant\Image;
use XC\ProductVariants\Model\ProductVariant as Model;
use XLite\Model\AttributeValue\AttributeValueCheckbox;
use XLite\Model\AttributeValue\AttributeValueSelect;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    /**
     * @param Model $object
     *
     * @throws Exception
     */
    public function transform($object, string $to, array $context = []): OutputDTO
    {
        $dto = new OutputDTO();
        $dto->id = $object->getId();
        $dto->price = $object->getPrice();
        $dto->default_price = $object->getDefaultPrice();
        $dto->amount = $object->getAmount();
        $dto->default_amount = $object->getDefaultAmount();
        $dto->weight = $object->getWeight();
        $dto->default_weight = $object->getDefaultWeight();
        $dto->default_variant = $object->getDefaultValue();
        $dto->sku = $object->getSku();
        $dto->image = $this->assembleImage($object->getImage());
        $dto->attribute_checkbox_values = $this->assembleCheckboxValues($object->getAttributeValueC());
        $dto->attribute_select_values = $this->assembleSelectValues($object->getAttributeValueS());

        return $dto;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === OutputDTO::class && $data instanceof Model;
    }

    protected function assembleImage(?Image $image): ?ImageOutput
    {
        if (!$image) {
            return null;
        }

        $dto = new ImageOutput();
        $dto->url = $image->getGetterURL();
        $dto->width = $image->getWidth();
        $dto->height = $image->getHeight();
        $dto->alt = $image->getAlt();

        return $dto;
    }

    /**
     * @param AttributeValueCheckbox[] $values
     *
     * @return int[]
     */
    protected function assembleCheckboxValues(Collection $values): array
    {
        return array_map(
            static function (AttributeValueCheckbox $value): int {
                return $value->getId();
            },
            $values->getValues()
        );
    }
    /**
     * @param AttributeValueSelect[] $values
     *
     * @return int[]
     */
    protected function assembleSelectValues(Collection $values): array
    {
        return array_map(
            static function (AttributeValueSelect $value): int {
                return $value->getId();
            },
            $values->getValues()
        );
    }
}
