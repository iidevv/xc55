<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\View\FormField\Input;

/**
 * Rating field (rate product via stars)
 */
class Rating extends \XC\Reviews\View\VoteBar
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/XC/Reviews/form_field/input/rating/rating.js';

        return $list;
    }
}
