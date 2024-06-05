<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFullWidthImages\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Qualiteam\SkinActFullWidthImages\Model\Image\Product\FullWidthImage;
use XCart\Extender\Mapping\Extender as Extender;

/**
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Product
{
    /**
     * Full width product images
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="\Qualiteam\SkinActFullWidthImages\Model\Image\Product\FullWidthImage", mappedBy="product",
     *                cascade={"all"})
     * @ORM\OrderBy   ({"orderby" = "ASC"})
     */
    protected $full_width_images;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->full_width_images = new ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Count product images
     *
     * @return integer
     */
    public function countFullWidthImages()
    {
        return count($this->getPublicFullWidthImages());
    }

    /**
     * Get public images
     *
     * @return array
     */
    public function getPublicFullWidthImages()
    {
        return $this->getFullWidthImages()->toArray();
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFullWidthImages()
    {
        return $this->full_width_images;
    }

    /**
     * Clone
     *
     * @return \XLite\Model\Product
     */
    public function cloneEntity()
    {
        /** @var \XLite\Model\Product $newProduct */
        $newProduct = parent::cloneEntity();

        $this->cloneEntityFullWidthImages($newProduct);

        return $newProduct;
    }

    /**
     * Clone entity (images)
     *
     * @param \XLite\Model\Product $newProduct New product
     *
     * @return void
     */
    protected function cloneEntityFullWidthImages(Product $newProduct)
    {
        foreach ($this->getFullWidthImages() as $image) {
            $newFullWidthImage = $image->cloneEntity();
            $newFullWidthImage->setProduct($newProduct);
            $newProduct->addFullWidthImages($newFullWidthImage);
        }
    }

    /**
     * Add images
     *
     * @param \Qualiteam\SkinActFullWidthImages\Model\Image\Product\FullWidthImage $image
     *
     * @return \XLite\Model\Product
     */
    public function addFullWidthImages(FullWidthImage $image)
    {
        $this->full_width_images[] = $image;

        return $this;
    }
}