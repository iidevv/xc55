<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\Controller\Admin;

use Qualiteam\SkinActVideoFeature\Model\VideoCategory as VideoCategoryModel;

class CategoryVideos extends EducationalVideos
{
    /**
     * @param array $params Handler params OPTIONAL
     */
    public function __construct(array $params)
    {
        parent::__construct($params);

        $this->params[] = 'id';
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->isVisible()
            ? static::t('SkinActVideoFeature manage category (X)', ['category_name' => $this->getCategoryName()])
            : '';
    }

    /**
     * Add part to the location nodes list
     *
     * @return void
     */
    protected function addBaseLocation()
    {
        if ($this->isVisible() && $this->getCategory()) {
            $this->addLocationNode(
                'Video categories',
                $this->buildURL('video_categories')
            );

            $categories = $this->getCategory()->getPath();
            array_pop($categories);
            foreach ($categories as $category) {
                $this->addLocationNode(
                    $category->getName(),
                    $this->buildURL('video_category', '', ['id' => $category->getCategoryId()])
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
            ? static::t('SkinActVideoFeature no category defined')
            : (($categoryName = $this->getCategoryName())
                ? $categoryName
                : static::t('SkinActVideoFeature manage categories')
            );
    }

    /**
     * Return the category name for the title
     *
     * @return string
     */
    public function getCategoryName()
    {
        return \XLite\Core\Database::getRepo(VideoCategoryModel::class)
            ->find($this->getCategoryId())->getName();
    }

    /**
     * Return the category name for the title
     *
     * @return string
     */
    public function getCategoryId()
    {
        return \XLite\Core\Request::getInstance()->id;
    }

    /**
     * Return the category name for the title
     *
     * @return string
     */
    public function getCategory()
    {
        if (is_null($this->category)) {
            $this->category = \XLite\Core\Database::getRepo(VideoCategoryModel::class)
                ->find($this->getCategoryId());
        }

        return $this->category;
    }

    protected function isVisible()
    {
        return parent::isVisible() && $this->getCategoryId() && $this->getCategory();
    }
}