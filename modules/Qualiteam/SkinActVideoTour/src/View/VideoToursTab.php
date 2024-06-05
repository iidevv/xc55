<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoTour\View;

use Qualiteam\SkinActVideoTour\Model\Repo\VideoTours as VideoToursModel;
use Qualiteam\SkinActVideoTour\Model\VideoTours;
use Qualiteam\SkinActVideoTour\Trait\VideoTourTrait;
use XCart\Extender\Mapping\ListChild;
use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Database;
use XLite\Model\Product;
use XLite\Model\WidgetParam\TypeObject;

/**
 * Class video tours tab
 *
 * @ListChild (list="product.details.page.videoTours")
 */
class VideoToursTab extends \XLite\View\AView
{
    use VideoTourTrait;
    use ExecuteCachedTrait;

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/Qualiteam/SkinActVideoTour/js/jquery.lazytube.js';
        $list[] = 'modules/Qualiteam/SkinActVideoTour/video_tours.js';

        return $list;
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = [
            'file'  => 'modules/Qualiteam/SkinActVideoTour/video_tours.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }

    /**
     * Widget param names
     */
    public const PARAM_PRODUCT = 'product';

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams(): void
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_PRODUCT => new TypeObject(
                'Product',
                null,
                false,
                Product::class
            ),
        ];
    }

    /**
     * Get default template
     *
     * @return string
     */
    protected function getDefaultTemplate(): string
    {
        return $this->getModulePath() . '/tabs/body.twig';
    }

    /**
     * If product has attribute ingredients
     *
     * @return bool
     */
    protected function hasVideoTours(): bool
    {
        return !$this->isEmpty($this->getVideoToursTabInfo());
    }

    /**
     * Get ingredients info
     *
     * @return mixed
     */
    protected function getVideoToursTabInfo()
    {
        return $this->executeCachedRuntime(function () {
            $cnd                                    = new \XLite\Core\CommonCell();
            $cnd->{VideoToursModel::SEARCH_PRODUCT} = $this->getProduct();
            $cnd->{VideoToursModel::SEARCH_ENABLED} = true;
            $cnd->{VideoToursModel::P_ORDER_BY}     = ['v.position', 'asc'];

            return Database::getRepo(VideoTours::class)->search($cnd);
        }, [
            self::class,
            __METHOD__,
            $this->getProduct()->getProductId(),
        ]);
    }

    /**
     * getProduct
     *
     * @return \XLite\Model\Product|null
     */
    protected function getProduct(): ?Product
    {
        return $this->getParam(self::PARAM_PRODUCT);
    }
}
