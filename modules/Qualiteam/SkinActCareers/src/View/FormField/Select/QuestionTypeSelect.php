<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCareers\View\FormField\Select;


use Qualiteam\SkinActCareers\Model\InterviewQuestion;

class QuestionTypeSelect extends \XLite\View\FormField\Select\Regular
{
    protected function getDefaultOptions()
    {
        return [
            InterviewQuestion::TYPE_PLAIN => static::t('SkinActCareers TYPE_PLAIN'),
            InterviewQuestion::TYPE_SELECT => static::t('SkinActCareers TYPE_SELECT'),
            InterviewQuestion::TYPE_TEXTAREA => static::t('SkinActCareers TYPE_TEXTAREA'),
        ];
    }
}