<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Product\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use XLite\Core\Database;
use XLite\Model\Membership;

/**
 * @Annotation
 */
class MembershipsValidator extends ConstraintValidator
{
    /**
     * @param array      $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        $missingMemberships = array_diff(
            $value,
            array_map(
                static fn ($m) => $m->getName(),
                Database::getRepo(Membership::class)->findByNames($value)
            )
        );

        if (!empty($missingMemberships)) {
            foreach ($missingMemberships as $mm) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ value }}', $mm)
                    ->addViolation();
            }
        }
    }
}
