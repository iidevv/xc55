<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoTour\Controller\Admin;

use Qualiteam\SkinActVideoTour\Model\Repo\VideoTours as VideoToursRepo;
use Qualiteam\SkinActVideoTour\Trait\VideoTourTrait;
use Qualiteam\SkinActVideoTour\View\ItemsList\Model\VideoTour as VideoTourItemsList;
use XCart\Extender\Mapping\Extender as Extender;
use XLite\Core\Auth;
use XLite\Core\Session;

/**
 * Class product
 * @Extender\Mixin
 */
class Product extends \XLite\Controller\Admin\Product
{
    use VideoTourTrait;

    /**
     * Class get pages
     *
     * @return array
     */
    public function getPages()
    {
        $list = parent::getPages();

        if (!$this->isNew()) {
            $list['video_tour'] = static::t('SkinActVideoTour video list');
        }

        return $list;
    }

    /**
     * Handles the request to admin interface
     *
     * @return void
     */
    public function handleRequest()
    {
        $cellName = VideoTourItemsList::getSessionCellName();

        Session::getInstance()->$cellName = [
            VideoToursRepo::SEARCH_PRODUCT => $this->getProductId(),
        ];

        parent::handleRequest();
    }

    /**
     * Get pages templates
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $tpls = parent::getPageTemplates();

        if (!$this->isNew()) {
            $tpls += [
                'video_tour' => $this->getModulePath() . '/video_tour.twig',
            ];
        }

        return $tpls;
    }

    /**
     * If display videos tab
     *
     * @return bool
     */
    protected function isDisplayVideoToursTab()
    {
        return !$this->isNew()
            && Auth::getInstance()->isPermissionAllowed('manage catalog');
    }
}