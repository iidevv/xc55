<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Module\XC\Reviews\View\Button\Customer;

use Qualiteam\SkinActYotpoReviews\Module;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class EditReview extends \XC\Reviews\View\Button\Customer\EditReview
{
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = Module::getModulePath() . 'reviews/script.js';

        return $list;
    }
}