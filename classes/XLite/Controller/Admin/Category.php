<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

class Category extends \XLite\Controller\Admin\ACL\Catalog
{
    /**
     * @param array $params
     */
    public function __construct(array $params)
    {
        parent::__construct($params);

        $this->params = array_merge($this->params, ['id', 'parent']);
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return ($categoryName = $this->getCategoryName())
            ? static::t('Manage category (X)', ['category_name' => $categoryName])
            : static::t('No category defined');
    }

    /**
     * Add part to the location nodes list
     */
    protected function addBaseLocation()
    {
        if ($this->isVisible() && $this->getCategory()) {
            $this->addLocationNode(
                'Categories',
                $this->buildURL('root_categories')
            );

            $categories = $this->getCategory()->getPath();
            array_pop($categories);
            foreach ($categories as $category) {
                $this->addLocationNode(
                    $category->getName(),
                    $this->buildURL('categories', '', ['id' => $category->getCategoryId()])
                );
            }
        }
    }

    /**
     * Common method to determine current location
     *
     * @return string
     */
    protected function getLocation()
    {
        return !$this->isVisible()
            ? static::t('No category defined')
            : (($categoryName = $this->getCategoryName())
                ? $categoryName
                : static::t('Manage categories')
            );
    }

    /**
     * Check controller visibility
     *
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getCategory();
    }

    /**
     * Return the category name for the title
     *
     * @return string
     */
    public function getCategoryName()
    {
        return $this->getCategory() ? $this->getCategory()->getName() : '';
    }

    /**
     * Return the category name for the title
     *
     * @return string
     */
    public function getCategory()
    {
        if (is_null($this->category)) {
            $this->category = \XLite\Core\Database::getRepo('XLite\Model\Category')
                ->find($this->getCategoryId());
        }

        return $this->category;
    }

    /**
     * Return the category name for the title
     *
     * @return string
     */
    public function getCategoryId()
    {
        $id = \XLite\Core\Request::getInstance()->id;

        return $id && $id != $this->getRootCategoryId() ? $id : null;
    }

    /**
     * Update model
     */
    protected function doActionUpdate()
    {
        $this->getModelForm()->performAction('modify');
    }

    /**
     * Get model form class
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'XLite\View\Model\Category';
    }
}
