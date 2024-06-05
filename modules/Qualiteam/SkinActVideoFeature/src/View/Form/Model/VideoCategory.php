<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\View\Form\Model;

class VideoCategory extends \XLite\View\Form\AForm
{
    /**
     * Return default value for the "target" parameter
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'video_category';
    }

    /**
     * Return default value for the "action" parameter
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'update';
    }

    /**
     * Get default class name
     *
     * @return string
     */
    protected function getDefaultClassName()
    {
        return trim(parent::getDefaultClassName() . ' validationEngine video_category');
    }

    /**
     * Return list of the form default parameters
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        return [
            'id'     => \XLite\Core\Request::getInstance()->id,
            'parent' => \XLite\Core\Request::getInstance()->parent,
        ];
    }
}