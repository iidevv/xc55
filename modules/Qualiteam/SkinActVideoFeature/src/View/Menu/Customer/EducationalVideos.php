<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\View\Menu\Customer;

use Qualiteam\SkinActVideoFeature\Helpers\Profile;
use XCart\Extender\Mapping\ListChild;

/**
 * Educational videos menu item
 *
 * @ListChild (list="layout.header.bar.links.logged", weight="495", zone="customer")
 */
class EducationalVideos extends \XLite\View\AView
{
    public const PARAM_CAPTION = 'caption';

    protected function getCaption()
    {
        return $this->getParam(static::PARAM_CAPTION);
    }

    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActVideoFeature/layout/header/educational_videos.twig';
    }

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_CAPTION => new \XLite\Model\WidgetParam\TypeString('Link caption', $this->getDefaultCaption()),
        ];
    }

    protected function getDefaultCaption()
    {
        return static::t('SkinActVideoFeature educational videos');
    }

    protected function getEducationalVideosUrl()
    {
        return $this->buildURL('educational_videos');
    }

    protected function isVisible()
    {
        return parent::isVisible() && Profile::isProMembership();
    }
}