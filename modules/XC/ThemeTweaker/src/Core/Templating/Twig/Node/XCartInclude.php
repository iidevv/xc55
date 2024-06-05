<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Core\Templating\Twig\Node;

use Twig\Node\IncludeNode;
use Twig\Compiler;

class XCartInclude extends IncludeNode
{
    public function compile(Compiler $compiler): void
    {
        $compiler
            ->write("\$currentWidget = \$this->env->getGlobals()['this'] ?? null;\n")
            ->write("if (\$currentWidget) {\n")
                ->indent()
                ->write('$includeResourcePath = (array) ')->subcompile($this->getNode('expr'))->raw(";\n")
                ->write("\$fullPath = '';\n")
                ->write("foreach (\$includeResourcePath as \$resourcePath) {\n")
                    ->indent()
                    ->write("if (\$fullPath = \XLite\Core\Layout::getInstance()->getResourceFullPath(\$resourcePath)) {\n")
                        ->indent()
                        ->write("break;\n")
                    ->outdent()
                    ->write("}\n")
                ->outdent()
                ->write("}\n")
                ->write("list(\$templateWrapperText, \$templateWrapperStart) = \$this->env->getGlobals()['this']->startMarker(\$fullPath);\n")
                ->write("if (\$templateWrapperText) {\n")
                    ->indent()
                    ->write("echo \$templateWrapperStart;\n")
                ->outdent()
                ->write("}\n")
            ->outdent()
            ->write("}\n\n");

        parent::compile($compiler);

        $compiler
            ->write("if (\$currentWidget && \$templateWrapperText) {\n")
                ->indent()
                ->write("echo \$this->env->getGlobals()['this']->endMarker(\$fullPath, \$templateWrapperText);\n")
            ->outdent()
            ->write("}\n");
    }
}
