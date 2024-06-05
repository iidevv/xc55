<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Data\Converter;

use Qualiteam\SkinActSkuVault\Core\Command\Push\Hydrator\BaseConverter;
use Qualiteam\SkinActSkuVault\Main;
use XCart\Domain\ModuleManagerDomain;
use XLite\Core\Config;
use XLite\Model\AEntity;
use XLite\Model\Base\Image;
use XLite\Model\Product;

class PushProductConverter extends BaseConverter
{
    /**
     * @param Product $entity
     * @return AEntity[]
     */
    public function convert(AEntity $entity): array
    {
        $shortDescription = Main::getFormattedDescription($entity->getBriefDescription());
        $description = Main::getFormattedDescription($entity->getDescription());

        return [
            'Sku'              => $entity->getSku(),
            'Description'      => $entity->getName(),
            'ShortDescription' => mb_strimwidth($shortDescription, 0, 997, '...'),
            'LongDescription'  => $description,
            'Classification'   => Config::getInstance()->Qualiteam->SkinActSkuVault->skuvault_classification,
            'Supplier'         => Config::getInstance()->Qualiteam->SkinActSkuVault->skuvault_supplier,
            'Brand'            => Config::getInstance()->Qualiteam->SkinActSkuVault->skuvault_brand,
            'Cost'             => $entity->getPrice(),
            'SalePrice'        => $entity->getAbsoluteSalePriceValue(),
            'RetailPrice'      => $entity->getPrice(),
            'Weight'           => $entity->getWeight(),
            'WeightUnit'       => Config::getInstance()->Units->weight_unit,
            'Pictures'         => array_map(function (Image $image) {
                return $image->getURL();
            }, $entity->getImages()->toArray()),
        ];
    }
}
