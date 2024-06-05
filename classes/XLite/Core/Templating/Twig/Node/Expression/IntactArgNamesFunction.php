<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Templating\Twig\Node\Expression;

use Twig\Node\Expression\FunctionExpression;

/**
 * Customize default Twig behavior to not to underscorize camel-cased names
 * in widget & widget_list functions arguments
 */
class IntactArgNamesFunction extends FunctionExpression
{
    protected function normalizeName(string $name): string
    {
        return $name;
    }
}
