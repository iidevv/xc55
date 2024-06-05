<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCareers\View\Form;

use XLite\Core\Request;

class CustomerQuestions extends \XLite\View\Form\AForm
{
    protected function getDefaultTarget()
    {
        return 'interview_questions';
    }

    protected function getDefaultAction()
    {
        return 'send_profile';
    }

    protected function getFormParams()
    {
        if (Request::getInstance()->jid) {
            return parent::getFormParams() +
                [
                    'jid' => Request::getInstance()->jid,
                ];
        }
        return parent::getFormParams();
    }

}