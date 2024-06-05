<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Payment;

use Doctrine\ORM\Mapping as ORM;

/**
 * Payment method
 *
 * @ORM\Entity
 * @ORM\Table  (name="payment_method_country_position",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint (name="method_country", columns={"method_id","countryCode"})
 *      },
 *      indexes={
 *          @ORM\Index (name="adminPosition", columns={"adminPosition"}),
 *      }
 * )
 */
class MethodCountryPosition extends \XLite\Model\AEntity
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer")
     */
    protected $id;

    /**
     * Payment method
     *
     * @var \XLite\Model\Payment\Method
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Payment\Method", inversedBy="countryPositions")
     * @ORM\JoinColumn (name="method_id", referencedColumnName="method_id", onDelete="CASCADE")
     */
    protected $paymentMethod;

    /**
     * Country code
     *
     * @var string
     *
     * @ORM\Column (type="string", options={ "fixed": true }, length=2)
     */
    protected $countryCode;

    /**
     * Position in popup
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $adminPosition = 0;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return Method
     */
    public function getPaymentMethod(): Method
    {
        return $this->paymentMethod;
    }

    /**
     * @param Method $paymentMethod
     */
    public function setPaymentMethod(Method $paymentMethod): void
    {
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * @param string $countryCode
     */
    public function setCountryCode(string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    /**
     * @return int
     */
    public function getAdminPosition(): int
    {
        return $this->adminPosition;
    }

    /**
     * @param int $adminPosition
     */
    public function setAdminPosition(int $adminPosition): void
    {
        $this->adminPosition = $adminPosition;
    }
}
