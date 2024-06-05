<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Qualiteam\SkinActCareers\View\Button;


class AddNewJob extends \XLite\View\Button\Simple
{
    protected function getDefaultLabel()
    {
        return static::t('SkinActCareers Add new job');
    }

    protected function getButtonAttributes()
    {
        $list = parent::getButtonAttributes();

        $list['onClick'] = 'window.location.href="'.$this->buildURL('job').'";';

        return $list;
    }
}