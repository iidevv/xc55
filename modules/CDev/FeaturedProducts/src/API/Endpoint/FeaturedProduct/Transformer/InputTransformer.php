<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FeaturedProducts\API\Endpoint\FeaturedProduct\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use CDev\FeaturedProducts\API\Endpoint\FeaturedProduct\DTO\CategoryFeaturedInput;
use CDev\FeaturedProducts\Model\Category as DecoratedCategory;
use CDev\FeaturedProducts\Model\FeaturedProduct;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use XLite\Model\Category;
use XLite\Model\Product;

class InputTransformer implements DataTransformerInterface, InputTransformerInterface
{
    protected EntityManagerInterface $entityManager;

    protected ValidatorInterface $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->validator     = $validator;
    }

    /**
     * @param CategoryFeaturedInput $object
     */
    public function transform($object, string $to, array $context = []): FeaturedProduct
    {
        $violations = $this->validator->validate($object);
        if (count($violations) > 0) {
            throw new InvalidArgumentException(sprintf("Input validations failed: %s", $violations));
        }

        $categoryId = $this->getCategoryId($context);
        if (!$categoryId) {
            throw new InvalidArgumentException("Category ID is wrong");
        }

        /** @var DecoratedCategory $category */
        $category = $this->entityManager->getRepository(Category::class)->find($categoryId);
        if (!$category) {
            throw new InvalidArgumentException(sprintf("Category with ID %s not found", $categoryId));
        }

        /** @var Product $product */
        $product = $this->entityManager->getRepository(Product::class)->find($object->product_id);
        if (!$product) {
            throw new InvalidArgumentException(sprintf("Product with ID %s not found", $object->product_id));
        }

        $link = $this->entityManager->getRepository(FeaturedProduct::class)->findOneBy([
            'product'  => $product,
            'category' => $category,
        ]);

        if ($link) {
            return $link;
        }

        $link = new FeaturedProduct();
        $link->setProduct($product);
        $link->setCategory($category);
        $category->addFeaturedProducts($link);

        return $link;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof FeaturedProduct) {
            return false;
        }

        return $to === FeaturedProduct::class && ($context['input']['class'] ?? null) === CategoryFeaturedInput::class;
    }

    protected function getCategoryId(array $context): ?int
    {
        if ($context['collection_operation_name'] === 'add_front_page_featured') {
            return $this->entityManager->getRepository(Category::class)->getRootCategoryId();
        }

        if (
            isset($context['request_uri'])
            && preg_match('/categories\/(\d+)\/featured/Ss', $context['request_uri'], $match)
        ) {
            return (int) $match[1];
        }

        return null;
    }
}
