<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Module\XC\ProductVariants\Model;

use Qualiteam\SkinActSkuVault\Core\Dispatcher\CreateVariantDispatcher;
use Symfony\Component\Messenger\MessageBusInterface;
use XCart\Container;
use XCart\Domain\ModuleManagerDomain;
use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;
use XLite\Core\Database;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductVariants")
 * @Extender\After("Qualiteam\SkinActSkuVaultReadonlyQty")
 */
class ProductVariant extends \XC\ProductVariants\Model\ProductVariant
{
    protected ?MessageBusInterface $bus;

    /**
     * Was this product variant synced with SkuVault or not
     *
     * @var boolean
     * @ORM\Column (type="boolean", nullable=true)
     */
    protected $isSkuvaultSynced = false;

    /**
     * Whether to skip product variant from syncing to SkuVault
     *
     * @var boolean
     *
     * @ORM\Column (name="skip_sync_to_skuvault", type="boolean", nullable=true)
     */
    protected $skipSyncToSkuvault = false;

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
     *
     * @return \Qualiteam\SkinActSkuVault\Module\XC\ProductVariants\Model\ProductVariant
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
     *
     * @return \Qualiteam\SkinActSkuVault\Module\XC\ProductVariants\Model\ProductVariant
     */
    public function setIsSkuvaultSynced($isSkuvaultSynced)
    {
        $this->isSkuvaultSynced = $isSkuvaultSynced;
        return $this;
    }

    public function __construct(array $data = [])
    {
        parent::__construct($data);

        $this->bus = Container::getContainer() ? Container::getContainer()->get('messenger.default_bus') : null;
    }

    /**
     * @ORM\PostPersist
     */
    public function processPostPersist()
    {
        parent::processPostPersist();

        $product = $this->getProduct();
        if (!$product->isSkippedFromSync()) {
            $em = Database::getEM();
            $em->addAfterFlushCallback(function () use ($em) {

                $dispatcher = new CreateVariantDispatcher($this->getId());
                $message    = $dispatcher->getMessage();
                $this->bus->dispatch($message);

            });
        }
    }

    public function getAbsoluteSalePriceValue()
    {
        $price = $this->getPrice();
        $moduleManagerDomain = Container::getContainer()->get(ModuleManagerDomain::class);

        if (
            $moduleManagerDomain->isEnabled('CDev-Sale')
        ) {
            if (!$this->getDefaultSale()) {
                if ($this->getDiscountType() === \XLite\Model\Product::SALE_DISCOUNT_TYPE_PERCENT) {
                    $price = $price * (1 - $this->getSalePriceValue() / 100);
                } else {
                    $price = $this->getSalePriceValue();
                }
            } elseif ($this->getProduct()->getParticipateSale()) {
                if ($this->getProduct()->getDiscountType() === \XLite\Model\Product::SALE_DISCOUNT_TYPE_PERCENT) {
                    $price = $price * (1 - $this->getProduct()->getSalePriceValue() / 100);
                } else {
                    $price = $this->getProduct()->getSalePriceValue();
                }
            }
        }

        return $price;
    }
}
