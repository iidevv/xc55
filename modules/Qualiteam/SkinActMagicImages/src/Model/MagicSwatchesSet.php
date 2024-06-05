<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMagicImages\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Database;
use XLite\Model\AttributeValue\AttributeValueSelect;
use XLite\Model\Base\I18n;
use XLite\Model\Product;

/**
 * Class magic swatches
 *
 * @ORM\Entity
 * @ORM\Table (name="magic_swatches_set")
 */
class MagicSwatchesSet extends I18n
{
    use ExecuteCachedTrait;

    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column(type="integer", options={"unsigneg": true})
     */
    protected $id;

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="XLite\Model\Product", inversedBy="magicSwatchesSet")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * Relation to a swatch entity
     *
     * @var AttributeValueSelect
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\AttributeValue\AttributeValueSelect", inversedBy="magicSwatchesSet")
     * @ORM\JoinColumn (name="attribute_value_id", nullable=true, referencedColumnName="id", onDelete="CASCADE")
     */
    protected $attributeValue;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Qualiteam\SkinActMagicImages\Model\Image", mappedBy="magicSwatchesSet",
     *                                                                         cascade={"all"})
     * @ORM\OrderBy({"orderby" = "ASC"})
     */
    protected $images;

    /**
     * Columns
     *
     * @var integer
     *
     * @ORM\Column         (type="smallint", options={ "default" : 0 }, nullable=true)
     */
    protected $spinColumns;

    /**
     * Has product spin
     *
     * @var boolean
     *
     */
    protected $hasSpin = null;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="Qualiteam\SkinActMagicImages\Model\MagicSwatchesSetTranslation", mappedBy="owner",
     *                cascade={"all"})
     */
    protected $translations;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->images = new ArrayCollection();

        parent::__construct($data);
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    public function getAttributeValue()
    {
        $set = $this;

        return $this->executeCachedRuntime(static function () use ($set) {
            return Database::getRepo(AttributeValueSelect::class)->findOneBy(['id' => $set->attributeValue]);
        }, [
            __CLASS__,
            __METHOD__,
            (bool) $set,
        ]);
    }

    public function setAttributeValue($attributeValue): void
    {
        $this->attributeValue = $attributeValue;
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Add spin images
     *
     * @param \Qualiteam\SkinActMagicImages\Model\Image $image
     *
     * @return \Qualiteam\SkinActMagicImages\Model\MagicSwatchesSet
     */
    public function addImages(Image $image)
    {
        $this->images[] = $image;

        return $this;
    }

    /**
     * Get spin images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Get spin images count
     *
     * @return integer
     */
    public function getImagesCount(): int
    {
        return $this->images->count();
    }

    /**
     * Check if product has spin
     *
     * @return boolean
     */
    public function hasSpin(): bool
    {
        if ($this->hasSpin === null) {
            $this->hasSpin = ($this->images->count() > 0);
        }

        return $this->hasSpin;
    }

    /**
     * Get spin columns
     *
     * @return int|null
     */
    public function getSpinColumns(): ?int
    {
        return $this->spinColumns;
    }

    /**
     * Set spin columns
     *
     * @param int|null $columns
     *
     * @return void
     */
    public function setSpinColumns(?int $columns): void
    {
        $this->spinColumns = $columns;
    }

    public function cloneEntity()
    {
        $newMagicSwatchesSet = parent::cloneEntity();

        /** @var \Qualiteam\SkinActMagicImages\Model\Image $image */
        foreach ($this->getImages() as $image) {
            $newImage = $image->cloneEntity();
            $newImage->setMagicSwatchesSet($newMagicSwatchesSet);
            $newMagicSwatchesSet->addImages($newImage);
        }

        return $newMagicSwatchesSet;
    }
}
