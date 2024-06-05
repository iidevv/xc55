<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoTour\View\Button\Admin;

use Qualiteam\SkinActVideoTour\Trait\VideoTourTrait;
use Qualiteam\SkinActVideoTour\View\VideoTour;

/**
 * Class edit video
 */
class EditVideoTour extends AddVideoTour
{
    use VideoTourTrait;

    /**
     * Widget param names
     */
    public const PARAM_ID = 'id';

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles(): array
    {
        $list   = parent::getJSFiles();
        $list[] = $this->getModulePath() . '/button/js/edit_video/controller.js';

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
            static::PARAM_ID => new \XLite\Model\WidgetParam\TypeInt('Video tour Id', 0),
        ];
    }

    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams(): array
    {
        $params = parent::prepareURLParams();

        return array_merge($params, [
            'id' => $this->getId(),
        ]);
    }

    /**
     * Return default button label
     *
     * @return string
     */
    protected function getDefaultLabel(): string
    {
        return '';
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass(): string
    {
        return parent::getClass() . ' edit-video-tour';
    }
}