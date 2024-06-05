<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Coupons\API\Endpoint\Coupon\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInitializerInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use CDev\Coupons\Model\CouponProduct;
use CDev\Coupons\Model\Repo\Coupon;
use DateTimeImmutable;
use CDev\Coupons\API\Endpoint\Coupon\DTO\CouponInput as InputDTO;
use CDev\Coupons\Model\Coupon as Model;
use Doctrine\ORM\EntityManagerInterface;
use XLite\API\SubEntityInputTransformer\SubEntityIdBidirectionalCollectionInputTransformerInterface;
use XLite\API\SubEntityInputTransformer\SubEntityIdCollectionInputTransformerInterface;
use XLite\Model\Category;
use XLite\Model\Membership;
use XLite\Model\Product;
use XLite\Model\ProductClass;
use XLite\Model\Zone;
use XLite\Model\Repo\Product as ProductRepo;

class InputTransformer implements DataTransformerInitializerInterface, InputTransformerInterface
{
    /**
     * Maximum Value Unsigned Integer for Mysql DB
     */
    protected const MAX_UNSIGNED_INT = 4294967295;

    protected EntityManagerInterface $entityManager;

    protected Coupon $couponRepository;

    protected ProductRepo $productRepository;

    protected SubEntityIdCollectionInputTransformerInterface $productClassesUpdater;

    protected SubEntityIdBidirectionalCollectionInputTransformerInterface $membershipsUpdater;

    protected SubEntityIdBidirectionalCollectionInputTransformerInterface $zonesUpdater;

    protected SubEntityIdCollectionInputTransformerInterface $productsUpdater;

    protected SubEntityIdBidirectionalCollectionInputTransformerInterface $categoriesUpdater;

    public function __construct(
        EntityManagerInterface $entityManager,
        Coupon $couponRepository,
        ProductRepo $productRepository,
        SubEntityIdCollectionInputTransformerInterface $productClassesUpdater,
        SubEntityIdBidirectionalCollectionInputTransformerInterface $membershipsUpdater,
        SubEntityIdBidirectionalCollectionInputTransformerInterface $zonesUpdater,
        SubEntityIdCollectionInputTransformerInterface $productsUpdater,
        SubEntityIdBidirectionalCollectionInputTransformerInterface $categoriesUpdater
    ) {
        $this->entityManager = $entityManager;
        $this->couponRepository = $couponRepository;
        $this->productRepository = $productRepository;
        $this->productClassesUpdater = $productClassesUpdater;
        $this->membershipsUpdater = $membershipsUpdater;
        $this->zonesUpdater = $zonesUpdater;
        $this->productsUpdater = $productsUpdater;
        $this->categoriesUpdater = $categoriesUpdater;
    }

    /**
     * @param InputDTO $object
     */
    public function transform($object, string $to, array $context = []): Model
    {
        $entity = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? new Model();

        if ($this->couponRepository->findDuplicates($object->code, $entity->isPersistent() ? $entity : null)) {
            throw new InvalidArgumentException('Field "code" must be unique');
        }

        $entity->setCode($object->code);
        $entity->setEnabled($object->enabled);
        $entity->setValue($object->value);
        $entity->setType($object->type);
        $entity->setComment($object->comment);
        $entity->setDateRangeBegin($object->date_range_begin ? $object->date_range_begin->getTimestamp() : 0);
        $entity->setDateRangeEnd($object->date_range_end ? $object->date_range_end->getTimestamp() : 0);
        $entity->setTotalRangeBegin(is_null($object->total_range_begin) ? null : $object->total_range_begin);
        $entity->setTotalRangeEnd(is_null($object->total_range_end) ? null : $object->total_range_end);
        $entity->setUsesLimit($object->uses_limit);
        $entity->setUsesLimitPerUser($object->uses_limit_per_user);
        $entity->setSingleUse($object->single_use);
        $entity->setSpecificProducts($object->specific_products);

        if ($entity->getDateRangeBegin() || $entity->getDateRangeEnd()) {
            if ($entity->getDateRangeBegin() > $entity->getDateRangeEnd()) {
                throw new InvalidArgumentException('Field "date_range_begin" must be less than "date_range_end"');
            }

            if ($entity->getDateRangeBegin() < 0 || $entity->getDateRangeEnd() < 0) {
                throw new InvalidArgumentException('Field "date_range_begin" or "date_range_end" date is too old');
            }

            if (
                $entity->getDateRangeBegin() > self::MAX_UNSIGNED_INT
                || $entity->getDateRangeEnd() > self::MAX_UNSIGNED_INT
            ) {
                throw new InvalidArgumentException('Field "date_range_begin" or "date_range_end" date is too big');
            }
        }

        if (
            $entity->getTotalRangeBegin()
            && $entity->getTotalRangeEnd()
            && $entity->getTotalRangeBegin() > $entity->getTotalRangeEnd()
        ) {
            throw new InvalidArgumentException('Field "total_range_begin" must be less than "total_range_end"');
        }

        if ($object->specific_products) {
            $object->product_classes = [];
        }
        $this->productClassesUpdater->update($entity->getProductClasses(), $object->product_classes);
        $this->membershipsUpdater->update($entity->getMemberships(), $object->memberships, $entity);
        $this->zonesUpdater->update($entity->getZones(), $object->zones, $entity);
        if (!$object->specific_products) {
            $object->products = [];
        }
        $this->updateCouponProducts($entity, $object->products);
        if ($object->specific_products) {
            $object->categories = [];
        }
        $this->categoriesUpdater->update($entity->getCategories(), $object->categories, $entity);

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
        $input->code = $entity->getCode();
        $input->enabled = $entity->getEnabled();
        $input->value = $entity->getValue();
        $input->type = $entity->getType();
        $input->comment = $entity->getComment();
        $input->date_range_begin = $entity->getDateRangeBegin()
            ? new DateTimeImmutable('@' . $entity->getDateRangeBegin())
            : null;
        $input->date_range_end = $entity->getDateRangeEnd()
            ? new DateTimeImmutable('@' . $entity->getDateRangeEnd())
            : null;
        $input->total_range_begin = $entity->getTotalRangeBegin();
        $input->total_range_end = $entity->getTotalRangeEnd();
        $input->uses_limit = $entity->getUsesLimit();
        $input->uses_limit_per_user = $entity->getUsesLimitPerUser();
        $input->single_use = $entity->getSingleUse();
        $input->specific_products = $entity->getSpecificProducts();
        $input->product_classes = $this->assembleProductClassesIdList($entity);
        $input->memberships = $this->assembleMembershipsIdList($entity);
        $input->zones = $this->assembleZonesIdList($entity);
        $input->products = $this->assembleProductsIdList($entity);
        $input->categories = $this->assembleCategoriesIdList($entity);

        return $input;
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

    protected function updateCouponProducts(Model $object, array $idList): void
    {
        $collectionIdList = [];
        /** @var CouponProduct $subEntity */
        foreach ($object->getCouponProducts() as $subEntity) {
            $collectionId = $subEntity->getProduct()->getProductId();
            $collectionIdList[] = $collectionId;
        }

        // Add
        $needAdd = array_diff($idList, $collectionIdList);
        foreach ($needAdd as $id) {
            /** @var Product $subEntity */
            $subEntity = $this->productRepository->find($id);
            if (!$subEntity) {
                throw new InvalidArgumentException(sprintf('Product with ID %d not found', $id));
            }

            $link = (new CouponProduct())
                ->setCoupon($object)
                ->setProduct($subEntity);
            $object->getCouponProducts()->add($link);
        }

        // Remove
        $needRemove = array_diff($collectionIdList, $idList);
        foreach ($needRemove as $id) {
            /** @var CouponProduct $subEntity */
            foreach ($object->getCouponProducts() as $subEntity) {
                if ($subEntity->getProduct()->getProductId() === $id) {
                    $object->getCouponProducts()->removeElement($subEntity);
                    $subEntity->getProduct()->getCouponProducts()->removeElement($subEntity);
                    $this->entityManager->remove($subEntity);
                    break;
                }
            }
        }
    }
}
