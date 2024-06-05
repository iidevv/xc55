<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Container;
use XCart\Domain\ModuleManagerDomain;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\After("Qualiteam\SkinActSkuVaultReadonlyQty")
 */
class Product extends \XLite\Model\Product
{
    protected function getModuleManagerDomain()
    {
        return Container::getContainer()->get(ModuleManagerDomain::class);
    }

    /**
     * Was this product synced with SkuVault or not
     *
     * @var boolean
     * @ORM\Column (type="boolean", nullable=true)
     */
    protected $isSkuvaultSynced = false;

    /**
     * Whether to skip product from syncing to SkuVault
     *
     * @var boolean
     *
     * @ORM\Column (name="skip_sync_to_skuvault", type="boolean", nullable=true)
     */
    protected $skipSyncToSkuvault = false;

    /**
     * @var bool
     * @ORM\Column (type="boolean", nullable=true)
     */
    protected $isSkuvaultUpdateSynced = false;

    /**
     * Getter (must not be decorated)
     *
     * @return bool
     */
    public function getSkipSyncToSkuvault()
    {
        return (bool)$this->skipSyncToSkuvault;
    }

    /**
     * @param bool $skipSyncToSkuvault
     * @return Product
     */
    public function setSkipSyncToSkuvault($skipSyncToSkuvault)
    {
        $this->skipSyncToSkuvault = (bool)$skipSyncToSkuvault;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsSkuvaultSynced()
    {
        return $this->isSkuvaultSynced;
    }

    /**
     * @param bool $isSkuvaultSynced
     * @return Product
     */
    public function setIsSkuvaultSynced($isSkuvaultSynced)
    {
        $this->isSkuvaultSynced = $isSkuvaultSynced;
        return $this;
    }

    /**
     * Method that indicates condition of skipping from SkuVault including additional circumstances (brand, membership etc.)
     *
     * @return bool
     */
    public function isSkippedFromSync()
    {
        return $this->getSkipSyncToSkuvault() || $this->isBrandSkipped() || $this->isProductPaidMembership();
    }

    protected function isBrandSkipped()
    {
        return $this->isPersistent()
            && $this->getModuleManagerDomain()->isEnabled('QSL-ShopByBrand')
            && $this->getBrand()
            && $this->getBrand()->getSkipSyncToSkuvault();
    }

    /**
     * @return bool
     */
    protected function isProductPaidMembership()
    {
        return $this->isPersistent()
            && $this->getModuleManagerDomain()->isEnabled('QSL-MembershipProducts')
            && $this->getAppointmentMembership();
    }

    public function getAbsoluteSalePriceValue()
    {
        $price = $this->getPrice();
        $moduleManagerDomain = Container::getContainer()->get(ModuleManagerDomain::class);

        if (
            $moduleManagerDomain->isEnabled('CDev-Sale')
            && $this->getParticipateSale()
        ) {
            if ($this->getDiscountType() === \XLite\Model\Product::SALE_DISCOUNT_TYPE_PERCENT) {
                $price = $price * (1 - $this->getSalePriceValue() / 100);
            } else {
                $price = $this->getSalePriceValue();
            }
        }

        return $price;
    }

    public function getIsSkuvaultUpdateSynced(): ?bool
    {
        return $this->isSkuvaultUpdateSynced;
    }

    public function setIsSkuvaultUpdateSynced(?bool $isSkuvaultUpdateSynced): void
    {
        $this->isSkuvaultUpdateSynced = $isSkuvaultUpdateSynced;
    }

    /**
     * @ORM\PostUpdate
     *
     * @return void
     */
    public function processPostUpdate()
    {
        $this->setIsSkuvaultUpdateSynced(false);
    }
}
