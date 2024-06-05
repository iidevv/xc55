<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductStickers\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Extender\Mixin
 */
class Category extends \XLite\Model\Category
{
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany (targetEntity="QSL\ProductStickers\Model\ProductSticker", inversedBy="categories")
     * @ORM\JoinTable (name="category_stickers_links",
     *      joinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="category_id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="sticker_id", referencedColumnName="sticker_id", onDelete="CASCADE")}
     * )
     */
    protected $category_stickers;

    /**
     * @var boolean
     *
     * @ORM\Column (type="boolean", nullable=true, options={"default" : false})
     */
    protected $is_stickers_included_subcategories = false;

    /**
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->category_stickers = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategoryStickers()
    {
        return $this->category_stickers;
    }

    /**
     * @param array $category_stickers
     * @param bool  $is_objects
     */
    public function setCategoryStickers($category_stickers = [], $is_objects = false)
    {
        $this->category_stickers = $is_objects
            ? $category_stickers
            : array_filter($this->getAllStickers(), static function ($v) use ($category_stickers) {
                return in_array($v->getProductStickerId(), $category_stickers);
            });
    }

    /**
     * @return mixed
     */
    protected function getAllStickers()
    {
        return \XLite\Core\Database::getRepo('\QSL\ProductStickers\Model\ProductSticker')
            ->findAllProductStickers();
    }

    /**
     * @param boolean $is_stickers_included_subcategories
     */
    public function setIsStickersIncludedSubcategories($is_stickers_included_subcategories)
    {
        $this->is_stickers_included_subcategories = (bool) $is_stickers_included_subcategories;
    }

    /**
     * @return boolean
     */
    public function isStickersIncludedSubcategories()
    {
        return $this->is_stickers_included_subcategories;
    }
}
