<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button\ItemsExport;

/**
 * Cloned products ItemsExport button
 */
class ClonedProducts extends \XLite\View\Button\ItemsExport
{
    protected function getAdditionalButtons()
    {
        $list = [];
        $list['CSV'] = $this->getWidget(
            [
                'label'      => 'CSV',
                'style'      => 'always-enabled action link list-action',
                'icon-style' => '',
                'entity'     => 'XLite\Logic\Export\Step\Products',
                'session'    => \XLite\View\ItemsList\Model\Product\Admin\Cloned::getConditionCellName(),
            ],
            'XLite\View\Button\ExportCSV'
        );

        return $list;
    }
}
