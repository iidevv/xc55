<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActWishlistUserExport\View\Button\ItemsExport;


class WishlistExport extends \XLite\View\Button\ItemsExport
{
    protected function getAdditionalButtons()
    {
        $list = [];
        $list['CSV'] = $this->getWidget(
            [
                'label'      => 'CSV',
                'style'      => 'always-enabled action link list-action',
                'icon-style' => '',
                'entity'     => 'Qualiteam\SkinActWishlistUserExport\Logic\Export\Step\Wishlist',
                'session'    => \Qualiteam\SkinActWishlistUserExport\View\ItemsList\Model\WishlistTable::getConditionCellName(),
            ],
            'XLite\View\Button\ExportCSV'
        );

        return $list;
    }

    public function getClass(): string
    {
        return parent::getClass() . ' hide-if-empty-list';
    }
}