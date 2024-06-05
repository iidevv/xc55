<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\View\Button\ItemsExport;

/**
 * Order ItemsExport button
 */
class Reviews extends \XLite\View\Button\ItemsExport
{
    protected function getAdditionalButtons()
    {
        $list = [];
        $list['CSV'] = $this->getWidget(
            [
                'label'      => 'CSV',
                'style'      => 'always-enabled action link list-action',
                'icon-style' => '',
                'entity'     => 'XC\Reviews\Logic\Export\Step\Reviews',
                'session'    => \XC\Reviews\View\ItemsList\Model\Review::getConditionCellName(),
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
