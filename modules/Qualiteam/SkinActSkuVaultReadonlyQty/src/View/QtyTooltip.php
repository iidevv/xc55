<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace Qualiteam\SkinActSkuVaultReadonlyQty\View;

use XLite\View\Tooltip;

class QtyTooltip extends Tooltip
{
    public function setWidgetParams(array $params)
    {
        parent::setWidgetParams($params);

        $this->widgetParams[static::PARAM_TEXT]
            ->setValue(static::t('Quantity is read-only because it is controlled by SkuVault'));
    }

    public function getValue()
    {
        return '';
    }

    public function setValue($value)
    {
    }

    public function validate()
    {
        return [true, true];
    }
}
