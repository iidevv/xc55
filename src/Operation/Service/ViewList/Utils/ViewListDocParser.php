<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Operation\Service\ViewList\Utils;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\DocParser;
use ReflectionException;

class ViewListDocParser implements ViewListDocParserInterface
{
    /**
     * @throws AnnotationException
     * @throws ReflectionException
     */
    public function parse(string $template): array
    {
        return $this->getDocParser()->parse($this->getDocCommentFromTemplate($template));
    }

    /**
     * @throws ReflectionException
     * @throws AnnotationException
     */
    public function parseContent(string $content): array
    {
        return $this->getDocParser()->parse($content);
    }

    private function getDocParser(): DocParser
    {
        $docParser = new DocParser();
        $docParser->addNamespace('XCart\Extender\Mapping');
        $docParser->setIgnoreNotImportedAnnotations(true);

        return $docParser;
    }

    /**
     * Read first comment from the template and convert it from twig to php syntax
     */
    private function getDocCommentFromTemplate(string $template): string
    {
        $content = str_split(file_get_contents($template));
        $comment = '';

        $collect = false;
        foreach ($content as $idx => $char) {
            if ($char === '{' && $content[$idx + 1] === '#') {
                $collect = true;
            }

            if (!$collect) {
                continue;
            }

            $comment .= $char;

            if ($char === '}' && $content[$idx - 1] === '#') {
                break;
            }
        }

        return str_replace(['{#', "\n #", '#}'], ['/**', "\n *", '*/'], $comment);
    }
}
