<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Membership\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInitializerInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use XLite\API\Endpoint\Membership\DTO\MembershipInput as InputDTO;
use XLite\API\Language;
use XLite\Model\Membership;

class InputTransformer implements DataTransformerInitializerInterface, InputTransformerInterface
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
    public function transform($object, string $to, array $context = []): Membership
    {
        $violations = $this->validator->validate($object);
        if (count($violations) > 0) {
            throw new InvalidArgumentException(sprintf("Input validations failed: %s", (string) $violations));
        }

        $repo = $this->entityManager->getRepository(Membership::class);

        if (
            !isset($context[AbstractItemNormalizer::OBJECT_TO_POPULATE])
            && $repo->findOneByName($object->name, true)
        ) {
            throw new InvalidArgumentException(sprintf("'%s' membership already exists", $object->name));
        }

        $model = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? new Membership();
        if (Language::getInstance()->getLanguageCode()) {
            $model->setEditLanguage(Language::getInstance()->getLanguageCode());
        }
        $model->setName($object->name);
        $model->setEnabled($object->enabled);

        return $model;
    }

    /**
     * @return InputDTO
     */
    public function initialize(string $inputClass, array $context = [])
    {
        /** @var Membership $membership */
        $membership = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? null;
        if (!$membership) {
            return new InputDTO();
        }

        $input          = new InputDTO();
        $input->name    = $membership->getName();
        $input->enabled = $membership->getEnabled();

        return $input;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Membership) {
            return false;
        }

        return $to === Membership::class && ($context['input']['class'] ?? null) !== null;
    }
}
