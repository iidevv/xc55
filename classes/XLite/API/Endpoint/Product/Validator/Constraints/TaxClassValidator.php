<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Product\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use XLite\Core\Database;
use XLite\Model\TaxClass;

/**
 * @Annotation
 */
class TaxClassValidator extends ConstraintValidator
{
    /**
     * @param string|null $value
     * @param Constraint  $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if ($value && !Database::getRepo(TaxClass::class)->findOneByName($value, true)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
