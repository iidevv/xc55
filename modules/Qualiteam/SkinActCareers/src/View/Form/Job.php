<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCareers\View\Form;

use XLite\Core\Request;

class Job extends \XLite\View\Form\AForm
{
    protected function getDefaultTarget()
    {
        return 'job';
    }

    protected function getDefaultAction()
    {
        if (Request::getInstance()->id) {
            return 'update_job';
        }

        return 'create_job';
    }

    protected function getFormParams()
    {
        if (Request::getInstance()->id) {
            return parent::getFormParams() +
                [
                    'id' => Request::getInstance()->id,
                ];
        }
        return parent::getFormParams();
    }

}