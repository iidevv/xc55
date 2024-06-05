<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Profile\InputRule\SubRule;

use XLite\API\InputRule\SubRule\CheckUniqueField;
use XLite\Model\QueryBuilder\AQueryBuilder;

class CheckUniqueLogin extends CheckUniqueField
{
    protected function buildQueryBuilder(object $inputDTO, array $context): AQueryBuilder
    {
        return parent::buildQueryBuilder($inputDTO, $context)
            ->andWhere('o.order IS NULL');
    }
}
