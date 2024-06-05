<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Provides various output helpers for any View classes
 */
trait OutputHelpersTrait
{
    /**
     * Returns array of key-value data as html tag attributes string
     * @param array $attributes
     * @return string
     */
    public static function convertToHtmlAttributeString(array $attributes)
    {
        $pairs = [];
        foreach ($attributes as $name => $value) {
            if (is_array($value)) {
                $value = implode(' ', $value);
            }

            $pairs[] = func_htmlspecialchars(strtolower($name)) . '="' . func_htmlspecialchars(trim($value)) . '"';
        }

        return implode(' ', $pairs);
    }
}
