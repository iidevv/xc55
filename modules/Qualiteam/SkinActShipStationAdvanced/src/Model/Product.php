<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActShipStationAdvanced\Model;

use Doctrine\ORM\Mapping as ORM;
use XC\ProductVariants\Model\ProductVariant;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Converter;
use XLite\Core\Database;

/**
 * @Extender\Mixin
 * @Extender\After ("XC\ProductVariants")
 *
 * @ORM\HasLifecycleCallbacks
 */
class Product extends \XLite\Model\Product
{
    protected array $variantIds = [];

    /**
     * Default flag
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean", nullable=true)
     */
    protected $prepareToSyncShipStation = false;

    public function setBoxHeight($boxHeight)
    {
        $boxHeight = Converter::toUnsigned32BitFloat($boxHeight);

        if ($boxHeight !== $this->getBoxHeight()) {
            $this->definePrepareToSync();
        }

        return parent::setBoxHeight($boxHeight);
    }

    protected function definePrepareToSync(): void
    {
        $this->definePrepareToSyncShipstation();
        $this->definePrepareToSyncVariants();
    }

    protected function definePrepareToSyncShipstation(): void
    {
        if (!$this->getPrepareToSyncShipStation()) {
            $this->setPrepareToSyncShipStation(true);
        }
    }

    public function getPrepareToSyncShipStation(): ?bool
    {
        return $this->prepareToSyncShipStation;
    }

    public function setPrepareToSyncShipStation($value): void
    {
        $this->prepareToSyncShipStation = (bool) $value;
    }

    protected function definePrepareToSyncVariants(): void
    {
        if ($this->hasVariants()) {
            foreach ($this->getVariants() as $variant) {
                if (!in_array($variant->getId(), $this->variantIds, true)) {
                    $this->variantIds[] = $variant->getId();
                }
            }
        }
    }

    public function setBoxWidth($boxWidth)
    {
        $boxWidth = Converter::toUnsigned32BitFloat($boxWidth);

        if ($boxWidth !== $this->getBoxWidth()) {
            $this->definePrepareToSync();
        }

        return parent::setBoxWidth($boxWidth);
    }

    public function setBoxLength($boxLength)
    {
        $boxLength = Converter::toUnsigned32BitFloat($boxLength);

        if ($boxLength !== $this->getBoxLength()) {
            $this->definePrepareToSync();
        }

        return parent::setBoxLength($boxLength);
    }

    public function setWeight($weight)
    {
        $weight = Converter::toUnsigned32BitFloat($weight);

        if ($weight !== $this->getWeight()) {
            $this->definePrepareToSyncShipstation();
            $this->definePrepareVariantDefaultWeightIds();
        }

        return parent::setWeight($weight);
    }

    protected function definePrepareVariantDefaultWeightIds(): void
    {
        if ($this->hasVariants()) {
            foreach ($this->getVariants() as $variant) {
                if ($variant->getDefaultWeight()) {
                    $this->variantIds[] = $variant->getId();
                }
            }
        }
    }

    /**
     * @ORM\PostUpdate
     */
    public function processPostUpdate()
    {
        parent::processPostUpdate();

        if (count($this->variantIds) > 0) {
            Database::getRepo(ProductVariant::class)
                ->prepareProductVariantToSyncShipStation($this->variantIds);
        }
    }
}