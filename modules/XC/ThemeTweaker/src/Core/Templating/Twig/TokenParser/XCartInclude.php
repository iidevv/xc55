<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Core\Templating\Twig\TokenParser;

use Twig\Node\Node;
use Twig\TokenParser\IncludeTokenParser;
use Twig\Token;
use XC\ThemeTweaker\Core\Templating\Twig\Node\XCartInclude as XCartIncludeNode;

class XCartInclude extends IncludeTokenParser
{
    public function parse(Token $token): Node
    {
        $expr = $this->parser->getExpressionParser()->parseExpression();
        [$variables, $only, $ignoreMissing] = $this->parseArguments();

        return new XCartIncludeNode($expr, $variables, $only, $ignoreMissing, $token->getLine(), $this->getTag());
    }
}
