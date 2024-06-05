<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Decorated product model.
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Product
{
    /**
     * Linked Nextag category.
     *
     * @var \QSL\ProductFeeds\Model\NextagCategory
     *
     * @ORM\ManyToOne  (targetEntity="QSL\ProductFeeds\Model\NextagCategory", inversedBy="products")
     * @ORM\JoinColumn (name="nextagCategory", referencedColumnName="nextag_id", nullable=true, onDelete="SET NULL")
     */
    protected $nextagCategory;

    /**
     * Linked Shopzilla category.
     *
     * @var \QSL\ProductFeeds\Model\ShopzillaCategory
     *
     * @ORM\ManyToOne  (targetEntity="QSL\ProductFeeds\Model\ShopzillaCategory", inversedBy="products")
     * @ORM\JoinColumn (name="shopzillaCategory", referencedColumnName="shopzilla_id", nullable=true, onDelete="SET NULL")
     */
    protected $shopzillaCategory;

    /**
     * Linked Pricegrabber category.
     *
     * @var \QSL\ProductFeeds\Model\PricegrabberCategory
     *
     * @ORM\ManyToOne  (targetEntity="QSL\ProductFeeds\Model\PricegrabberCategory", inversedBy="products")
     * @ORM\JoinColumn (name="pricegrabberCategory", referencedColumnName="pricegrabber_id", nullable=true, onDelete="SET NULL")
     */
    protected $pricegrabberCategory;

    /**
     * Linked eBay Commerce Network category.
     *
     * @var \QSL\ProductFeeds\Model\EBayCommerceCategory
     *
     * @ORM\ManyToOne  (targetEntity="QSL\ProductFeeds\Model\EBayCommerceCategory", inversedBy="products")
     * @ORM\JoinColumn (name="eBayCommerceCategory", referencedColumnName="ebay_id", nullable=true, onDelete="SET NULL")
     */
    protected $eBayCommerceCategory;

    /**
     * Linked Google Shopping category.
     *
     * @var \QSL\ProductFeeds\Model\GoogleShoppingCategory
     *
     * @ORM\ManyToOne  (targetEntity="QSL\ProductFeeds\Model\GoogleShoppingCategory", inversedBy="products")
     * @ORM\JoinColumn (name="googleShoppingCategory", referencedColumnName="google_id", nullable=true, onDelete="SET NULL")
     */
    protected $googleShoppingCategory;

    /**
     * Get VAT Price
     *
     * @return float
     */
    public function getVatPrice()
    {
        return \XLite\Logic\Price::getInstance()->apply($this, 'getDisplayPrice', ['taxable'], 'vat');
    }

    /**
     * Set the Nextag category model by its ID.
     *
     * @param integer $id ID of the Nextag category.
     *
     * @return void
     */
    public function setNextagId($id)
    {
        $this->setNextagCategory($this->getFeedCategoryModel('nextagCategory', $id));
    }

    /**
     * Set the Shopzilla category model by its ID.
     *
     * @param integer $id Id of the Shopzilla category.
     *
     * @return void
     */
    public function setShopzillaId($id)
    {
        $this->setShopzillaCategory($this->getFeedCategoryModel('shopzillaCategory', $id));
    }

    /**
     * Set the Pricegrabber category model by its ID.
     *
     * @param integer $id ID of the Pricegrabber category.
     *
     * @return void
     */
    public function setPricegrabberId($id)
    {
        $this->setPricegrabberCategory($this->getFeedCategoryModel('pricegrabberCategory', $id));
    }

    /**
     * Set the eBay Commerce Network category model by its ID.
     *
     * @param integer $id Id of the eBay Commerce Network category.
     *
     * @return void
     */
    public function setEbayId($id)
    {
        $this->setEBayCommerceCategory($this->getFeedCategoryModel('eBayCommerceCategory', $id));
    }

    /**
     * Set the Google Shopping category model by its ID.
     *
     * @param integer $id Id of the Google Shopping category.
     *
     * @return void
     */
    public function setGoogleId($id)
    {
        $this->setGoogleShoppingCategory($this->getFeedCategoryModel('googleShoppingCategory', $id));
    }

    /**
     * Set the Google Shopping category model by its Name.
     *
     * @param integer $name Name of the Google Shopping category.
     *
     * @return void
     */
    public function setGoogleCategory($name)
    {
        $category = \XLite\Core\Database::getRepo('\QSL\ProductFeeds\Model\GoogleShoppingCategory')
            ->findOneByName($name);

        $this->setGoogleShoppingCategory($category);
    }

    /**
     * Get model object for the requested feed category model.
     *
     * @param string $name Name of the feed category field
     * @param mixed  $id   Id of the feed category model
     *
     * @return mixed
     */
    protected function getFeedCategoryModel($name, $id)
    {
        return \XLite\Core\Database::getRepo('\QSL\ProductFeeds\Model\\' . ucfirst($name))->find($id);
    }

    /**
     * Sets the Nextag category model.
     *
     * @param \QSL\ProductFeeds\Model\NextagCategory $nextagCategory Entity
     *
     * @return Product
     */
    public function setNextagCategory(\QSL\ProductFeeds\Model\NextagCategory $nextagCategory = null)
    {
        $this->nextagCategory = $nextagCategory;

        return $this;
    }

    /**
     * Returns the Nextag category model.
     *
     * @return \QSL\ProductFeeds\Model\NextagCategory
     */
    public function getNextagCategory()
    {
        return $this->nextagCategory;
    }

    /**
     * Sets the Shopzilla category model.
     *
     * @param \QSL\ProductFeeds\Model\ShopzillaCategory $shopzillaCategory Entity
     *
     * @return Product
     */
    public function setShopzillaCategory(\QSL\ProductFeeds\Model\ShopzillaCategory $shopzillaCategory = null)
    {
        $this->shopzillaCategory = $shopzillaCategory;

        return $this;
    }

    /**
     * Returns the Shopzilla category model.
     *
     * @return \QSL\ProductFeeds\Model\ShopzillaCategory
     */
    public function getShopzillaCategory()
    {
        return $this->shopzillaCategory;
    }

    /**
     * Sets the Pricegrabber category model.
     *
     * @param \QSL\ProductFeeds\Model\PricegrabberCategory $pricegrabberCategory Entity
     *
     * @return Product
     */
    public function setPricegrabberCategory(\QSL\ProductFeeds\Model\PricegrabberCategory $pricegrabberCategory = null)
    {
        $this->pricegrabberCategory = $pricegrabberCategory;

        return $this;
    }

    /**
     * Returns the Pricegrabber category model.
     *
     * @return \QSL\ProductFeeds\Model\PricegrabberCategory
     */
    public function getPricegrabberCategory()
    {
        return $this->pricegrabberCategory;
    }

    /**
     * Sets the eBay Commerce category model.
     *
     * @param \QSL\ProductFeeds\Model\EBayCommerceCategory $eBayCommerceCategory Entity
     *
     * @return Product
     */
    public function setEBayCommerceCategory(\QSL\ProductFeeds\Model\EBayCommerceCategory $eBayCommerceCategory = null)
    {
        $this->eBayCommerceCategory = $eBayCommerceCategory;

        return $this;
    }

    /**
     * Returns the eBay Commerce category model.
     *
     * @return \QSL\ProductFeeds\Model\EBayCommerceCategory
     */
    public function getEBayCommerceCategory()
    {
        return $this->eBayCommerceCategory;
    }

    /**
     * Sets the Google Shopping category model.
     *
     * @param \QSL\ProductFeeds\Model\GoogleShoppingCategory $googleShoppingCategory Entity
     *
     * @return Product
     */
    public function setGoogleShoppingCategory(\QSL\ProductFeeds\Model\GoogleShoppingCategory $googleShoppingCategory = null)
    {
        $this->googleShoppingCategory = $googleShoppingCategory;

        return $this;
    }

    /**
     * Returns the Google Shopping category model.
     *
     * @return \QSL\ProductFeeds\Model\GoogleShoppingCategory
     */
    public function getGoogleShoppingCategory()
    {
        return $this->googleShoppingCategory;
    }

    /**
     * Clone entity (model fields)
     *
     * @param \XLite\Model\Product $newProduct New product
     *
     * @return void
     */
    protected function cloneEntityModels(\XLite\Model\Product $newProduct)
    {
        parent::cloneEntityModels($newProduct);

        $newProduct->setShopzillaCategory($this->getShopzillaCategory());
        $newProduct->setNextagCategory($this->getNextagCategory());
        $newProduct->setPricegrabberCategory($this->getPricegrabberCategory());
        $newProduct->setGoogleShoppingCategory($this->getGoogleShoppingCategory());
        $newProduct->setEBayCommerceCategory($this->getEBayCommerceCategory());
    }
}
