<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\Core\Validator;

use XCart\Extender\Mapping\Extender;

/**
 * Product SKU
 * @Extender\Mixin
 */
class SKU extends \XLite\Core\Validator\SKU
{
    /**
     * Validate
     *
     * @param mixed $data Data
     *
     * @return void
     */
    public function validate($data)
    {
        parent::validate($data);

        if (!\XLite\Core\Converter::isEmptyString($data)) {
            $entity = \XLite\Core\Database::getRepo('XC\ProductVariants\Model\ProductVariant')
                ->findOneBySku($this->sanitize($data));

            if ($entity) {
                $this->throwVariantSKUError();
            }
        }
    }

    /**
     * Specific throwError
     *
     * @return void
     * @throws \XLite\Core\Validator\Exception
     */
    protected function throwVariantSKUError()
    {
        throw $this->throwError('SKU is not unique (has duplicate assigned to product variant)');
    }
}
