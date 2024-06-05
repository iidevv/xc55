<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\Controller\Admin;

use Qualiteam\SkinActVideoFeature\Model\VideoCategory;
use XLite\Core\Database;

class SearchVideoCategories extends \XLite\Controller\Admin\AAdmin
{
    protected function doNoAction()
    {
        $request = \XLite\Core\Request::getInstance();
        $getParams = $request->getGetData();
        $searchText = $getParams['search'] ?? '';
        $page = $getParams['page'];
        $displayNoCategory = $getParams['displayNoCategory'] ?? false;
        $displayRootCategory = $getParams['displayRootCategory'] ?? false;
        $displayAnyCategory = $getParams['displayAnyCategory'] ?? false;
        $excludeCategoryId = $getParams['excludeCategory'] ?? 0;

        $countPerPage = 20;

        $result = [];
        $result['categories'] = [];

        $categoriesFound = Database::getRepo(VideoCategory::class)
            ->findAllByNamePart($searchText, $page, $countPerPage, $excludeCategoryId);

        /** @var VideoCategory $category */
        foreach ($categoriesFound as $category) {
            $result['categories'][] = [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'path' => $category->getStringPath(),
                'enabled' => $category->isVisible(),
            ];
        }

        if ($displayNoCategory && $page == 1) {
            $notAssigned = [
                'id' => 'no_category',
                'name' => static::t('SkinActVideoFeature no category assigned'),
                'path' => static::t('SkinActVideoFeature no category assigned'),
            ];

            array_unshift($result['categories'], $notAssigned);
        }

        if ($displayRootCategory  && $page == 1) {
            $rootCategory = [
                'id' => Database::getRepo(VideoCategory::class)->getRootCategoryId(),
                'name' => static::t('SkinActVideoFeature root category'),
                'path' => static::t('SkinActVideoFeature root category'),
            ];

            array_unshift($result['categories'], $rootCategory);
        }

        if ($displayAnyCategory  && $page == 1) {
            $anyCategory = [
                'id' => 0,
                'name' => static::t('SkinActVideoFeature any category'),
                'path' => static::t('SkinActVideoFeature any category'),
            ];

            array_unshift($result['categories'], $anyCategory);
        }

        $result['more'] = $categoriesFound->count() > $page * $countPerPage;

        $this->printAjax($result);
        die();
    }

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return true;
    }
}