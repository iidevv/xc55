<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Templating\Twig\TokenParser;

use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

class Form extends AbstractTokenParser
{
    public function parse(Token $token)
    {
        $parser = $this->parser;
        $stream = $parser->getStream();

        $class = $this->parser->getExpressionParser()->parseExpression();

        $params = $stream->nextIf(Token::NAME_TYPE, 'with')
            ? $this->parser->getExpressionParser()->parseExpression()
            : null;

        $stream->expect(Token::BLOCK_END_TYPE);

        $body = $this->parser->subparse([$this, 'decideBlockEnd'], true);

        $stream->expect(Token::BLOCK_END_TYPE);

        return new \XLite\Core\Templating\Twig\Node\Form($class, $params, $body, $token->getLine(), $this->getTag());
    }

    public function decideBlockEnd(Token $token)
    {
        return $token->test('endform');
    }

    public function getTag()
    {
        return 'form';
    }
}
