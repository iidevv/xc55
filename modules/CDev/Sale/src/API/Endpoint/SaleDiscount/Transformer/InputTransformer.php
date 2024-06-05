<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\API\Endpoint\SaleDiscount\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInitializerInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use CDev\Sale\Model\SaleDiscountProduct;
use DateTimeImmutable;
use CDev\Sale\API\Endpoint\SaleDiscount\DTO\SaleDiscountInput as InputDTO;
use CDev\Sale\Model\SaleDiscount as Model;
use Doctrine\ORM\EntityRepository;
use XLite\API\SubEntityInputTransformer\SubEntityIdBidirectionalCollectionInputTransformerInterface;
use XLite\API\SubEntityInputTransformer\SubEntityIdCollectionInputTransformerInterface;
use XLite\API\SubEntityOutputTransformer\SubEntityIdCollectionOutputTransformerInterface;
use XLite\Model\Product;

class InputTransformer implements DataTransformerInitializerInterface, InputTransformerInterface
{
    /**
     * Maximum Value Unsigned Integer for Mysql DB
     */
    protected const MAX_UNSIGNED_INT = 4294967295;

    protected SubEntityIdBidirectionalCollectionInputTransformerInterface $productClassesUpdater;

    protected SubEntityIdBidirectionalCollectionInputTransformerInterface $membershipsUpdater;

    protected SubEntityIdCollectionInputTransformerInterface $productsUpdater;

    protected SubEntityIdBidirectionalCollectionInputTransformerInterface $categoriesUpdater;

    protected SubEntityIdCollectionOutputTransformerInterface $productClassIdCollectionOutputTransformer;

    protected SubEntityIdCollectionOutputTransformerInterface $membershipIdCollectionOutputTransformer;

    protected SubEntityIdCollectionOutputTransformerInterface $categoryIdCollectionOutputTransformer;

    public function __construct(
        SubEntityIdBidirectionalCollectionInputTransformerInterface $productClassesUpdater,
        SubEntityIdBidirectionalCollectionInputTransformerInterface $membershipsUpdater,
        SubEntityIdCollectionInputTransformerInterface $productsUpdater,
        SubEntityIdBidirectionalCollectionInputTransformerInterface $categoriesUpdater,
        SubEntityIdCollectionOutputTransformerInterface $productClassIdCollectionOutputTransformer,
        SubEntityIdCollectionOutputTransformerInterface $membershipIdCollectionOutputTransformer,
        SubEntityIdCollectionOutputTransformerInterface $categoryIdCollectionOutputTransformer
    ) {
        $this->productClassesUpdater = $productClassesUpdater;
        $this->membershipsUpdater = $membershipsUpdater;
        $this->productsUpdater = $productsUpdater;
        $this->categoriesUpdater = $categoriesUpdater;
        $this->productClassIdCollectionOutputTransformer = $productClassIdCollectionOutputTransformer;
        $this->membershipIdCollectionOutputTransformer = $membershipIdCollectionOutputTransformer;
        $this->categoryIdCollectionOutputTransformer = $categoryIdCollectionOutputTransformer;
    }

    /**
     * @param InputDTO $object
     */
    public function transform($object, string $to, array $context = []): Model
    {
        $entity = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? new Model();

        $entity->setEnabled($object->enabled);
        $entity->setValue($object->value);
        $entity->setDateRangeBegin($object->date_range_begin ? $object->date_range_begin->getTimestamp() : 0);
        $entity->setDateRangeEnd($object->date_range_end ? $object->date_range_end->getTimestamp() : 0);
        $entity->setShowInSeparateSection($object->show_in_separate_section);
        $entity->setMetaDescType($object->meta_description_type);
        $entity->setSpecificProducts($object->specific_products);
        $entity->setName($object->name);
        $entity->setMetaTitle($object->meta_title);
        $entity->setMetaTags($object->meta_tags);
        $entity->setMetaDesc($object->meta_description);
        $entity->setCleanURL($object->clean_url);

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

        if ($object->specific_products) {
            $object->product_classes = [];
        }

        $this->productClassesUpdater->update($entity->getProductClasses(), $object->product_classes, $entity);
        $this->membershipsUpdater->update($entity->getMemberships(), $object->memberships, $entity);

        if (!$object->specific_products) {
            $object->products = [];
        }

        $this->updateProducts($entity, $object->products);

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
        $input->date_range_begin = $entity->getDateRangeBegin()
            ? new DateTimeImmutable('@' . $entity->getDateRangeBegin())
            : null;
        $input->date_range_end = $entity->getDateRangeEnd()
            ? new DateTimeImmutable('@' . $entity->getDateRangeEnd())
            : null;
        $input->show_in_separate_section = $entity->getShowInSeparateSection();
        $input->meta_description_type = $entity->getMetaDescType();
        $input->specific_products = $entity->getSpecificProducts();
        $input->name = $entity->getName();
        $input->meta_title = $entity->getMetaTitle();
        $input->meta_tags = $entity->getMetaTags();
        $input->meta_description = $entity->getMetaDesc();
        $input->clean_url = $entity->getCleanURL();
        $input->product_classes = $this->productClassIdCollectionOutputTransformer->transform($entity->getProductClasses());
        $input->memberships = $this->membershipIdCollectionOutputTransformer->transform($entity->getMemberships());
        $input->products = $this->assembleProductsIdList($entity);
        $input->categories = $this->categoryIdCollectionOutputTransformer->transform($entity->getCategories());

        return $input;
    }

    protected function getProductRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(Product::class);
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

    protected function updateProducts(Model $object, array $idList): void
    {
        $collectionIdList = [];
        /** @var SaleDiscountProduct $subEntity */
        foreach ($object->getSaleDiscountProducts() as $subEntity) {
            $collectionId = $subEntity->getProduct()->getProductId();
            $collectionIdList[] = $collectionId;
        }

        // Add
        $needAdd = array_diff($idList, $collectionIdList);
        foreach ($needAdd as $id) {
            $subEntity = $this->getProductRepository()->find($id);
            if (!$subEntity) {
                throw new InvalidArgumentException(sprintf('Product with ID %d not found', $id));
            }

            $link = (new SaleDiscountProduct())
                ->setSaleDiscount($object)
                ->setProduct($subEntity);
            $object->getSaleDiscountProducts()->add($link);
            $this->entityManager->persist($link);
        }

        // Remove
        $needRemove = array_diff($collectionIdList, $idList);
        foreach ($needRemove as $id) {
            /** @var SaleDiscountProduct $subEntity */
            foreach ($object->getSaleDiscountProducts() as $subEntity) {
                if ($subEntity->getProduct()->getProductId() === $id) {
                    $object->getSaleDiscountProducts()->removeElement($subEntity);
                    $subEntity->getProduct()->getSaleDiscountProducts()->removeElement($subEntity);
                    $this->entityManager->remove($subEntity);
                    break;
                }
            }
        }
    }
}
