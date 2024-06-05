<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActExtraCouponsAndDiscounts\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Extra coupons multilingual data
 *
 * @ORM\Entity
 * @ORM\Table  (name="extra_coupons_and_discoints_translations")
 */
class ExtraCouponsAndDiscountsTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Extra coupon tab title
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255, nullable=true)
     */
    protected $title = '';

    /**
     * Extra coupon description
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $description = '';

    /**
     * Extra coupon additional content
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $additional_content = '';

    /**
     * @var \Qualiteam\SkinActExtraCouponsAndDiscounts\Model\ExtraCouponsAndDiscounts
     *
     * @ORM\ManyToOne (targetEntity="Qualiteam\SkinActExtraCouponsAndDiscounts\Model\ExtraCouponsAndDiscounts", inversedBy="translations")
     * @ORM\JoinColumn (name="id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $owner;

    /**
     * Set name
     *
     * @param string $title
     *
     * @return ExtraCouponsAndDiscountsTranslation
     */
    public function setName($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return ExtraCouponsAndDiscountsTranslation
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set additional content
     *
     * @param string $additionalContent
     * @return ExtraCouponsAndDiscountsTranslation
     */
    public function setAdditionalContent($additionalContent)
    {
        $this->additional_content = $additionalContent;
        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getAdditionalContent()
    {
        return $this->additional_content;
    }

    /**
     * Get label_id
     *
     * @return integer
     */
    public function getLabelId()
    {
        return $this->label_id;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return ExtraCouponsAndDiscountsTranslation
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
}