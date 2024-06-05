<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductTags\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Product
{
    /**
     * Tags
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany (targetEntity="XC\ProductTags\Model\Tag", inversedBy="products")
     * @ORM\JoinTable (name="product_tags",
     *      joinColumns={@ORM\JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn (name="tag_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     * @ORM\OrderBy({"position" = "ASC"})
     */
    protected $tags;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Clone
     *
     * @return \XLite\Model\AEntity
     */
    public function cloneEntity()
    {
        /** @var static $product */
        $product = parent::cloneEntity();

        /** @var Tag $tag */
        foreach ($this->getTags() as $tag) {
            $tag->addProducts($product);
            $product->addTags($tag);
        }

        return $product;
    }

    /**
     * @deprecated 5.4
     * @see addTag
     *
     * @param \XC\ProductTags\Model\Tag $tags
     *
     * @return Product
     */
    public function addTags(\XC\ProductTags\Model\Tag $tags)
    {
        return $this->addTag($tags);
    }

    /**
     * Add tag
     *
     * @param Tag $tag
     *
     * @return Product
     */
    public function addTag(Tag $tag)
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param Tag[] $tags
     */
    public function addTagsByTags($tags)
    {
        foreach ($tags as $tag) {
            if (!$tag->isPersistent() || !$this->hasTagByTag($tag)) {
                $this->addTags($tag);
            }
        }
    }

    /**
     * @param Tag[] $tags
     */
    public function removeTagsByTags($tags)
    {
        foreach ($tags as $tag) {
            if ($this->hasTagByTag($tag)) {
                $this->getTags()->removeElement($tag);
            }
        }
    }

    public function replaceTagsByTags($tags)
    {
        $ids = array_map(static function ($item) {
            /** @var Tag $item */
            return (int) $item->getId();
        }, $tags);

        $toRemove = [];
        foreach ($this->getTags() as $tag) {
            if (!in_array((int) $tag->getId(), $ids, true)) {
                $toRemove[] = $tag;
            }
        }

        $this->addTagsByTags($tags);
        $this->removeTagsByTags($toRemove);
    }

    /**
     * @param Tag $tag
     *
     * @return boolean
     */
    public function hasTagByTag($tag)
    {
        return (bool) $this->getTagByTag($tag);
    }

    /**
     * @param Tag $tag
     *
     * @return mixed|null
     */
    public function getTagByTag($tag)
    {
        foreach ($this->getTags() as $tagObject) {
            if ((int) $tag->getId() === (int) $tagObject->getId()) {
                return $tagObject;
            }
        }

        return null;
    }
}
