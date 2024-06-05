<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Model;

use Doctrine\ORM\Mapping as ORM;
use XLite\Model\AEntity;

/**
 * SkuVault items
 *
 * @ORM\Entity
 * @ORM\Table (name="skuvault_items",
 *     indexes={
 *         @ORM\Index (name="productId", columns={"product_id","variant_id"}),
 *         @ORM\Index (name="inventoryCreated", columns={"inventory_created"}),
 *         @ORM\Index (name="productCreated", columns={"product_created"}),
 *         @ORM\Index (name="productUpdated", columns={"product_updated"})
 *     }
 *  )
 */
class SkuvaultItem extends AEntity
{
    const CREATED     = 'Y';
    const NOT_CREATED = 'N';

    /**
     * Item SKU
     *
     * @var string
     *
     * @ORM\Column (type="string", length=32, options={ "default": "" })
     */
    protected $sku = '';

    /**
     * Product ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column (type="integer", name="product_id", options={ "unsigned": true, "default": "0" })
     */
    protected $productId;

    /**
     * Variant ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column (type="integer", name="variant_id", options={ "unsigned": true, "default": "0" })
     */
    protected $variantId;

    /**
     * Available
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column (type="integer", name="available", options={ "unsigned": true, "default": "0" })
     */
    protected $available;

    /**
     * Product created
     *
     * @var string
     *
     * @ORM\Column (type="string", name="product_created", options={ "fixed": true, "default": "N" }, length=1)
     */
    protected $productCreated = self::NOT_CREATED;

    /**
     * Inventory created
     *
     * @var string
     *
     * @ORM\Column (type="string", name="inventory_created", options={ "fixed": true, "default": "N" }, length=1)
     */
    protected $inventoryCreated = self::NOT_CREATED;

    /**
     * Product updated date
     *
     * @var integer
     *
     * @ORM\Column (type="integer", name="product_updated", options={ "default": "0" })
     */
    protected $productUpdated = 0;

    /**
     * Sync date
     *
     * @var integer
     *
     * @ORM\Column (type="integer", name="sync_date", options={ "default": "0" })
     */
    protected $syncDate = 0;

    /**
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @param string $sku
     * @return SkuvaultItem
     */
    public function setSku($sku)
    {
        $this->sku = $sku;
        return $this;
    }

    /**
     * @return int
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @param int $productId
     * @return SkuvaultItem
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
        return $this;
    }

    /**
     * @return int
     */
    public function getVariantId()
    {
        return $this->variantId;
    }

    /**
     * @param int $variantId
     * @return SkuvaultItem
     */
    public function setVariantId($variantId)
    {
        $this->variantId = $variantId;
        return $this;
    }

    /**
     * @return int
     */
    public function getAvailable()
    {
        return $this->available;
    }

    /**
     * @param int $available
     * @return SkuvaultItem
     */
    public function setAvailable($available)
    {
        $this->available = $available;
        return $this;
    }

    /**
     * @return string
     */
    public function getProductCreated()
    {
        return $this->productCreated;
    }

    /**
     * @param string $productCreated
     * @return SkuvaultItem
     */
    public function setProductCreated($productCreated)
    {
        $this->productCreated = $productCreated;
        return $this;
    }

    /**
     * @return string
     */
    public function getInventoryCreated()
    {
        return $this->inventoryCreated;
    }

    /**
     * @param string $inventoryCreated
     * @return SkuvaultItem
     */
    public function setInventoryCreated($inventoryCreated)
    {
        $this->inventoryCreated = $inventoryCreated;
        return $this;
    }

    /**
     * @return int
     */
    public function getProductUpdated()
    {
        return $this->productUpdated;
    }

    /**
     * @param int $productUpdated
     * @return SkuvaultItem
     */
    public function setProductUpdated($productUpdated)
    {
        $this->productUpdated = $productUpdated;
        return $this;
    }

    /**
     * @return int
     */
    public function getSyncDate()
    {
        return $this->syncDate;
    }

    /**
     * @param int $syncDate
     * @return SkuvaultItem
     */
    public function setSyncDate($syncDate)
    {
        $this->syncDate = $syncDate;
        return $this;
    }
}
