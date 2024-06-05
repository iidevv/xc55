<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Qualiteam\SkinActCareers\View\Button;

use XLite\Core\PreloadedLabels\ProviderInterface;

class DeleteSelected extends \XLite\View\Button\DeleteSelected implements ProviderInterface
{

    protected function getDefaultButtonClass()
    {
        return parent::getDefaultButtonClass() . ' skinact-delete-selected';
    }

    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/Qualiteam/SkinActCareers/delete_selected_button.js';
        return $list;
    }

    protected function getDefaultLabel()
    {
        return static::t('SkinActCareers Delete all');
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultTitle()
    {
        return static::t('SkinActCareers Delete all');
    }

    public function getPreloadedLanguageLabels()
    {
        return [
            'SkinActCareers Delete all' => static::t('SkinActCareers Delete all'),
            'SkinActCareers Delete selected' => static::t('SkinActCareers Delete selected')
        ];
    }
}