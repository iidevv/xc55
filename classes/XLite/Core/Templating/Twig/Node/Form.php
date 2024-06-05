<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Templating\Twig\Node;

use Twig\Compiler;
use Twig\Node\Expression\AbstractExpression;
use Twig\Node\Node;
use Twig\Node\NodeOutputInterface;

class Form extends Node implements NodeOutputInterface
{
    public function __construct(AbstractExpression $class, ?AbstractExpression $params = null, ?Node $body = null, $lineno = null, $tag = null)
    {
        parent::__construct(array_filter(['body' => $body, 'params' => $params, 'class' => $class]), [], $lineno, $tag);
    }

    /**
     * Compiles the node to PHP.
     *
     * @param Compiler $compiler A Compiler instance
     */
    public function compile(Compiler $compiler)
    {
        $compiler->addDebugInfo($this);

        $compiler
            ->write("\$thisContext = \$this->env->getGlobals()['this'];\n")
            ->write("\$formWidget = \$thisContext->getWidget(");

        if ($this->hasNode('params')) {
            $compiler
                ->subcompile($this->getNode('params'))
                ->raw(', ');
        } else {
            $compiler->raw('[], ');
        }

        $compiler
            ->subcompile($this->getNode('class'))
            ->raw(");\n")
            ->write("\$formWidget->display();\n");

        if ($this->hasNode('body')) {
            $compiler->subcompile($this->getNode('body'));
        }

        $compiler
            ->write("\$formWidget->setWidgetParams(['end' => '1']);\n")
            ->write("\$formWidget->display();\n");
    }
}
