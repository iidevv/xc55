<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\Controller\Customer;

use Qualiteam\SkinActVideoFeature\Helpers\Profile;
use Qualiteam\SkinActVideoFeature\View\ItemsList\EducationalVideos as EducationalVideosItemsList;
use XLite\Controller\Features\ItemsListControllerTrait;

class EducationalVideos extends ACatalog
{
    use ItemsListControllerTrait;

    public function __construct(array $params = [])
    {
        parent::__construct($params);

        $cellName = $this->getSessionCellName();
        \XLite\Core\Session::getInstance()->$cellName = $this->getSearchParams();
    }

    public function getItemsListClass()
    {
        return EducationalVideosItemsList::class;
    }

    public function getTitle()
    {
        return $this->isVisible() ? \XLite\Core\Request::getInstance()->widget_title ?: static::t('My account') : '';
    }

    public function isTitleVisible()
    {
        return true;
    }

    public function isSecure()
    {
        return \XLite\Core\Config::getInstance()->Security->customer_security;
    }

    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getCategory() !== null
            && $this->getCategory()->isVisible();
    }

    public function checkAccess()
    {
        return parent::checkAccess()
            && \XLite\Core\Auth::getInstance()->isLogged()
            && Profile::isProMembership();
    }

    protected function addBaseLocation()
    {
    }

    /**
     * getDescription
     *
     * @return string
     */
    public function getDescription()
    {
        $model = $this->getModelObject();

        return $model ? $model->getViewDescription() : null;
    }

    /**
     * getModelObject
     *
     * @return \XLite\Model\AEntity
     */
    protected function getModelObject()
    {
        return $this->getCategory();
    }

    public function getCondition($paramName)
    {
        $searchParams = $this->getConditions();

        return $searchParams[$paramName] ?? null;
    }

    /**
     * Save search conditions
     */
    protected function doActionSearch()
    {
        $cellName = EducationalVideosItemsList::getSessionCellName();

        \XLite\Core\Session::getInstance()->$cellName = $this->getSearchParams();
    }

    /**
     * Return search parameters
     *
     * @return array
     */
    protected function getSearchParams()
    {
        $searchParams = $this->getConditions();

        foreach (
            EducationalVideosItemsList::getSearchParams() as $requestParam
        ) {
            if (isset(\XLite\Core\Request::getInstance()->$requestParam)) {
                $searchParams[$requestParam] = \XLite\Core\Request::getInstance()->$requestParam;
            }
        }

        return $searchParams;
    }

    /**
     * Get search conditions
     *
     * @return array
     */
    protected function getConditions()
    {
        $cellName = EducationalVideosItemsList::getSessionCellName();

        $searchParams = \XLite\Core\Session::getInstance()->$cellName;

        if (!is_array($searchParams)) {
            $searchParams = [];
        }

        return $searchParams;
    }
}