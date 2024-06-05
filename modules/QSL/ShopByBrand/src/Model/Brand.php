<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\Model;

use Doctrine\ORM\Mapping as ORM;
use XLite\Core\Database;

/**
 * Class represents Brand model.
 *
 * @ORM\Entity (repositoryClass="\QSL\ShopByBrand\Model\Repo\Brand")
 * @ORM\Table  (name="brands",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="option_brand", columns={"attribute_option_id", "brand_id"})
 *      }
 * )
 * @ORM\HasLifecycleCallbacks
 */
class Brand extends \XLite\Model\Base\Catalog
{
    /**
     * Brand ID.
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $brand_id;

    /**
     * The attribute option used to indicate products of this brand.
     *
     * @var \XLite\Model\AttributeOption
     *
     * @ORM\OneToOne (targetEntity="XLite\Model\AttributeOption")
     * @ORM\JoinColumn (name="attribute_option_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    protected $option;

    /**
     * One-to-one relation with brand_images table
     *
     * @var \QSL\ShopByBrand\Model\Image\Brand\Image
     *
     * @ORM\OneToOne  (targetEntity="QSL\ShopByBrand\Model\Image\Brand\Image", mappedBy="brand", cascade={"remove"})
     */
    protected $image;

    /**
     * Position of the brand among other ones in the list.
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $position = 0;

    /**
     * Enabled flag
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $enabled = true;

    /**
     * Clean URLs
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\CleanURL", mappedBy="brand", cascade={"all"})
     * @ORM\OrderBy   ({"id" = "ASC"})
     */
    protected $cleanURLs;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="QSL\ShopByBrand\Model\BrandTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * Get the brand name stored in the associate attribute option.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getOption() ? $this->getOption()->getName() : '';
    }

    /**
     * Get the model ID.
     *
     * @return int
     */
    public function getId()
    {
        return $this->getBrandId();
    }

    /**
     * Get brand products.
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition OPTIONAL
     * @param bool                   $countOnly Whether to count matching product, or return the list of them OPTIONAL
     *
     * @return array|integer
     */
    public function getProducts(\XLite\Core\CommonCell $cnd = null, $countOnly = false)
    {
        if (!isset($cnd)) {
            $cnd = new \XLite\Core\CommonCell();
        }

        // Main condition for this search
        $cnd->{\XLite\Model\Repo\Product::P_BRAND_ID} = $this->getBrandId();

        if (\XLite\Core\Config::getInstance()->General->show_out_of_stock_products === 'directLink') {
            $cnd->{\XLite\Model\Repo\Product::P_INVENTORY} = \XLite\Model\Repo\Product::INV_IN;
        }

        return Database::getRepo('XLite\Model\Product')->search($cnd, $countOnly);
    }

    /**
     * Check if the brand has logo.
     *
     * @return bool
     */
    public function hasImage()
    {
        return is_object($this->getImage());
    }

    /**
     * Get brand_id
     *
     * @return int
     */
    public function getBrandId()
    {
        return $this->brand_id;
    }

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set position
     *
     * @param int $position
     *
     * @return Brand
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get option
     *
     * @return \XLite\Model\AttributeOption
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * Set option
     *
     * @param \XLite\Model\AttributeOption $option
     *
     * @return Brand
     */
    public function setOption(\XLite\Model\AttributeOption $option = null)
    {
        $this->option = $option;
        $this->setCleanUrlIfEmpty();

        return $this;
    }

    protected function setCleanUrlIfEmpty()
    {
        if (\XLite\Core\Converter::isEmptyString($this->getCleanURL())) {
            $cleanUrl = Database::getRepo('XLite\Model\CleanURL')->generateCleanURL($this);
            $this->setCleanURL($cleanUrl);
        }
    }

    /**
     * Get image
     *
     * @return \QSL\ShopByBrand\Model\Image\Brand\Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set image
     *
     * @param \QSL\ShopByBrand\Model\Image\Brand\Image $image
     *
     * @return Brand
     */
    public function setImage(\QSL\ShopByBrand\Model\Image\Brand\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Add clean URL
     *
     * @param \XLite\Model\CleanURL $cleanURLs
     *
     * @return Brand
     */
    public function addCleanURLs(\XLite\Model\CleanURL $cleanURLs)
    {
        $this->cleanURLs[] = $cleanURLs;

        return $this;
    }

    /**
     * Get clean URLs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCleanURLs()
    {
        return $this->cleanURLs;
    }

    // {{{ Translation Getters / setters

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $description
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setDescription($description)
    {
        return $this->setTranslationField(__FUNCTION__, $description);
    }
    /**
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $metaTitle
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setMetaTitle($metaTitle)
    {
        return $this->setTranslationField(__FUNCTION__, $metaTitle);
    }
    /**
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $metaDescription
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setMetaDescription($metaDescription)
    {
        return $this->setTranslationField(__FUNCTION__, $metaDescription);
    }
    /**
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $metaKeywords
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setMetaKeywords($metaKeywords)
    {
        return $this->setTranslationField(__FUNCTION__, $metaKeywords);
    }

    /**
     * Get enabled flag
     *
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set enabled flag
     *
     * @param bool $enabled
     *
     * @return Brand
     */
    public function setEnabled(bool $enabled = true)
    {
        $this->enabled = $enabled;

        return $this;
    }

    // }}}

    public function getViewDescription(): string
    {
        return static::getPreprocessedValue($this->getDescription())
            ?: $this->getDescription();
    }
}
