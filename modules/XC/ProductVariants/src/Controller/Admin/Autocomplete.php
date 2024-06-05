<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Autocomplete extends \XLite\Controller\Admin\Autocomplete
{
    /**
     * @param $term
     *
     * @return array
     * @throws \Doctrine\ORM\ORMException
     */
    protected function assembleDictionaryThemeTweakerProductVariant($term)
    {
        $data = array_map(
            static function (\XC\ProductVariants\Model\ProductVariant $variant) {
                return $variant->getVariantId();
            },
            \XLite\Core\Database::getRepo('XC\ProductVariants\Model\ProductVariant')
                ->findProductVariantsByTerm($term, 5)
        );

        return array_combine($data, $data);
    }
}
