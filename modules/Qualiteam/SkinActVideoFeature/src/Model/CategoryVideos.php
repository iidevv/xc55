<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 *
 * @ORM\Entity
 * @ORM\Table (name="video_category_videos",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint (name="pair", columns={"category_id","video_id"})
 *      },
 *      indexes={
 *          @ORM\Index (name="orderby", columns={"orderby"}),
 *          @ORM\Index (name="orderbyInVideo", columns={"orderbyInVideo"})
 *      }
 * )
 */
class CategoryVideos extends \XLite\Model\AEntity
{
    /**
     * Primary key
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Video position in the category
     *
     * @var integer
     *
     * @ORM\Column (type="integer", length=11, nullable=false)
     */
    protected $orderby = 0;

    /**
     * Category position in the video
     *
     * @var integer
     *
     * @ORM\Column (type="integer", length=11, nullable=false)
     */
    protected $orderbyInVideo = 0;

    /**
     * Relation to a category entity
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToOne  (targetEntity="Qualiteam\SkinActVideoFeature\Model\VideoCategory", inversedBy="categoryVideos")
     * @ORM\JoinColumn (name="category_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $category;

    /**
     * Relation to a video entity
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToOne  (targetEntity="Qualiteam\SkinActVideoFeature\Model\EducationalVideo", inversedBy="categoryVideos")
     * @ORM\JoinColumn (name="video_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $video;

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
     * Set orderby
     *
     * @param integer $orderby
     * @return CategoryVideos
     */
    public function setOrderby($orderby)
    {
        $this->orderby = $orderby;
        return $this;
    }

    /**
     * Get orderby
     *
     * @return integer
     */
    public function getOrderby()
    {
        return $this->orderby;
    }

    /**
     * Set category
     *
     * @param \Qualiteam\SkinActVideoFeature\Model\VideoCategory $category
     * @return CategoryVideos
     */
    public function setCategory(\Qualiteam\SkinActVideoFeature\Model\VideoCategory $category = null)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Get category
     *
     * @return \Qualiteam\SkinActVideoFeature\Model\VideoCategory
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set video
     *
     * @param \Qualiteam\SkinActVideoFeature\Model\EducationalVideo $video
     * @return CategoryVideos
     */
    public function setVideo(\Qualiteam\SkinActVideoFeature\Model\EducationalVideo $video = null)
    {
        $this->video = $video;
        return $this;
    }

    /**
     * Get video
     *
     * @return \Qualiteam\SkinActVideoFeature\Model\EducationalVideo
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * @return int
     */
    public function getOrderbyInVideo()
    {
        return $this->orderbyInVideo;
    }

    /**
     * @param int $orderbyInVideo
     */
    public function setOrderbyInVideo($orderbyInVideo)
    {
        $this->orderbyInVideo = $orderbyInVideo;
    }
}