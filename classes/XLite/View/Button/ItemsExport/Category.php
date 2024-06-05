<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button\ItemsExport;

/**
 * Order ItemsExport button
 */
class Category extends \XLite\View\Button\ItemsExport
{
    protected function getAdditionalButtons()
    {
        $list = [];

        $list['CSV'] = $this->getWidget(
            [
                'label'      => 'CSV',
                'style'      => 'always-enabled action link list-action',
                'icon-style' => '',
                'entity'     => 'XLite\Logic\Export\Step\Categories',
                'session'    => \XLite\View\ItemsList\Model\Category::getConditionCellName(),
            ],
            'XLite\View\Button\ExportCSV'
        );

        return $list;
    }
}
