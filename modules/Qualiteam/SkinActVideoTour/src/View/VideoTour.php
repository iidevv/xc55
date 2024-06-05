<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoTour\View;

use Qualiteam\SkinActVideoTour\Trait\VideoTourTrait;
use XCart\Extender\Mapping\ListChild;

/**
 * Class video tour
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class VideoTour extends \XLite\View\AView
{
    use VideoTourTrait;

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets(): array
    {
        $list = parent::getAllowedTargets();
        $list[] = 'video_tour';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate(): string
    {
        return $this->getModulePath() . '/video_tour/body.twig';
    }
}