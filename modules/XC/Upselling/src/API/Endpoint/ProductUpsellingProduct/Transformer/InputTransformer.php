<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Upselling\API\Endpoint\ProductUpsellingProduct\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInitializerInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use XC\Upselling\API\Endpoint\ProductUpsellingProduct\DTO\ProductUpsellingProductInput as InputDTO;
use XC\Upselling\Model\UpsellingProduct as Model;
use XLite\Model\Product;
use XLite\Model\Repo\Product as ProductRepo;
use XC\Upselling\Model\Repo\UpsellingProduct as UpsellingProductRepo;

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
        /** @var Model $entity */
        $entity = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? new Model();

        $entity->setPosition($object->position);

        $productId = $this->detectProductId($context);
        if (!$productId) {
            throw new InvalidArgumentException('Product ID is invalid');
        }

        if ($productId === $object->product_id) {
            throw new InvalidArgumentException('The product cannot be linked to itself');
        }

        $this->checkUniqueness($entity, $object, $productId);

        /** @var Product $product */
        $product = $this->getProductRepository()->find($object->product_id);
        if (!$product) {
            throw new InvalidArgumentException(sprintf('Product with ID %d not found', $object->product_id));
        }
        $entity->setProduct($product);
        $product->addUpsellingProducts($entity);

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
        $input->product_id = $entity->getProduct()->getProductId();
        $input->position = $entity->getPosition();

        return $input;
    }

    protected function getProductRepository(): ProductRepo
    {
        return $this->entityManager->getRepository(Product::class);
    }

    protected function getUpsellingProductRepository(): UpsellingProductRepo
    {
        return $this->entityManager->getRepository(Model::class);
    }

    protected function detectProductId(array $context): ?int
    {
        if (preg_match('/products\/(\d+)\//S', $context['request_uri'], $match)) {
            return (int) $match[1];
        }

        return null;
    }

    protected function checkUniqueness(Model $entity, InputDTO $object, int $productId): void
    {
        if (!$this->needUniqueness($entity, $object)) {
            return;
        }

        $count = $this->getUpsellingProductRepository()->createQueryBuilder('up')
            ->andWhere('up.parentProduct = :parentProductId AND up.product = :productId')
            ->setParameter('parentProductId', $productId)
            ->setParameter('productId', $object->product_id)
            ->count();
        if ($count > 0) {
            throw new InvalidArgumentException(
                sprintf('Product link (%d -> %d) must be unique', $productId, $object->product_id)
            );
        }
    }

    protected function needUniqueness(Model $entity, InputDTO $object): bool
    {
        return !$entity->isPersistent()
            || $entity->getProduct()->getProductId() !== $object->product_id;
    }
}
