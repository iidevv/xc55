<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Module\QSL\Banner\View;

use XCart\Extender\Mapping\Extender;
use XC\ThemeTweaker;

/**
 * @Extender\Mixin
 * @Extender\Depend ("QSL\Banner")
 */
class ABannerSection extends \QSL\Banner\View\Customer\ABannerSection implements ThemeTweaker\View\LayoutBlockInterface
{
    use ThemeTweaker\View\LayoutBlockTrait;

    /**
     * Array index which is used by layout editor, may contain banner/slide/image ids
     */
    public const BANNER_SLIDE_IMAGE_KEY = 'banner_slide_image_';

    /**
     * We presume that id condition is in action
     */
    protected function getConditionalBanner()
    {
        if ($allBanners = $this->getBannerBoxes()) {
            return array_shift($allBanners);
        }

        return null;
    }

    /**
     * @return string
     */
    protected function getDefaultDisplayName()
    {
        if ($firstBanner = $this->getConditionalBanner()) {
            return $firstBanner->getTitleEscaped();
        }

        return static::t('Banner');
    }

    /**
     * @return string
     */
    protected function getDefaultLayoutBodyEntityId()
    {
        if ($firstBanner = $this->getConditionalBanner()) {
            return $firstBanner->getId();
        }

        return '';
    }

    /**
     * @return string
     */
    protected function getDefaultLayoutRemoveId()
    {
        return static::BANNER_SLIDE_IMAGE_KEY . $this->getDefaultLayoutBodyEntityId();
    }

    /**
     * Used to add data attributes to tag to work story builder properly
     */
    protected function displayDataAttributes($banner)
    {
        $parentData = parent::displayDataAttributes($banner);

        $dataArray = [
            'id'           => $banner->getId(),
            'bodyEntityId' => $banner->getId(),
            'removeId'     => static::BANNER_SLIDE_IMAGE_KEY . $banner->getId(),
            'blockName'    => $banner->getTitleEscaped(),
            'weight'       => $banner->getPosition(),
        ];

        $result = $parentData;
        foreach ($dataArray as $key => $value) {
            $result .= " data-$key='$value'";
        }

        return $result;
    }

    /*
     * To avoid fatal errors Trying to call undefined method;  template: skins/customer/modules/XC/ThemeTweaker/themetweaker/layout_editor/panel_parts/banners/layout_banners_vue_templates.twig  function: getCategory  object - XC\ThemeTweaker\View\ThemeTweaker\LayoutEditor
     * on cart/checkout pages
     */
    protected function getCategory()
    {
        $controller = \XLite::getController();
        try {
            $category = $controller->getCategory();
        } catch (\Throwable $exception) {
            $category = null;
        }

        return $category;
    }

    /*
     * To avoid fatal errors Trying to call undefined method;  template: skins/customer/modules/XC/ThemeTweaker/themetweaker/layout_editor/panel_parts/banners/layout_banners_vue_templates.twig  function: getCategory  object - XC\ThemeTweaker\View\ThemeTweaker\LayoutEditor
     * on cart/checkout pages
     */
    protected function getProduct()
    {
        // is_callable('parent::func') doesn't work due to __call implementation in classes/XLite/View/AView.php
        if (is_callable(\XLite::getController(), 'getProduct')) {
            return parent::getProduct();
        } else {
            return null;
        }
    }
}
