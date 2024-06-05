<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Banner\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity (repositoryClass="\QSL\Banner\Model\Repo\BannerSlide")
 * @ORM\Table (name="banner_slide",
 *    indexes={
 *      @ORM\Index (name="ep", columns={"enabled","position"}),
 *  }
 * )
 *
 */
class BannerSlide extends \XLite\Model\Base\I18n
{
    public const CONTENT_TYPE  = 'I';

    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={"unsigned": true})
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column (type="string")
     */
    protected $link = '';

    /**
     * @var string
     *
     * @ORM\Column (type="string")
     */
    protected $maintext_color = '333333';

    /**
     * @var string
     *
     * @ORM\Column (type="string")
     */
    protected $addtext_color = '888888';

    /**
     * @var \QSL\Banner\Model\Banner
     *
     * @ORM\ManyToOne  (targetEntity="QSL\Banner\Model\Banner", inversedBy="bannerSlide", cascade={"persist","merge","detach"})
     * @ORM\JoinColumn (name="banner_id", referencedColumnName="id", onDelete="CASCADE")
    */
    protected $banner;

    /**
     * @var \QSL\Banner\Model\Image\Banner\Image
     *
     * @ORM\OneToOne (targetEntity="QSL\Banner\Model\Image\Banner\Image", mappedBy="bannerSlide", cascade={"all"})
     * @ORM\OrderBy ({"position" = "ASC"})
     */
    protected $image;

    /**
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $position = 0;

    /**
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $enabled = true;

    /**
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $parallaxImage = false;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="QSL\Banner\Model\BannerSlideTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * @return boolean
     */
    public function hasImage()
    {
        return !is_null($this->getImage()) && $this->getImage()->isPersistent();
    }

    public function getCode()
    {
        return $this->getId();
    }

    /**
     * @return string|null
     */
    public function getImageURL()
    {
        return $this->getImage() ? $this->getImage()->getURL() : null;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param boolean $parallaxImage
     */
    public function setParallaxImage($parallaxImage)
    {
        $this->parallaxImage = $parallaxImage;
    }

    /**
     * @return boolean
     */
    public function getParallaxImage()
    {
        return $this->parallaxImage;
    }

    /**
     * @param integer $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return boolean
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param string $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @return string
     */
    public function getMainTextColor()
    {
        return $this->maintext_color;
    }

    /**
     * @param string $maintext_color
     */
    public function setMainTextColor($maintext_color)
    {
        $this->maintext_color = $maintext_color;
    }

    /**
     * @return string
     */
    public function getAddTextColor()
    {
        return $this->addtext_color;
    }

    /**
     * @param string $addtext_color
     */
    public function setAddTextColor($addtext_color)
    {
        $this->addtext_color = $addtext_color;
    }

    /**
     * @param \QSL\Banner\Model\Image\Banner\Image $image
     */
    public function setImage(\QSL\Banner\Model\Image\Banner\Image $image = null)
    {
        $this->image = $image;
    }

    /**
     * @return \QSL\Banner\Model\Image\Banner\Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param \QSL\Banner\Model\Banner $banner
     */
    public function setBanner(\QSL\Banner\Model\Banner $banner)
    {
        $this->banner = $banner;
    }

    /**
     * @return \QSL\Banner\Model\Banner
     */
    public function getBanner()
    {
        return $this->banner;
    }

    /**
     * @return string
     */
    public function getEventCell()
    {
        return self::CONTENT_TYPE;
    }

    /**
     * @return string|null
     */
    public function getActionButtonLink()
    {
        return $this->getLink() ?: \Includes\Utils\URLManager::getCurrentURL() . '#';
    }

    // {{{ Translation Getters / setters

    /**
     * @return string
     */
    public function getMaintext()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $maintext
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setMaintext($maintext)
    {
        return $this->setTranslationField(__FUNCTION__, $maintext);
    }

    /**
     * @return string
     */
    public function getAddtext()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $addtext
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setAddtext($addtext)
    {
        return $this->setTranslationField(__FUNCTION__, $addtext);
    }

    /**
     * @return string
     */
    public function getActionButton()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $actionButton
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setActionButton($actionButton)
    {
        return $this->setTranslationField(__FUNCTION__, $actionButton);
    }

    // }}}
}
