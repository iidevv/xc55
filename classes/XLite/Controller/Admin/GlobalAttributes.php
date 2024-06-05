<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Attributes controller
 */
class GlobalAttributes extends \XLite\Controller\Admin\ACL\Catalog
{
    /**
     * Product class
     *
     * @var \XLite\Model\ProductClass
     */
    protected $productClass;

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getProductClass()
            ? static::t(
                'Attributes for X product class',
                [
                    'class' => $this->getProductClass()->getName()
                ]
            )
            : static::t('Classes & attributes');
    }

    /**
     * Get product class
     *
     * @return \XLite\Model\ProductClass
     */
    public function getProductClass()
    {
        return null;
    }

    /**
     * Get attribute groups
     *
     * @return array
     */
    public function getAttributeGroups()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\AttributeGroup')->findByProductClass(null);
    }

    /**
     * Get attributes count
     *
     * @return int
     */
    public function getAttributesCount()
    {
        return count(
            \XLite\Core\Database::getRepo('XLite\Model\Attribute')->findBy(
                ['productClass' => null, 'product' => null]
            )
        );
    }
}
