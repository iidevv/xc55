<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Model;

use Doctrine\ORM\Mapping as ORM;
use XLite\Model\AEntity;

/**
 * SkuVault items
 *
 * @ORM\Entity
 * @ORM\Table (name="skuvault_sales",
 *     indexes={
 *         @ORM\Index (name="orderId", columns={"order_id"}),
 *         @ORM\Index (name="status", columns={"status"}),
 *     }
 *  )
 */
class SkuvaultSales extends AEntity
{
    /**
     * Order ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column (type="integer", name="order_id", options={ "unsigned": true, "default": "0" })
     */
    protected $orderId;

    /**
     * Status
     *
     * @var string
     *
     * @ORM\Column (type="string", name="status", options={ "fixed": true, "default": "Q" }, length=2)
     */
    protected $status = 'Q';

    /**
     * Sync date
     *
     * @var integer
     *
     * @ORM\Column (type="integer", name="sync_date", options={ "default": "0" })
     */
    protected $syncDate = 0;

    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param int $orderId
     * @return SkuvaultSales
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return SkuvaultSales
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return int
     */
    public function getSyncDate()
    {
        return $this->syncDate;
    }

    /**
     * @param int $syncDate
     * @return SkuvaultSales
     */
    public function setSyncDate($syncDate)
    {
        $this->syncDate = $syncDate;
        return $this;
    }
}
