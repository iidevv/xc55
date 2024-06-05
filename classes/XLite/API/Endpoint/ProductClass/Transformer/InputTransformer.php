<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\ProductClass\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use XLite\API\Language;
use XLite\Model\ProductClass;
use XLite\API\Endpoint\ProductClass\DTO\ProductClassInput as InputDTO;

class InputTransformer implements DataTransformerInterface, InputTransformerInterface
{
    private EntityManagerInterface $entityManager;

    private ValidatorInterface $validator;

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
    public function transform($object, string $to, array $context = []): ProductClass
    {
        $violations = $this->validator->validate($object);
        if (count($violations) > 0) {
            throw new InvalidArgumentException(sprintf("Input validations failed: %s", (string) $violations));
        }

        $repo = $this->entityManager->getRepository(ProductClass::class);

        if (
            !isset($context[AbstractItemNormalizer::OBJECT_TO_POPULATE])
            && $repo->findOneByName($object->name, true)
        ) {
            throw new InvalidArgumentException(sprintf("'%s' product class already exists", $object->name));
        }

        $model = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? new ProductClass();
        if (Language::getInstance()->getLanguageCode()) {
            $model->setEditLanguage(Language::getInstance()->getLanguageCode());
        }
        $model->setName($object->name);

        return $model;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof ProductClass) {
            return false;
        }

        return $to === ProductClass::class && ($context['input']['class'] ?? null) !== null;
    }
}
