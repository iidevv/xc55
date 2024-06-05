<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\Controller\Customer;

use Qualiteam\SkinActVideoFeature\Model\VideoCategory;

abstract class ACatalog extends \XLite\Controller\Customer\ACustomer
{
    public function getPagerSessionCell()
    {
        return parent::getPagerSessionCell() . ($this->getCategory() ? $this->getCategory()->getCategoryId() : '');
    }

    abstract protected function getModelObject();

    public function __construct(array $params = [])
    {
        parent::__construct($params);

        $this->params[] = 'category_id';
        $this->params[] = 'videosubstring';
    }

    public function handleRequest()
    {
        \XLite\Core\Request::getInstance()->category_id = (int)$this->getCategoryId();
        \XLite\Core\Request::getInstance()->videosubstring = (string)$this->getVideosubstring();

        parent::handleRequest();
    }

    public function getVideosubstring()
    {
        return \XLite\Core\Request::getInstance()->videosubstring;
    }

    public function getCategory()
    {
        return \XLite\Core\Database::getRepo(VideoCategory::class)->getCategory($this->getCategoryId());
    }

    public function getTitleParentPart()
    {
        $categoryToGetName = null;

        if (!(in_array($this->getTarget(), ['educational_videos']))) {
            $categoryToGetName = $this->getCategory();
        } elseif ($this->getCategory() && $this->getCategory()->getParent()) {
            $categoryToGetName = $this->getCategory()->getParent();
        }

        return $categoryToGetName && $categoryToGetName->isVisible() && $categoryToGetName->getDepth() !== -1
            ? $categoryToGetName->getName()
            : '';
    }

    public function getDescription()
    {
        $model = $this->getModelObject();

        return $model ? $model->getDescription() : null;
    }

    protected function getCategoryPath()
    {
        return \XLite\Core\Database::getRepo(VideoCategory::class)->getCategoryPath($this->getCategoryId());
    }

    public function defineCommonJSData()
    {
        $list = parent::defineCommonJSData();

        $responsive_array = [
            'carousel_nav'        => false,
            'carousel_pagination' => true,

            'carousel_responsive_one_column'    => [
                0    => [
                    "items" => 2,
                ],
                480  => [
                    "items" => 2,
                ],
                768  => [
                    "items" => 2,
                ],
                992  => [
                    "items" => 3,
                ],
                1200 => [
                    "items" => 6,
                ],
            ],
        ];

        return array_merge(
            $list,
            $responsive_array
        );
    }
}