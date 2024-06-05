<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Category\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInitializerInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use XLite\API\Endpoint\Category\DTO\CategoryMoveInput;
use XLite\Model\Category;

class CategoryMoveInputTransformer implements DataTransformerInitializerInterface, CategoryMoveInputTransformerInterface
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
     * @param CategoryMoveInput $object
     */
    public function transform($object, string $to, array $context = []): Category
    {
        $violations = $this->validator->validate($object);
        if (count($violations) > 0) {
            throw new InvalidArgumentException(sprintf("Input validations failed: %s", $violations));
        }

        $model = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? new Category();

        /** @var \XLite\Model\Repo\Category $categoryRepo */
        $categoryRepo = $this->entityManager->getRepository(Category::class);
        $parentId     = $object->parent ?? $categoryRepo->getRootCategoryId();
        /** @var Category $parent */
        $parent = $categoryRepo->find($parentId);
        $this->validateParent($model, $parent, $object);
        $model->setParent($parent);
        $model->setLpos($parent->getLpos() + 1);
        $model->setRpos($parent->getRpos() + 2);
        $model->setDepth($parent->getDepth() + 1);

        $model->setPosition($object->position);

        return $model;
    }

    protected function validateParent(Category $model, ?Category $parent, $object): void
    {
        if (!$parent) {
            throw new InvalidArgumentException(sprintf("Parent category with ID %s not found", $object->parent));
        }

        $path = array_map(static fn($c) => $c->getCategoryId(), $parent->getPath());

        if (in_array($model->getCategoryId(), $path, true)) {
            throw new InvalidArgumentException('The category selected as a parent category has already been specified as a child category');
        }
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Category) {
            return false;
        }

        return $to === Category::class && ($context['input']['class'] ?? null) === CategoryMoveInput::class;
    }

    /**
     * @return CategoryMoveInput
     */
    public function initialize(string $inputClass, array $context = [])
    {
        /** @var Category $category */
        $category = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? null;

        if (!$category) {
            return new CategoryMoveInput();
        }

        $input           = new CategoryMoveInput();
        $input->position = $category->getPosition();
        $input->parent   = $category->getParentId();

        return $input;
    }
}
