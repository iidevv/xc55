<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoSocial\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Repo\Product
{
    /**
     * @param array $record
     * @param array $regular
     *
     * @return array
     */
    protected function assembleRegularFieldsFromRecord(array $record, array $regular = [])
    {
        if (isset($record['ogMeta'])) {
            $record['ogMeta'] = \CDev\GoSocial\Logic\OgMeta::prepareOgMeta($record['ogMeta']);
        }

        return parent::assembleRegularFieldsFromRecord($record, $regular);
    }
}
