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
use XLite\API\Endpoint\Category\DTO\Input as InputDTO;
use XLite\API\Language;
use XLite\Model\Category;
use XLite\Model\Membership;

class InputTransformer implements DataTransformerInitializerInterface, InputTransformerInterface
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
     * @param InputDTO $object
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

        $parentId = $object->parent ?? $categoryRepo->getRootCategoryId();
        /** @var Category $parent */
        $parent = $categoryRepo->find($parentId);
        $this->validateParent($model, $parent, $object);
        $model->setParent($parent);
        $model->setLpos($parent->getLpos() + 1);
        $model->setRpos($parent->getRpos() + 2);
        $model->setDepth($parent->getDepth() + 1);

        if (Language::getInstance()->getLanguageCode()) {
            $model->setEditLanguage(Language::getInstance()->getLanguageCode());
        }

        $model->setEnabled($object->enabled);
        $model->setShowTitle($object->show_title);
        $model->setPosition($object->position);

        if (!empty($model->getMemberships())) {
            foreach ($model->getMemberships() as $membership) {
                $membership->getCategories()->removeElement($model);
            }
            $model->getMemberships()->clear();
        }

        if (!empty($object->memberships)) {
            /** @var \XLite\Model\Repo\Membership $membershipRepo */
            $membershipRepo = $this->entityManager->getRepository(Membership::class);
            foreach ($object->memberships as $m) {
                /** @var Membership $membership */
                $membership = $membershipRepo->findOneByName($m, false);
                if ($membership) {
                    $model->addMemberships($membership);
                    $membership->addCategories($model);
                }
            }
        }

        if (!empty($object->clean_url)) {
            $model->setCleanURL($object->clean_url);
        }

        $model->setName($object->name);
        $model->setDescription($object->description);
        $model->setMetaTags($object->meta_tags);
        $model->setMetaDescType($object->meta_description_type);
        $model->setMetaDesc($object->meta_description);
        $model->setMetaTitle($object->meta_title);

        return $model;
    }

    protected function validateParent(Category $model, ?Category $parent, $object): void
    {
        if (!$parent) {
            throw new InvalidArgumentException(sprintf("Parent category with ID %s is invalid", $object->parent));
        }

        $path = array_map(static fn($c) => $c->getCategoryId(), $parent->getPath());

        if (
            $model->getCategoryId()
            && in_array($model->getCategoryId(), $path, true)
        ) {
            throw new InvalidArgumentException('The category selected as a parent category has already been specified as a child category');
        }
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Category) {
            return false;
        }

        return $to === Category::class && ($context['input']['class'] ?? null) === InputDTO::class;
    }

    /**
     * @return InputDTO
     */
    public function initialize(string $inputClass, array $context = [])
    {
        /** @var Category $category */
        $category = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? null;

        if (!$category) {
            return new InputDTO();
        }

        $input                        = new InputDTO();
        $input->enabled               = $category->getEnabled();
        $input->show_title            = $category->getShowTitle();
        $input->position              = $category->getPosition();
        $input->memberships           = $category->getMemberships()->map(static fn($m) => $m->getName())->toArray();
        $input->parent                = $category->getParentId();
        $input->clean_url             = $category->getCleanURL();
        $input->name                  = $category->getName();
        $input->description           = $category->getDescription();
        $input->meta_tags             = $category->getMetaTags();
        $input->meta_description_type = $category->getMetaDescType();
        $input->meta_description      = $category->getMetaDesc();
        $input->meta_title            = $category->getMetaTitle();

        return $input;
    }
}
