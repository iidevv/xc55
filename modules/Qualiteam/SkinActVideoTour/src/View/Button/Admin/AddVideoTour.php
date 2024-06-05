<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoTour\View\Button\Admin;

use Qualiteam\SkinActVideoTour\Trait\VideoTourTrait;
use Qualiteam\SkinActVideoTour\View\VideoTour;

/**
 * Class add video
 */
class AddVideoTour extends \XLite\View\Button\APopupButton
{
    use VideoTourTrait;

    /**
     * Widget param names
     */
    public const PARAM_TARGET_PRODUCT_ID = 'target_product_id';

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles(): array
    {
        $list = parent::getJSFiles();
        $list[] = $this->getModulePath() . '/button/js/add_video/controller.js';

        return $list;
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams(): void
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_TARGET_PRODUCT_ID => new \XLite\Model\WidgetParam\TypeInt('', 0),
        ];
    }

    /**
     * Return target product id which is provided to the widget
     *
     * @return string
     */
    protected function getTargetProductId(): string
    {
        return $this->getParam(static::PARAM_TARGET_PRODUCT_ID);
    }

    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams(): array
    {
        $params = [
            'target' => 'video_tour',
            'widget' => VideoTour::class,
        ];

        if ($this->getTargetProductId()) {
            $params[self::PARAM_TARGET_PRODUCT_ID] = $this->getTargetProductId();
        }

        return $params;
    }

    /**
     * Return default button label
     *
     * @return string
     */
    protected function getDefaultLabel(): string
    {
        return static::t('SkinActVideoTour add video');
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass(): string
    {
        return parent::getClass() . ' add-video-tour';
    }
}