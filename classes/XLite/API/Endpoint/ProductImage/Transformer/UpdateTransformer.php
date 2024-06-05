<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\ProductImage\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use XLite\API\Endpoint\ProductImage\DTO\ImageUpdate;
use XLite\Model\Image\Product\Image;
use XLite\Model\Product;

class UpdateTransformer implements DataTransformerInterface, UpdateTransformerInterface
{
    protected EntityManagerInterface $entityManager;

    protected ValidatorInterface $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    /**
     * @param ImageUpdate $object
     */
    public function transform($object, string $to, array $context = []): Image
    {
        $violations = $this->validator->validate($object);
        if (count($violations) > 0) {
            throw new InvalidArgumentException(sprintf("Input validations failed: %s", $violations));
        }

        $productId = $this->getProductId($context);
        if (!$productId) {
            throw new InvalidArgumentException("Product ID is invalid");
        }

        /** @var Product $product */
        $product = $this->entityManager->getRepository(Product::class)->find($productId);
        if (!$product) {
            throw new InvalidArgumentException(sprintf("Product with ID %s not found", $productId));
        }

        $imageId = $this->getImageId($context);
        if (!$imageId) {
            throw new InvalidArgumentException("Image ID is invalid");
        }

        /** @var Image $image */
        $image = $this->entityManager->getRepository(Image::class)->find($imageId);
        if (!$image) {
            throw new InvalidArgumentException(sprintf("Image with ID %s not found", $imageId));
        }

        $image->setOrderby(!$object->position ? $image->getOrderby() : $object->position);
        $image->setAlt(empty($object->alt) ? $image->getAlt() : $object->alt);

        return $image;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Image) {
            return false;
        }

        return $to === Image::class && ($context['input']['class'] ?? null) === ImageUpdate::class;
    }

    protected function getProductId(array $context): ?int
    {
        if (
            isset($context['request_uri'])
            && preg_match('/products\/(\d+)\/images\/\d+/Ss', $context['request_uri'], $match)
        ) {
            return (int) $match[1];
        }

        return null;
    }

    protected function getImageId(array $context): ?int
    {
        if (
            isset($context['request_uri'])
            && preg_match('/products\/\d+\/images\/(\d+)/Ss', $context['request_uri'], $match)
        ) {
            return (int) $match[1];
        }

        return null;
    }
}
