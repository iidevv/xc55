<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * Order
 * 
 * @ORM\Table  (name="orders",
 *      indexes={
 *          @ORM\Index (name="qbc_ignore", columns={"qbc_ignore"}),
 *      }
 * )
 * 
 * @Extender\Mixin
 */
class Order extends \XLite\Model\Order
{
    /**
     * Do not import this order to QuickBooks
     *
     * @var string
     *
     * @ORM\Column (type="string", length=1, options={"default": "N"})
     */
    protected $qbc_ignore = 'N';
    
    /**
     * Set qbc_ignore
     *
     * @param string $qbcIgnore
     * 
     * @return Order
     */
    public function setQbcIgnore($qbcIgnore)
    {
        $this->qbc_ignore = $qbcIgnore;
        
        return $this;
    }

    /**
     * Get qbc_ignore
     *
     * @return string
     */
    public function getQbcIgnore()
    {
        return $this->qbc_ignore;
    }
}