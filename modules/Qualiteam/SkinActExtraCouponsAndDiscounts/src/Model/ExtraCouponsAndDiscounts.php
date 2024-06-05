<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActExtraCouponsAndDiscounts\Model;

use Doctrine\ORM\Mapping as ORM;
use XLite\Core\Database;

/**
 * @ORM\Entity
 * @ORM\Table  (name="extra_coupons_and_discounts")
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\MappedSuperclass
 */
class ExtraCouponsAndDiscounts extends \XLite\Model\Base\I18n
{
    public const TYPE_PERCENT  = '%';
    public const TYPE_ABSOLUTE = '$';

    /**
     * Node unique ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Stamp text 1 field
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $stamp_text_1;

    /**
     * Stamp text 2 field
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $stamp_text_2;

    /**
     * Coupon code
     *
     * @var string
     *
     * @ORM\Column (type="string", options={ "fixed": true }, length=16)
     */
    protected $coupon_code;

    /**
     * @var   string
     *
     * @ORM\Column (type="string", options={ "fixed": true }, length=1)
     */
    protected $type = self::TYPE_PERCENT;

    /**
     * @var int
     *
     * @ORM\Column (type="decimal", precision=14, scale=4)
     */
    protected $value = 0;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="Qualiteam\SkinActExtraCouponsAndDiscounts\Model\ExtraCouponsAndDiscountsTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * @var \CDev\Coupons\Model\Coupon
     *
     * @ORM\OneToOne (targetEntity="CDev\Coupons\Model\Coupon", cascade={"all"})
     * @ORM\JoinColumn (name="coupon_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $coupon;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getStampText1()
    {
        return $this->stamp_text_1;
    }

    /**
     * @param string $stamp_text_1
     */
    public function setStampText1($stamp_text_1)
    {
        $this->stamp_text_1 = $stamp_text_1;
    }

    /**
     * @return string
     */
    public function getStampText2()
    {
        return $this->stamp_text_2;
    }

    /**
     * @param string $stamp_text_2
     */
    public function setStampText2($stamp_text_2)
    {
        $this->stamp_text_2 = $stamp_text_2;
    }

    /**
     * @return string
     */
    public function getCouponCode()
    {
        return $this->coupon_code;
    }

    /**
     * @param string $coupon_code
     */
    public function setCouponCode($coupon_code)
    {
        $this->coupon_code = $coupon_code;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param int $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getTitle()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    public function setTitle($title)
    {
        return $this->setTranslationField(__FUNCTION__, $title);
    }

    public function getDescription()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    public function setDescription($description)
    {
        return $this->setTranslationField(__FUNCTION__, $description);
    }

    public function getAdditionalContent()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    public function setAdditionalContent($content)
    {
        return $this->setTranslationField(__FUNCTION__, $content);
    }

    public function getCoupon()
    {
        return $this->coupon;
    }

    public function setCoupon($coupon)
    {
        $this->coupon = $coupon;
    }

    public function isAbsolute()
    {
        return $this->getType() === static::TYPE_ABSOLUTE;
    }
}