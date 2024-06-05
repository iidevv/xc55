<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Coupons\API\Endpoint\Coupon\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use CDev\Coupons\API\Endpoint\Coupon\DTO\CouponOutput as OutputDTO;
use CDev\Coupons\Model\Coupon as Model;
use CDev\Coupons\Model\CouponProduct;
use DateTimeImmutable;
use Exception;
use XLite\Model\Category;
use XLite\Model\Membership;
use XLite\Model\ProductClass;
use XLite\Model\Zone;

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
        $dto->code = $object->getCode();
        $dto->enabled = $object->getEnabled();
        $dto->value = $object->getValue();
        $dto->type = $object->getType();
        $dto->comment = $object->getComment();
        $dto->date_range_begin = $object->getDateRangeBegin()
            ? new DateTimeImmutable('@' . $object->getDateRangeBegin())
            : null;
        $dto->date_range_end = $object->getDateRangeEnd()
            ? new DateTimeImmutable('@' . $object->getDateRangeEnd())
            : null;
        $dto->total_range_begin = $object->getTotalRangeBegin();
        $dto->total_range_end = $object->getTotalRangeEnd();
        $dto->uses_limit = $object->getUsesLimit();
        $dto->uses_limit_per_user = $object->getUsesLimitPerUser();
        $dto->single_use = $object->getSingleUse();
        $dto->specific_products = $object->getSpecificProducts();
        $dto->product_classes = $this->assembleProductClassesIdList($object);
        $dto->memberships = $this->assembleMembershipsIdList($object);
        $dto->zones = $this->assembleZonesIdList($object);
        $dto->products = $this->assembleProductsIdList($object);
        $dto->categories = $this->assembleCategoriesIdList($object);

        return $dto;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === OutputDTO::class && $data instanceof Model;
    }

    private function assembleProductClassesIdList(Model $object): array
    {
        return array_map(
            static function (ProductClass $entity): int {
                return $entity->getId();
            },
            $object->getProductClasses()->toArray()
        );
    }

    private function assembleMembershipsIdList(Model $object): array
    {
        return array_map(
            static function (Membership $entity): int {
                return $entity->getMembershipId();
            },
            $object->getMemberships()->toArray()
        );
    }

    private function assembleZonesIdList(Model $object): array
    {
        return array_map(
            static function (Zone $entity): int {
                return $entity->getZoneId();
            },
            $object->getZones()->toArray()
        );
    }

    private function assembleCategoriesIdList(Model $object): array
    {
        return array_map(
            static function (Category $entity): int {
                return $entity->getCategoryId();
            },
            $object->getCategories()->toArray()
        );
    }

    private function assembleProductsIdList(Model $object): array
    {
        return array_map(
            static function (CouponProduct $entity): int {
                return $entity->getProduct()->getProductId();
            },
            $object->getCouponProducts()->toArray()
        );
    }
}
