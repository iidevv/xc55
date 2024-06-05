<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVaultReadonlyQty\Module\XC\ProductVariants\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;
use XLite\Core\Database;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductVariants")
 */
class ProductVariant extends \XC\ProductVariants\Model\ProductVariant
{
    /**
     * @ORM\PostPersist
     */
    public function processPostPersist()
    {
        $product = $this->getProduct();
        if (!$product->isSkippedFromSync()) {
            $em = Database::getEM();
            $em->addAfterFlushCallback(function () use ($em) {
                if (!$this->getSku()) {
                    $sku = $this->getRepository()->assembleUniqueSKU($this->getProduct()->getSku());
                    $this->setSku($sku);
                    $em->flush();
                }
            });
        }
    }
}
