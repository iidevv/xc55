<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Order extends \XLite\Model\Order
{
    /**
     * Yotpo product id
     *
     * @var int
     *
     * @ORM\Column (type="bigint", nullable=true, options={ "unsigned": true })
     */
    protected $yotpo_id;

    /**
     * Is yotpo sync
     *
     * @var bool
     *
     * @ORM\Column (type="boolean", nullable=true, options={ "default": false })
     */
    protected $isYotpoSync = false;

    /**
     * @return int|null
     */
    public function getYotpoId(): ?int
    {
        return $this->yotpo_id;
    }

    /**
     * @param ?int $yotpoId
     *
     * @return void
     */
    public function setYotpoId(?int $yotpoId): void
    {
        $this->yotpo_id = $yotpoId;
    }

    public function cloneEntity()
    {
        $newOrder = parent::cloneEntity();

        $newOrder->setYotpoId(null);

        return $newOrder;
    }

    /**
     * @return bool|null
     */
    public function isYotpoSync(): ?bool
    {
        return $this->isYotpoSync;
    }

    /**
     * @param bool|null $isYotpoSync
     *
     * @return void
     */
    public function setIsYotpoSync(?bool $isYotpoSync): void
    {
        $this->isYotpoSync = $isYotpoSync;
    }
}