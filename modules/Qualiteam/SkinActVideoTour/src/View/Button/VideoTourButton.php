<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoTour\View\Button;

use Qualiteam\SkinActVideoTour\Trait\VideoTourTrait;

/**
 * Class video tour button
 */
class VideoTourButton extends \XLite\View\Button\Regular
{
    use VideoTourTrait;

    /**
     * Get class
     *
     * @return string
     */
    protected function getClass(): string
    {
        return parent::getClass() . ' video-tour-button';
    }

    /**
     * JavaScript: default JS code to execute
     *
     * @return string
     */
    protected function getDefaultJSCode(): string
    {
        return '';
    }

    /**
     * Get attributes
     *
     * @return array
     */
    protected function getAttributes(): array
    {
        $list = parent::getAttributes();

        if (isset($list['onclick'])) {
            unset($list['onclick']);
        }

        return $list;
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles(): array
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getModulePath() . '/image/style.less';

        return $list;
    }

    /**
     * Get a list of JavaScript files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles(): array
    {
        $list = parent::getJSFiles();
        $list[] = $this->getModulePath() . '/image/script.js';

        return $list;
    }
}