<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\AvaTax\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Profile
 * @Extender\Mixin
 */
class Profile extends \XLite\Model\Profile
{
    /**
     * AvaxTax exemption number
     *
     * @var string
     *
     * @ORM\Column (type="string", length=25, nullable=true)
     */
    protected $avaTaxExemptionNumber;

    /**
     * AvaxTax exemption number
     *
     * @var string
     *
     * @ORM\Column (type="string", length=4, nullable=true)
     */
    protected $avaTaxCustomerUsageType;


    /**
     * Set avaTaxExemptionNumber
     *
     * @param string $avaTaxExemptionNumber
     * @return Profile
     */
    public function setAvaTaxExemptionNumber($avaTaxExemptionNumber)
    {
        $this->avaTaxExemptionNumber = $avaTaxExemptionNumber;
        return $this;
    }

    /**
     * Get avaTaxExemptionNumber
     *
     * @return string
     */
    public function getAvaTaxExemptionNumber()
    {
        return $this->avaTaxExemptionNumber;
    }

    /**
     * Set avaTaxCustomerUsageType
     *
     * @param string $avaTaxCustomerUsageType
     * @return Profile
     */
    public function setAvaTaxCustomerUsageType($avaTaxCustomerUsageType)
    {
        $this->avaTaxCustomerUsageType = $avaTaxCustomerUsageType;
        return $this;
    }

    /**
     * Get avaTaxCustomerUsageType
     *
     * @return string
     */
    public function getAvaTaxCustomerUsageType()
    {
        return $this->avaTaxCustomerUsageType;
    }
}
