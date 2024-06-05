<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Shipping method multilingual data
 *
 * @ORM\Entity
 * @ORM\Table (
 *     name="sale_discount_translations",
 *     indexes={
 *         @ORM\Index (name="ci", columns={"code","id"}),
 *         @ORM\Index (name="id", columns={"id"})
 *     }
 * )
 */
class SaleDiscountTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Sale discount name
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255, nullable=false)
     */
    protected $name = '';

    /**
     * Sale discount meta keywords
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $metaTags = '';

    /**
     * Sale discount meta description
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $metaDesc = '';

    /**
     * Value of the title HTML-tag for category page
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $metaTitle = '';

    /**
     * @var \CDev\Sale\Model\SaleDiscount
     *
     * @ORM\ManyToOne (targetEntity="CDev\Sale\Model\SaleDiscount", inversedBy="translations")
     * @ORM\JoinColumn (name="id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $owner;

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
     */
    public function setCode($code)
    {
        $this->code = $code;
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

    /**
     * @return string
     */
    public function getMetaTags()
    {
        return $this->metaTags;
    }

    /**
     * @param string $metaTags
     */
    public function setMetaTags($metaTags)
    {
        $this->metaTags = $metaTags;
    }

    /**
     * @return string
     */
    public function getMetaDesc()
    {
        return $this->metaDesc;
    }

    /**
     * @param string $metaDesc
     */
    public function setMetaDesc($metaDesc)
    {
        $this->metaDesc = $metaDesc;
    }

    /**
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * @param string $metaTitle
     */
    public function setMetaTitle($metaTitle)
    {
        $this->metaTitle = $metaTitle;
    }
}
