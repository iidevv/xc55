<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCareers\Controller\Admin;


class Careers extends \XLite\Controller\Admin\AAdmin
{

    public function getTitle()
    {
        return static::t('SkinActCareers Careers');
    }
}