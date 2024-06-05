<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input;

/**
 * Password (visible variant)
 */
class PasswordVisible extends \XLite\View\FormField\Input\Secure implements \XLite\Core\PreloadedLabels\ProviderInterface
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        return array_merge(
            parent::getJSFiles(),
            [
                $this->getDir() . '/js/password_visible.js',
            ]
        );
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            [
                $this->getDir() . '/css/password_visible.less',
            ]
        );
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'password_visible.twig';
    }

    /**
     * Prepare attributes
     *
     * @param array $attrs Field attributes to prepare
     *
     * @return array
     */
    protected function prepareAttributes(array $attrs)
    {
        $attrs = parent::prepareAttributes($attrs);

        $attrs['class'] = (empty($attrs['class']) ? '' : $attrs['class'] . ' ')
            . 'password-visible';

        return $attrs;
    }

    /**
     * Return true if value is trusted (purification must be ignored)
     *
     * @return boolean
     */
    public function isTrusted()
    {
        return true;
    }

    /**
     * Array of labels in following format.
     *
     * 'label' => 'translation'
     *
     * @return mixed
     */
    public function getPreloadedLanguageLabels()
    {
        return $this->getPasswordDifficultyLabels();
    }

    /**
     * Return HTML representation for widget attributes
     *
     * @return string
     */
    protected function getAttributesCode()
    {
        $attributes = $this->getAttributes();

        foreach ($attributes as $key => $attribute) {
            if ($key === 'value') {
                $attributes[$key] = '';
            }
        }

        return ' ' . static::convertToHtmlAttributeString($attributes);
    }
}
