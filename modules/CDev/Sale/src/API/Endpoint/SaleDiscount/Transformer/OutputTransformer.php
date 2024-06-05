<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\API\Endpoint\SaleDiscount\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use CDev\Sale\API\Endpoint\SaleDiscount\DTO\SaleDiscountOutput as OutputDTO;
use CDev\Sale\Model\SaleDiscount as Model;
use CDev\Sale\Model\SaleDiscountProduct;
use DateTimeImmutable;
use Exception;
use XLite\API\SubEntityOutputTransformer\SubEntityIdCollectionOutputTransformerInterface;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    protected SubEntityIdCollectionOutputTransformerInterface $productClassIdCollectionOutputTransformer;

    protected SubEntityIdCollectionOutputTransformerInterface $membershipIdCollectionOutputTransformer;

    protected SubEntityIdCollectionOutputTransformerInterface $categoryIdCollectionOutputTransformer;

    public function __construct(
        SubEntityIdCollectionOutputTransformerInterface $productClassIdCollectionOutputTransformer,
        SubEntityIdCollectionOutputTransformerInterface $membershipIdCollectionOutputTransformer,
        SubEntityIdCollectionOutputTransformerInterface $categoryIdCollectionOutputTransformer
    ) {
        $this->productClassIdCollectionOutputTransformer = $productClassIdCollectionOutputTransformer;
        $this->membershipIdCollectionOutputTransformer = $membershipIdCollectionOutputTransformer;
        $this->categoryIdCollectionOutputTransformer = $categoryIdCollectionOutputTransformer;
    }

    /**
     * @param Model $object
     *
     * @throws Exception
     */
    public function transform($object, string $to, array $context = []): OutputDTO
    {
        $dto = new OutputDTO();
        $dto->id = $object->getId();
        $dto->code = $object->getCode();
        $dto->enabled = $object->getEnabled();
        $dto->value = $object->getValue();
        $dto->date_range_begin = $object->getDateRangeBegin()
            ? new DateTimeImmutable('@' . $object->getDateRangeBegin())
            : null;
        $dto->date_range_end = $object->getDateRangeEnd()
            ? new DateTimeImmutable('@' . $object->getDateRangeEnd())
            : null;
        $dto->show_in_separate_section = $object->getShowInSeparateSection();
        $dto->meta_description_type = $object->getMetaDescType();
        $dto->clean_url = $object->getCleanURL();
        $dto->name = $object->getName();
        $dto->meta_title = $object->getMetaTitle();
        $dto->meta_tags = $object->getMetaTags();
        $dto->meta_description = $object->getMetaDesc();
        $dto->specific_products = $object->getSpecificProducts();
        $dto->product_classes = $this->productClassIdCollectionOutputTransformer->transform($object->getProductClasses());
        $dto->memberships = $this->membershipIdCollectionOutputTransformer->transform($object->getMemberships());
        $dto->products = $this->assembleProductsIdList($object);
        $dto->categories = $this->categoryIdCollectionOutputTransformer->transform($object->getCategories());

        return $dto;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === OutputDTO::class && $data instanceof Model;
    }

    private function assembleProductsIdList(Model $object): array
    {
        return array_map(
            static function (SaleDiscountProduct $entity): int {
                return $entity->getProduct()->getProductId();
            },
            $object->getSaleDiscountProducts()->toArray()
        );
    }
}
