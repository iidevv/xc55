<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\XLSExportImport\Logic\Export\Step;

use XCart\Extender\Mapping\Extender;

/**
 * Users export step
 * @Extender\Mixin
 */
class Users extends \XLite\Logic\Export\Step\Users
{
    /**
     * @inheritdoc
     */
    protected function getWriterTypes()
    {
        $types = parent::getWriterTypes();

        $types['dates'][] = 'added';
        $types['dates'][] = 'firstLogin';
        $types['dates'][] = 'lastLogin';

        return $types;
    }
}
