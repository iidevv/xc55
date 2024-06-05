<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

class Attributes extends \XLite\Controller\Admin\ACL\Catalog
{
    /**
     * Controller parameters
     *
     * @var array
     */
    protected $params = ['target'];

    /**
     * Product class
     *
     * @var \XLite\Model\ProductClass
     */
    protected $productClass;

    /**
     * Check if current page is accessible
     *
     * @return bool
     */
    public function checkAccess()
    {
        return parent::checkAccess()
            && (
                $this->getProductClass()
                || !\XLite\Core\Request::getInstance()->product_class_id
            );
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getProductClass()
            ? $this->getProductClass()->getName()
            : static::t('Global attributes');
    }

    /**
     * Get product class
     *
     * @return \XLite\Model\ProductClass
     */
    public function getProductClass()
    {
        if (
            is_null($this->productClass)
            && \XLite\Core\Request::getInstance()->product_class_id
        ) {
            $this->productClass = \XLite\Core\Database::getRepo('XLite\Model\ProductClass')
                ->find((int) \XLite\Core\Request::getInstance()->product_class_id);
        }

        return $this->productClass;
    }

    /**
     * Get attribute groups
     *
     * @return array
     */
    public function getAttributeGroups()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\AttributeGroup')->findByProductClass(
            $this->getProductClass()
        );
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
                ['productClass' => $this->getProductClass(), 'product' => null]
            )
        );
    }

    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        $this->addLocationNode(
            'Classes & Attributes',
            $this->buildURL('product_classes')
        );
    }
}
