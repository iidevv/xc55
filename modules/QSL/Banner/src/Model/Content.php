<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Banner\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Banner image
 *
 * @ORM\Entity
 * @ORM\Table  (name="banner_contents")
 */
class Content extends \XLite\Model\Base\I18n
{
    public const CONTENT_TYPE  = 'C';

    /**
     *  Unique id
     *
     * @var   integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={"unsigned": true})
     */
    protected $content_id;

    /**
     * Position
     *
     * @var   integer
     *
     * @ORM\Column (type="integer")
     */
    protected $position = 5;

    /**
     * Relation to a product entity
     *
     * @var  \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToOne  (targetEntity="QSL\Banner\Model\Banner", inversedBy="contents")
     * @ORM\JoinColumn (name="banner_id", referencedColumnName="id")
     */
    protected $banner;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="QSL\Banner\Model\ContentTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

     /**
     * Get event cell base information
     *
     * @return string
     */
    public function getEventCell()
    {
        return self::CONTENT_TYPE;
    }

    /**
     * Set image
     *
     * @param \QSL\Banner\Model\Banner $banner
     * @return Content
     */
    public function setBanner(\QSL\Banner\Model\Banner $banner)
    {
        $this->banner = $banner;
        return $this;
    }

    /**
     * Get image
     *
     * @return \QSL\Banner\Model\Banner
     */
    public function getBanner()
    {
        return $this->banner;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return Content
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Returns the slide HTML content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->getTranslation(\XLite\Core\Translation::DEFAULT_LANGUAGE)->getContent();
    }
}
