<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Banner\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Banner
 *
 * @ORM\Entity (repositoryClass="\QSL\Banner\Model\Repo\Banner")
 * @ORM\Table (name="banners",
 *    indexes={
 *      @ORM\Index (name="ep", columns={"enabled","position"}),
 *      @ORM\Index (name="effect", columns={"effect"})
 *  }
 * )
 *
 */
class Banner extends \XLite\Model\AEntity
{
    /**
    * banner + category link unique id
    *
    * @var   integer
    *
    * @ORM\Id
    * @ORM\GeneratedValue (strategy="AUTO")
    * @ORM\Column         (type="integer", options={"unsigned": true})
    */
    protected $id;

    /**
    * Title
    *
    * @var   string
    *
    * @ORM\Column (type="string", length=255)
    */
    protected $title;

    /**
    * Title
    *
    * @var   string
    *
    * @ORM\Column (type="string", length=255)
    */
    protected $location;


    /**
    * Categories
    *
    * @var   \Doctrine\Common\Collections\ArrayCollection
    *
    * @ORM\ManyToMany (targetEntity="XLite\Model\Category", inversedBy="banners")
    * @ORM\JoinTable (name="banner_category",
    *      joinColumns={@ORM\JoinColumn(name="banner_id", referencedColumnName="id")},
    *      inverseJoinColumns={@ORM\JoinColumn(name="category_id", referencedColumnName="category_id")}
    * )
    */
    protected $categories;


    /**
    * Banner slides
    *
    * @var   \Doctrine\Common\Collections\Collection
    *
    * @ORM\OneToMany (targetEntity="QSL\Banner\Model\BannerSlide", mappedBy="banner", cascade={"all"})
    * @ORM\OrderBy   ({"position" = "ASC"})
    */
    protected $bannerSlide;

    /**
    * Banner contents
    *
    * @var   \Doctrine\Common\Collections\Collection
    *
    * @ORM\OneToMany (targetEntity="QSL\Banner\Model\Content", mappedBy="banner", cascade={"all"})
    * @ORM\OrderBy   ({"position" = "ASC"})
    */
    protected $contents;


    /**
     * Memberships
     *
     * @var   \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany (targetEntity="XLite\Model\Membership", inversedBy="banners")
     * @ORM\JoinTable (name="membership_banner",
     *      joinColumns={@ORM\JoinColumn (name="banner_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn (name="membership_id", referencedColumnName="membership_id", onDelete="CASCADE")}
     * )
     */
    protected $memberships;

    /**
    * Banner width
    *
    * @var   integer
    *
    * @ORM\Column (type="integer")
    */
    protected $width = 0;

    /**
    * Banner height
    *
    * @var   integer
    *
    * @ORM\Column (type="integer")
    */
    protected $height = 0;

    /**
    * Sort position
    *
    * @var   integer
    *
    * @ORM\Column (type="integer")
    */
    protected $position = 0;

    /**
    * Enabled
    *
    * @var   boolean
    *
    * @ORM\Column (type="boolean")
    */
    protected $enabled = true;

    /**
     * Enabled
     *
     * @var   boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $parallax = false;

    /**
    * Roation effect
    *
    * @var  integer
    *
    * @ORM\Column (type="string", length=255)
    */
    protected $effect;

    /**
    * Banner slide delay
    *
    * @var   integer
    *
    * @ORM\Column (type="integer")
    */
    protected $timeout = 4;


    /**
    * Banner animation speed
    *
    * @var   integer
    *
    * @ORM\Column (type="integer")
    */
    protected $delay = 3;

    /**
    * Show on home page
    *
    * @var   boolean
    *
    * @ORM\Column (type="boolean")
    */
    protected $home_page = true;

    /**
     * Show on product pages
     *
     * @var   boolean
     *
     * @ORM\Column (type="boolean", options={"default":true})
     */
    protected $products_pages = true;

    /**
    * Show navigation
    *
    * @var   boolean
    *
    * @ORM\Column (type="boolean")
    */
    protected $navigation = false;

    /**
    * Show arrows
    *
    * @var   boolean
    *
    * @ORM\Column (type="boolean")
    */
    protected $arrows = false;


    /**
     * Banner constructor.
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->categories  = new \Doctrine\Common\Collections\ArrayCollection();
        $this->memberships = new \Doctrine\Common\Collections\ArrayCollection();
        $this->bannerSlide = new \Doctrine\Common\Collections\ArrayCollection();
        $this->contents    = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
    * Get escaped title
    *
    * @return string
    */
    public function getTitleEscaped()
    {
        return htmlspecialchars($this->getTitle(), ENT_QUOTES);
    }


    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection
     */
    public function getBannerSlide()
    {
        return $this->bannerSlide;
    }


    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * Add categories
     *
     * @param \XLite\Model\Category $categories
     * @return Banner
     */
    public function addCategories(\XLite\Model\Category $categories)
    {
        //$this->categories[] = $categories;
        $this->getCategories()->add($categories);
        return $this;
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Clear categories
     */
    public function clearCategories()
    {
        foreach ($this->getCategories()->getKeys() as $key) {
            $this->getCategories()->remove($key);
        }
    }

    /**
     * Add memberships
     *
     * @param \XLite\Model\Membership $memberships
     * @return Banner
     */
    public function addMemberships(\XLite\Model\Membership $memberships)
    {
        $this->memberships[] = $memberships;
        return $this;
    }

    /**
     * Get memberships
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMemberships()
    {
        return $this->memberships;
    }

    /**
     * Clear memberships
     */
    public function clearMemberships()
    {
        foreach ($this->getMemberships()->getKeys() as $key) {
            $this->getMemberships()->remove($key);
        }
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Banner
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set navigation
     *
     * @param boolean $navigation
     * @return Banner
     */
    public function setNavigation($navigation)
    {
        $this->navigation = $navigation;
        return $this;
    }

    /**
     * Get navigation
     *
     * @return boolean
     */
    public function getNavigation()
    {
        return $this->navigation;
    }

    /**
     * Set arrows
     *
     * @param boolean $arrows
     * @return Banner
     */
    public function setArrows($arrows)
    {
        $this->arrows = $arrows;
        return $this;
    }

    /**
     * Get navigation
     *
     * @return boolean
     */
    public function getArrows()
    {
        return $this->arrows;
    }


    /**
     * Set enabled
     *
     * @param boolean $parallax
     * @return Banner
     */
    public function setParallax($parallax)
    {
        $this->parallax = $parallax;
        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getParallax()
    {
        return $this->parallax;
    }


    /**
     * @param $position
     * @return $this
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * Get position
     *
     * @return boolean
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param $timeout
     * @return $this
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * Get timeout
     *
     * @return boolean
     */
    public function getTimeout()
    {
        return $this->timeout;
    }


    /**
     * @param $delay
     * @return $this
     */
    public function setDelay($delay)
    {
        $this->delay = $delay;
        return $this;
    }

    /**
     * Get delay
     *
     * @return boolean
     */
    public function getDelay()
    {
        return $this->delay;
    }


    /**
     * @param $height
     * @return $this
     */
    public function setHeight($height)
    {
        $this->height = $height;
        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getHeight()
    {
        return $this->height;
    }


    /**
     * @param $width
     * @return $this
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getWidth()
    {
        return $this->width;
    }


    /**
     * @param $location
     * @return $this
     */
    public function setLocation($location)
    {
        $this->location = $location;
        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param $products_pages
     * @return $this
     */
    public function setProductsPages($products_pages)
    {
        $this->products_pages = $products_pages;
        return $this;
    }

    /**
     * @return bool
     */
    public function getProductsPages()
    {
        return $this->products_pages;
    }

    /**
     * Set enabled
     *
     * @return integer
     */
    public function setEffect($effect)
    {
        $this->effect = $effect;
        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEffect()
    {
        return $this->effect;
    }



    /**
     * Add images
     *
     * @param \QSL\Banner\Model\BannerSlide $bannerSlide
     * @return BannerSlide
     */
    public function addImages(\QSL\Banner\Model\BannerSlide $bannerSlide)
    {
        $this->bannerSlide[] = $bannerSlide;
        return $this;
    }


    /**
     * Add images
     *
     * @param \QSL\Banner\Model\Content $contents
     * @return Content
     */
    public function addContents(\QSL\Banner\Model\Content $contents)
    {
        $this->contents[] = $contents;
        return $this;
    }
}
