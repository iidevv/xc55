<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;

/**
 * @Extender\Mixin
 *
 * @ORM\HasLifecycleCallbacks
 */
class Order extends \XLite\Model\Order
{
    const NOT_SYNC_NO = 'N';
    const NOT_SYNC_YES = 'Y';

    /**
     * Whether to skip order from syncing to SkuVault
     *
     * @var boolean
     *
     * @ORM\Column (name="skuvault_not_sync", type="string", nullable=true, options={ "fixed": true, "default": "N" }, length=1)
     */
    protected $skuvaultNotSync = self::NOT_SYNC_NO;

    /**
     * @return bool
     */
    public function isSkuvaultNotSync()
    {
        return $this->skuvaultNotSync;
    }

    /**
     * @param bool $skuvaultNot_Sync
     * @return Order
     */
    public function setSkuvaultNotSync($skuvaultNotSync)
    {
        $this->skuvaultNotSync = $skuvaultNotSync;
        return $this;
    }

    public function getStatusesMapXcToSkuvault(): ?StatusesMap
    {
        /** @var StatusesMap $result */
        $result = Database::getRepo(StatusesMap::class)->findOneBy([
            'xcartPaymentStatus'     => $this->getPaymentStatus()->getId(),
            'xcartFullfilmentStatus' => $this->getShippingStatus()->getId(),
            'direction'              => StatusesMap::DIRECTION_XC_TO_SKUVAULT,
        ]);

        return $result;
    }

    public function getStatusesMapSkuvaultToXc(string $skuvaultSaleStatus): ?StatusesMap
    {
        /** @var StatusesMap $result */
        $result = Database::getRepo(StatusesMap::class)->findOneBy([
            'skuvaultSaleStatus'     => $skuvaultSaleStatus,
            'direction'              => StatusesMap::DIRECTION_SKUVAULT_TO_XC,
        ]);

        return $result;
    }

    /**
     * @ORM\PostUpdate
     *
     * @return void
     */
    public function processPostUpdate()
    {
        $this->setSkuvaultNotSync(Order::NOT_SYNC_NO);
    }
}
