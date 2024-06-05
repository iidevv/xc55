<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoTour\View\Form\Model;

use XLite\Core\Request;

/**
 * Class video tour
 */
class VideoTour extends \XLite\View\Form\AForm
{
    /**
     * Widget params names
     */
    public const PARAM_ID = 'id';
    public const PARAM_TARGET_PRODUCT_ID = 'target_product_id';

    /**
     * Return default value for the "target" parameter
     *
     * @return string
     */
    protected function getDefaultTarget(): string
    {
        return 'video_tour';
    }

    /**
     * Return default value for the "action" parameter
     *
     * @return string
     */
    protected function getDefaultAction(): string
    {
        return 'modify';
    }

    /**
     * Get default class name
     *
     * @return string
     */
    protected function getDefaultClassName(): string
    {
        return trim(parent::getDefaultClassName() . ' validationEngine video_tour');
    }

    /**
     * Return list of the form default parameters
     *
     * @return array
     */
    protected function getDefaultParams(): array
    {
        $params = [
            static::PARAM_ID => Request::getInstance()->id,
        ];

        if ((int)Request::getInstance()->target_product_id > 0) {
            $params[static::PARAM_TARGET_PRODUCT_ID] = Request::getInstance()->target_product_id;
        }

        return $params;
    }
}