<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Validation;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class InputTransformerValidationDecorator implements DataTransformerInterface
{
    protected DataTransformerInterface $inner;

    protected ValidatorInterface $validator;

    public function __construct(
        DataTransformerInterface $inner,
        ValidatorInterface $validator
    ) {
        $this->inner = $inner;
        $this->validator = $validator;
    }

    /**
     * @return object
     */
    public function transform($object, string $to, array $context = [])
    {
        $violationList = $this->validator->validate($object);
        if (count($violationList) > 0) {
            throw new InvalidArgumentException((string)$violationList);
        }

        return $this->inner->transform($object, $to, $context);
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $this->inner->supportsTransformation($data, $to, $context);
    }
}
