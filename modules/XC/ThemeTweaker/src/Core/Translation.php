<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Core;

use Twig\Markup;
use XCart\Extender\Mapping\Extender;

/**
 * Request
 * @Extender\Mixin
 */
class Translation extends \XLite\Core\Translation
{
    /**
     * Get translation postprocessor.
     *
     * @return \XLite\Core\ITranslationProcessor
     */
    public function getProcessor()
    {
        if (!isset($this->processor) && $this->isInlineEditingEnabled()) {
            $this->processor = new EditableTranslationProcessor();
        }

        return $this->processor;
    }

    /**
     * Get translation
     *
     * @param string $name      Label name
     * @param array  $arguments Substitute arguments OPTIONAL
     * @param string $code      Language code OPTIONAL
     *
     * @return string
     */
    public function translateAsEditable($name, array $arguments = [], $code = null)
    {
        $this->setProcessor(new EditableTranslationProcessor());
        $result = $this->translate($name, $arguments, $code);
        $this->setProcessor(null);

        return $result;
    }

    /**
     * Get translation
     *
     * @param string $name      Label name
     * @param array  $arguments Substitute arguments OPTIONAL
     * @param string $code      Language code OPTIONAL
     * @param string $type      Label type, can be used in \XLite\Core\ITranslationProcessor
     *
     * @return string
     */
    public function translate($name, array $arguments = [], $code = null, $type = null)
    {
        // Don't process the same thing twice
        return $name instanceof Markup
            ? $name
            : parent::translate($name, $arguments, $code, $type);
    }

    /**
     * Checks if inline language labels editor mode is on
     *
     * @return bool
     */
    public function isInlineEditingEnabled()
    {
        return \XC\ThemeTweaker\Core\ThemeTweaker::getInstance()->isInLabelsMode();
    }
}
