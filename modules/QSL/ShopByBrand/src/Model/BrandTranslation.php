<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Reminder multilingual data.
 *
 * @ORM\Entity
 * @ORM\Table  (name="brand_translations",
 *      indexes={
 *          @ORM\Index (name="ci", columns={"code","id"}),
 *          @ORM\Index (name="id", columns={"id"})
 *      }
 * )
 */
class BrandTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Formatted brand description.
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $description = '';

    /**
     * Value of the title HTML-tag for category page
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $metaTitle = '';

    /**
     * Formatted brand description.
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $metaDescription = '';

    /**
     * Meta keywords
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $metaKeywords = '';

    /**
     * @var \QSL\ShopByBrand\Model\Brand
     *
     * @ORM\ManyToOne (targetEntity="QSL\ShopByBrand\Model\Brand", inversedBy="translations")
     * @ORM\JoinColumn (name="id", referencedColumnName="brand_id", onDelete="CASCADE")
     */
    protected $owner;

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
     * Set description
     *
     * @param string $description
     *
     * @return BrandTranslation
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get metaTitle
     *
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * Set metaTitle
     *
     * @param string $metaTitle
     *
     * @return BrandTranslation
     */
    public function setMetaTitle($metaTitle)
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    /**
     * Get metaDescription
     *
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * Set metaDescription
     *
     * @param string $metaDescription
     *
     * @return BrandTranslation
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * Get metaKeywords
     *
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * Set metaKeywords
     *
     * @param string $metaKeywords
     *
     * @return BrandTranslation
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;

        return $this;
    }

    /**
     * Get label_id
     *
     * @return int
     */
    public function getLabelId()
    {
        return $this->label_id;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return BrandTranslation
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
