<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View\Location\Node;

class Brands extends \XLite\View\Location\Node
{
    /**
     * Define widget parameters
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams[self::PARAM_NAME]->setValue(static::t('Brands'));
        $this->widgetParams[self::PARAM_LINK]->setValue($this->buildURL('brands'));
    }
}
