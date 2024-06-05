<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoTour\View\Product\Details\Customer\Page;

use Qualiteam\SkinActVideoTour\Model\Repo\VideoTours as VideoToursModel;
use Qualiteam\SkinActVideoTour\Model\VideoTours;
use Qualiteam\SkinActVideoTour\Trait\VideoTourTrait;
use XCart\Extender\Mapping\Extender as Extender;
use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Database;
use XLite\Model\Repo\ARepo;

/**
 * Abstract product page
 *
 * @Extender\Mixin
 */
class APage extends \XLite\View\Product\Details\Customer\Page\APage
{
    use VideoTourTrait;
    use ExecuteCachedTrait;

    /**
     * Process global tab addition into list
     *
     * @param                                  $list
     * @param \XLite\Model\Product\IProductTab $tab
     */
    protected function applyStaticTabListValue(&$list, $tab)
    {
        parent::applyStaticTabListValue($list, $tab);

        if (
            $this->hasVideoTours()
            && $tab->getServiceName() === $this->getVideoToursLabel()
        ) {
            $list[$tab->getServiceName()] = [
                'list'   => 'product.details.page.videoTours',
                'weight' => $tab->getPosition(),
            ];
        }
    }

    /**
     * If product has video tours
     *
     * @return bool
     */
    protected function hasVideoTours(): bool
    {
        return 0 < $this->getVideoTours();
    }

    /**
     * Get video tours count
     *
     * @return mixed
     */
    protected function getVideoTours()
    {
        return $this->executeCachedRuntime(function () {
            $cnd                                    = new \XLite\Core\CommonCell();
            $cnd->{VideoToursModel::SEARCH_PRODUCT} = $this->getProduct();
            $cnd->{VideoToursModel::SEARCH_ENABLED} = true;

            return Database::getRepo(VideoTours::class)->search($cnd, ARepo::SEARCH_MODE_COUNT);
        }, [
            self::class,
            __METHOD__,
            $this->getProduct()->getProductId(),
        ]);
    }
}
