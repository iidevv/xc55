<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\XLSExportImport\Logic\Export;

use XCart\Extender\Mapping\Extender;

/**
 * Generator
 * @Extender\Mixin
 */
class Generator extends \XLite\Logic\Export\Generator
{
    /**
     * @inheritdoc
     */
    public function __construct(array $options = [])
    {
        parent::__construct($options);

        $this->options['type'] = !empty($options['type']) ? $options['type'] : 'csv';
    }
}
