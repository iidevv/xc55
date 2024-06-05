<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Product\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @Annotation
 */
class DateValidator extends ConstraintValidator
{
    /**
     * @param string|null $value
     * @param Constraint  $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if ($value && strtotime($value) === false) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
