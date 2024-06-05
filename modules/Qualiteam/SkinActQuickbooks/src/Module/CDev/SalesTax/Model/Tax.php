<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\Module\CDev\SalesTax\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * Tax
 *
 * @Extender\Mixin
 * @Extender\Depend("CDev\SalesTax")
 */
class Tax extends \CDev\SalesTax\Model\Tax
{
    /**
     * QuickBooks Tax Name
     *
     * @var string
     *
     * @ORM\Column(type="string", length=31, nullable=true)
     */
    protected $qb_tax_name;
    
    /**
     * Set qb_tax_name
     *
     * @param string $qb_tax_name
     * 
     * @return Tax
     */
    public function setQbTaxName($qb_tax_name)
    {
        $this->qb_tax_name = $qb_tax_name;
        
        return $this;
    }

    /**
     * Get qb_tax_name
     *
     * @return string
     */
    public function getQbTaxName()
    {
        return $this->qb_tax_name;
    }
}