<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\Controller\Admin;

use Qualiteam\SkinActVideoFeature\Model\VideoCategory as VideoCategoryModel;
use Qualiteam\SkinActVideoFeature\View\ItemsList\Model\VideoCategories as VideoCategoriesItemsListModel;
use Qualiteam\SkinActVideoFeature\View\Model\VideoCategory as VideoCategoryViewModel;

class VideoCategory extends \XLite\Controller\Admin\ACL\Catalog
{
    /**
     * @param array $params
     */
    public function __construct(array $params)
    {
        parent::__construct($params);

        $this->params = array_merge($this->params, ['id', 'parent']);
    }

    public function getTitle()
    {
        return static::t('SkinActVideoFeature video categories');
    }

    /**
     * 'selectorData' target used to get categories for selector on edit video page
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_unique(array_merge(parent::defineFreeFormIdActions(), ['selectorData']));
    }

    /**
     * Add part to the location nodes list
     */
    protected function addBaseLocation()
    {
        if ($this->isVisible() && $this->getCategory()) {
            $this->addLocationNode(
                static::t('SkinActVideoFeature video categories'),
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
                : static::t('SkinActVideoFeature video categories')
            );
    }

    /**
     * Check controller visibility
     *
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible() && (
                !$this->getCategoryId() || $this->getCategory()
            );
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
    {;
        if (is_null($this->category)) {
            $this->category = \XLite\Core\Database::getRepo(VideoCategoryModel::class)
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
        return \XLite\Core\Request::getInstance()->id;
    }

    /**
     * Return list of removed categories identifiers from request
     *
     * @return array
     */
    protected function getRemovedCategoriesIdentifiers()
    {
        $result = [];

        $data = \XLite\Core\Request::getInstance()->getData();

        if (!empty($data['delete']) && is_array($data['delete'])) {
            $result = array_keys($data['delete']);
        }

        return $result;
    }

    /**
     * Update list
     */
    protected function doActionUpdateItemsList()
    {
        $removalIdentifiers = $this->getRemovedCategoriesIdentifiers();

        if (!empty($removalIdentifiers)) {
            $this->processRemovalNotice($removalIdentifiers);
        }

        parent::doActionUpdateItemsList();
    }

    /**
     * Process removal notice
     *
     * @param array $ids Category identifiers
     */
    protected function processRemovalNotice($ids)
    {
        if (\XLite\Core\Database::getRepo(VideoCategoryModel::class)->checkForInternalCategoryVideos($ids)) {
            \XLite\Core\Session::getInstance()->{VideoCategoriesItemsListModel::IS_DISPLAY_REMOVAL_NOTICE} = true;
        }
    }

    protected function doActionUpdate()
    {
        $this->getModelForm()->performAction('modify');
    }

    protected function getModelFormClass()
    {
        return VideoCategoryViewModel::class;
    }
}