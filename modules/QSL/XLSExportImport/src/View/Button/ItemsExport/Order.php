<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\XLSExportImport\View\Button\ItemsExport;

use XCart\Extender\Mapping\Extender;

/**
 * Order ItemsExport button
 * @Extender\Mixin
 */
class Order extends \XLite\View\Button\ItemsExport\Order
{
    /**
     * @inheritdoc
     */
    protected function getAdditionalButtons()
    {
        $list = parent::getAdditionalButtons();

        $list['XLS'] = $this->getWidget(
            [
                'label'      => 'Excel 5',
                'style'      => 'always-enabled action link list-action',
                'icon-style' => '',
                'entity'     => 'XLite\Logic\Export\Step\Orders',
                'session'    => \XLite\View\ItemsList\Model\Order\Admin\Search::getConditionCellName(),
            ],
            'QSL\XLSExportImport\View\Button\ExportXLS'
        );
        $list['XLSX'] = $this->getWidget(
            [
                'label'      => 'Excel 2007',
                'style'      => 'always-enabled action link list-action',
                'icon-style' => '',
                'entity'     => 'XLite\Logic\Export\Step\Orders',
                'session'    => \XLite\View\ItemsList\Model\Order\Admin\Search::getConditionCellName(),
            ],
            'QSL\XLSExportImport\View\Button\ExportXLSX'
        );
        $list['ODS'] = $this->getWidget(
            [
                'label'      => 'Open Document',
                'style'      => 'always-enabled action link list-action',
                'icon-style' => '',
                'entity'     => 'XLite\Logic\Export\Step\Orders',
                'session'    => \XLite\View\ItemsList\Model\Order\Admin\Search::getConditionCellName(),
            ],
            'QSL\XLSExportImport\View\Button\ExportODS'
        );

        return $list;
    }
}
