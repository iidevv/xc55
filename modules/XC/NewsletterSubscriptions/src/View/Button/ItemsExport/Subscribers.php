<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\NewsletterSubscriptions\View\Button\ItemsExport;

/**
 * Subscribers ItemsExport button
 */
class Subscribers extends \XLite\View\Button\ItemsExport
{
    protected function getAdditionalButtons()
    {
        $list = [];
        $list['CSV'] = $this->getWidget(
            [
                'label'      => 'CSV',
                'style'      => 'always-enabled action link list-action',
                'icon-style' => '',
                'entity'     => 'XC\NewsletterSubscriptions\Logic\Export\Step\NewsletterSubscribers',
                'session'    => \XC\NewsletterSubscriptions\View\ItemsList\Subscribers::getConditionCellName(),
            ],
            'XLite\View\Button\ExportCSV'
        );

        return $list;
    }
}
