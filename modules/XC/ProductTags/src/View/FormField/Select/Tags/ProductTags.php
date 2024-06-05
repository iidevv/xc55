<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductTags\View\FormField\Select\Tags;

/**
 * Roles
 */
class ProductTags extends \XLite\View\FormField\Select\Tags\ATags
{
    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $list = [];

        foreach (\XLite\Core\Database::getRepo('XC\ProductTags\Model\Tag')->findAllTags() as $tag) {
            $list[$tag->getId()] = $tag->getName();
        }

        return $list;
    }

    /**
     * Set common attributes
     *
     * @param array $attrs Field attributes to prepare
     *
     * @return array
     */
    protected function setCommonAttributes(array $attrs)
    {
        return parent::setCommonAttributes($attrs)
            + [
                'size' => 1,
            ];
    }
}
