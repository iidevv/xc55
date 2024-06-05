<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\VendorMessages\View\StickyPanel;

class Conversations extends \XLite\View\StickyPanel\ItemsListForm implements \XLite\Core\PreloadedLabels\ProviderInterface
{
    protected function defineButtons()
    {
        $list = parent::defineButtons();
        unset($list['save']);

        return $list;
    }

    protected function defineAdditionalButtons()
    {
        return [
            'payment-status' => [
                'class'    => '\XC\VendorMessages\View\Button\Dropdown\MarkAs',
                'params'   => [
                    'label'          => 'Mark all',
                    'style'          => 'more-action always-enabled icon-only',
                    'dropDirection'  => 'dropup',
                ],
                'position' => 250,
            ],
        ];
    }

    public function getPreloadedLanguageLabels()
    {
        return [
            'Mark all'      => static::t('Mark all'),
            'Mark selected' => static::t('Mark selected'),
        ];
    }

    protected function getSettingLinkClassName(): string
    {
        return parent::getSettingLinkClassName() ?: '\XC\VendorMessages\Main';
    }
}
