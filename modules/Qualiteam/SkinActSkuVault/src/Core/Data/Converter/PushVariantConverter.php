<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Data\Converter;

use Qualiteam\SkinActSkuVault\Core\Command\Push\Hydrator\BaseConverter;
use Qualiteam\SkinActSkuVault\Main;
use XC\ProductVariants\Model\ProductVariant;
use XLite\Core\Config;
use XLite\Model\AEntity;

class PushVariantConverter extends BaseConverter
{
    /**
     * @param ProductVariant $entity
     * @return AEntity[]
     */
    public function convert(AEntity $entity): array
    {
        $product = $entity->getProduct();
        $shortDescription = Main::getFormattedDescription($product->getBriefDescription());
        $description = Main::getFormattedDescription($product->getDescription());

        $result = [
            'Sku'              => $entity->getSku(),
            'Description'      => $product->getName(),
            'ShortDescription' => mb_strimwidth($shortDescription, 0, 997, '...'),
            'LongDescription'  => $description,
            'Classification'   => Config::getInstance()->Qualiteam->SkinActSkuVault->skuvault_classification,
            'Supplier'         => Config::getInstance()->Qualiteam->SkinActSkuVault->skuvault_supplier,
            'Brand'            => Config::getInstance()->Qualiteam->SkinActSkuVault->skuvault_brand,
            'Cost'             => $entity->getDefaultPrice() ? $product->getPrice() : $entity->getPrice(),
            'SalePrice'        => $entity->getDefaultSale()
                ? $product->getAbsoluteSalePriceValue()
                : $entity->getAbsoluteSalePriceValue(),
            'RetailPrice'      => $entity->getPrice(),
            'Weight'           => $entity->getWeight(),
            'WeightUnit'       => Config::getInstance()->Units->weight_unit,
        ];

        if ($entity->getImage()) {
            $result['Pictures'] = [$entity->getImage()->getURL()];
        }

        return $result;
    }
}
