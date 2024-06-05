<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\Core\ConsistencyCheck;

use XCart\Extender\Mapping\Extender;
use XLite\Core\ConsistencyCheck\Retriever;
use XC\ProductVariants\Core\ConsistencyCheck\Rules\AttributesRule;

/**
 * Class Director
 * @package XC\ProductVariants\Core\ConsistencyCheck
 * @Extender\Mixin
 */
class Director extends \XLite\Core\ConsistencyCheck\Director
{
    /**
     * @return array
     */
    public function getRetrievers()
    {
        $retrievers = parent::getRetrievers();

        $retrievers['variants'] = [
            'name'      => 'Product Variants',
            'retriever' => new Retriever($this->getVariantsRules()),
        ];

        return $retrievers;
    }

    /**
     * @return array
     */
    protected function getVariantsRules()
    {
        return [
            'variants_has_attributes' => new AttributesRule(
                \XLite\Core\Database::getRepo('XC\ProductVariants\Model\ProductVariant')
            ),
        ];
    }
}
