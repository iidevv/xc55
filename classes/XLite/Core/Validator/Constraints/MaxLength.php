<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class MaxLength extends Constraint
{
    public $message;
    public $length;
}
