<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCareers\View\Form;

use XLite\Core\Request;

class Question extends \XLite\View\Form\AForm
{
    protected function getDefaultTarget()
    {
        return 'career_question';
    }

    protected function getDefaultAction()
    {
        if (Request::getInstance()->id) {
            return 'update_question';
        }

        return 'create_question';
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