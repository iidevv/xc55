<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActShipStationAdvanced\Module\XC\ProductVariants\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Converter;

/**
 * @Extender\Mixin
 */
class ProductVariant extends \XC\ProductVariants\Model\ProductVariant
{
    /**
     * Default flag
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean", nullable=true)
     */
    protected $prepareToSyncShipStation = false;

    public function getPrepareToSyncShipStation(): ?bool
    {
        return $this->prepareToSyncShipStation;
    }

    public function setPrepareToSyncShipStation($value): void
    {
        $this->prepareToSyncShipStation = (bool) $value;
    }

    public function setWeight($weight)
    {
        $weight = Converter::toUnsigned32BitFloat($weight);

        if ($weight !== $this->getWeight()) {
            $this->defineToSyncShipStation();
        }

        return parent::setWeight($weight);
    }

    protected function defineToSyncShipStation(): void
    {
        $this->setPrepareToSyncShipStation(true);
    }
}
