<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Module\QSL\ShopByBrand\View\Model;

use XCart\Extender\Mapping\Extender;
use XLite\View\FormField\Input\Checkbox\OnOff;

/**
 * @Extender\Mixin
 * @Extender\Depend("QSL\ShopByBrand")
 */
class Brand extends \QSL\ShopByBrand\View\Model\Brand
{
    public function __construct(array $params = [], array $sections = [])
    {
        parent::__construct($params, $sections);

        $schema = $this->schemaDefault;
        $schema['skip_sync_to_skuvault'] = [
            self::SCHEMA_CLASS    => OnOff::class,
            self::SCHEMA_LABEL    => static::t('Skip sync to SkuVault'),
            self::SCHEMA_REQUIRED => false,
        ];

        $this->schemaDefault = $schema;
    }
}
