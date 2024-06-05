<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class MaxLengthValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if ($value === null || $value === '') {
            return;
        }

        $stringValue = (string) $value;
        $length = mb_strlen($stringValue);

        if ($constraint->length !== null && $length > $constraint->length) {
                $this->context->buildViolation($constraint->message)
                    ->setInvalidValue($value)
                    ->addViolation();
        }
    }
}
