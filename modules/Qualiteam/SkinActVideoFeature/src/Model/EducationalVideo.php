<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\Model;

use Doctrine\ORM\Mapping as ORM;
use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Converter;
use XLite\Core\Model\EntityVersion\EntityVersionTrait;

/**
 * The "video" model class
 *
 * @ORM\Entity
 * @ORM\Table  (name="educational_videos")
 *
 * @ORM\HasLifecycleCallbacks
 */
class EducationalVideo extends \XLite\Model\Base\I18n
{
    use EntityVersionTrait;
    use ExecuteCachedTrait;

    /**
     * Video unique ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Video code
     *
     * @var string
     *
     * @ORM\Column (type="text", nullable=true)
     */
    protected $video_code;

    /**
     * Youtube video id
     *
     * @var string
     *
     * @ORM\Column (type="text", nullable=true)
     */
    protected $youtube_video_id;

    /**
     * Is video available or not
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $enabled = true;

    /**
     * Position parameter
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $pos = 0;

    /**
     * Creation date (UNIX timestamp)
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $date = 0;

    /**
     * Update date (UNIX timestamp)
     *
     * @var integer
     *
     * @ORM\Column (type="integer", options={ "unsigned": true })
     */
    protected $updateDate = 0;

    /**
     * Relation to a CategoryVideos entities
     *
     * @var \Doctrine\ORM\PersistentCollection
     *
     * @ORM\OneToMany (targetEntity="Qualiteam\SkinActVideoFeature\Model\CategoryVideos", mappedBy="video", cascade={"all"})
     * @ORM\OrderBy   ({"orderbyInVideo" = "ASC"})
     */
    protected $categoryVideos;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="Qualiteam\SkinActVideoFeature\Model\EducationalVideoTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->categoryVideos = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Get object unique id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->getVideoId();
    }

    /**
     * Check video visibility
     *
     * @return boolean
     */
    public function isVisible()
    {
        return $this->getEnabled();
    }

    /**
     * Return random video category
     *
     * @param integer|null $categoryId Category ID OPTIONAL
     *
     * @return \Qualiteam\SkinActVideoFeature\Model\VideoCategory
     */
    public function getCategory($categoryId = null)
    {
        $result = $this->getLink($categoryId)->getCategory();

        if (empty($result)) {
            $result = new \Qualiteam\SkinActVideoFeature\Model\VideoCategory();
        }

        return $result;
    }

    /**
     * Return random video category ID
     *
     * @param integer|null $categoryId Category ID OPTIONAL
     *
     * @return integer
     */
    public function getCategoryId($categoryId = null)
    {
        return $this->getCategory($categoryId)->getCategoryId();
    }

    /**
     * Return list of video categories
     *
     * @return array
     */
    public function getCategories()
    {
        $result = [];

        foreach ($this->getCategoryVideos() as $cp) {
            $result[] = $cp->getCategory();
        }

        return $result;
    }

    /**
     * Get video Url
     *
     * @return string
     */
    public function getURL()
    {
        return $this->getVideoId()
            ? \XLite\Core\Converter::makeURLValid(
                \XLite\Core\Converter::buildURL('educational_video', '', ['id' => $this->getVideoId()])
            )
            : null;
    }

    /**
     * Get front URL
     *
     * @return string
     */
    public function getFrontURL($withAttributes = false, $buildCuInAdminZone = false)
    {
        return $this->getVideoId()
            ? \XLite\Core\Converter::makeURLValid(
                \XLite::getInstance()->getShopURL(
                    \XLite\Core\Converter::buildURL(
                        'educational_video',
                        '',
                        $this->getParamsForFrontURL($withAttributes),
                        \XLite::getCustomerScript(),
                        $buildCuInAdminZone
                    )
                )
            )
            : null;
    }

    /**
     * @return array
     */
    protected function getParamsForFrontURL($withAttributes = false)
    {
        $result = [
            'id' => $this->getVideoId(),
        ];

        return $result;
    }

    /**
     * Return video position in category
     *
     * @param integer|null $categoryId Category ID OPTIONAL
     *
     * @return integer|void
     */
    public function getOrderBy($categoryId = null)
    {
        $link = $this->getLink($categoryId);

        return $link ? $link->getOrderBy() : null;
    }

    /**
     * Prepare creation date
     *
     * @return void
     *
     * @ORM\PrePersist
     */
    public function prepareBeforeCreate()
    {
        $time = \XLite\Core\Converter::time();

        if (!$this->getDate()) {
            $this->setDate($time);
        }

        if (!$this->getDate()) {
            $this->setDate(mktime(0, 0, 0, date('m', $time), date('j', $time), date('Y', $time)));
        }

        $this->prepareBeforeUpdate();
    }

    /**
     * Prepare update date
     *
     * @return void
     *
     * @ORM\PreUpdate
     */
    public function prepareBeforeUpdate()
    {
        $this->setUpdateDate(\XLite\Core\Converter::time());
    }

    /**
     * Prepare remove
     *
     * @return void
     *
     * @ORM\PreRemove
     */
    public function prepareBeforeRemove()
    {
        // No default actions. May be used in modules
    }

    /**
     * Return certain Video <--> Category association
     *
     * @param integer|null $categoryId Category ID
     *
     * @return \Qualiteam\SkinActVideoFeature\Model\CategoryVideos|void
     */
    protected function findLinkByCategoryId($categoryId)
    {
        $result = null;

        foreach ($this->getCategoryVideos() as $cp) {
            if ($cp->getCategory() && $cp->getCategory()->getCategoryId() == $categoryId) {
                $result = $cp;
            }
        }

        return $result;
    }

    /**
     * Return certain Video <--> Category association
     *
     * @param integer|null $categoryId Category ID OPTIONAL
     *
     * @return \Qualiteam\SkinActVideoFeature\Model\CategoryVideos
     */
    protected function getLink($categoryId = null)
    {
        $result = empty($categoryId)
            ? $this->getCategoryVideos()->first()
            : $this->findLinkByCategoryId($categoryId);

        if (empty($result)) {
            $result = new \Qualiteam\SkinActVideoFeature\Model\CategoryVideos;
        }

        return $result;
    }

    /**
     * Get video_id
     *
     * @return integer
     */
    public function getVideoId()
    {
        return $this->id;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return \Qualiteam\SkinActVideoFeature\Model\EducationalVideo
     */
    public function setEnabled($enabled)
    {
        $this->enabled = (bool) $enabled;

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
     * Set date
     *
     * @param integer $date
     *
     * @return \Qualiteam\SkinActVideoFeature\Model\EducationalVideo
     */
    public function setDate($date)
    {
        $this->date = min(MAX_TIMESTAMP, max(0, $date));

        return $this;
    }

    /**
     * Get date
     *
     * @return integer
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set updateDate
     *
     * @param integer $updateDate
     *
     * @return \Qualiteam\SkinActVideoFeature\Model\EducationalVideo
     */
    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;

        return $this;
    }

    /**
     * Get updateDate
     *
     * @return integer
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    /**
     * Add categoryVideos
     *
     * @param \Qualiteam\SkinActVideoFeature\Model\CategoryVideos $categoryVideos
     *
     * @return \Qualiteam\SkinActVideoFeature\Model\EducationalVideo
     */
    public function addCategoryVideos(\Qualiteam\SkinActVideoFeature\Model\CategoryVideos $categoryVideos)
    {
        $this->categoryVideos[] = $categoryVideos;

        return $this;
    }

    /**
     * Get categoryVideos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategoryVideos()
    {
        return $this->categoryVideos;
    }

    /**
     * @param \Qualiteam\SkinActVideoFeature\Model\VideoCategory[] $categories
     */
    public function addCategoryVideosLinksByCategories($categories)
    {
        foreach ($categories as $category) {
            if (!$this->hasCategoryVideosLinkByCategory($category)) {
                $categoryVideo = new \Qualiteam\SkinActVideoFeature\Model\CategoryVideos;
                $categoryVideo->setVideo($this);
                $categoryVideo->setCategory($category);

                $this->addCategoryVideos($categoryVideo);
            }
        }
    }

    /**
     * @param \Qualiteam\SkinActVideoFeature\Model\VideoCategory $category
     */
    public function addCategory($category)
    {
        $categoryVideo = new \Qualiteam\SkinActVideoFeature\Model\CategoryVideos;
        $categoryVideo->setVideo($this);
        $categoryVideo->setCategory($category);

        $this->addCategoryVideos($categoryVideo);
    }

    /**
     * @param \Qualiteam\SkinActVideoFeature\Model\VideoCategory[] $categories
     */
    public function removeCategoryVideosLinksByCategories($categories)
    {
        $categoryVideosLinks = [];

        foreach ($categories as $category) {
            $categoryVideosLink = $this->findCategoryVideosLinkByCategory($category);
            if ($categoryVideosLink) {
                $categoryVideosLinks[] = $categoryVideosLink;
            }
        }

        if ($categoryVideosLinks) {
            \XLite\Core\Database::getRepo('\Qualiteam\SkinActVideoFeature\Model\CategoryVideos')->deleteInBatch(
                $categoryVideosLinks
            );
        }
    }

    /**
     * @param \Qualiteam\SkinActVideoFeature\Model\VideoCategory[] $categories
     */
    public function replaceCategoryVideosLinksByCategories($categories)
    {
        $categoriesToAdd = [];
        foreach ($categories as $category) {
            if (!$this->hasCategoryVideosLinkByCategory($category)) {
                $categoriesToAdd[] = $category;
            }
        }

        $categoriesIds = array_map(static function ($item) {
            /** @var \Qualiteam\SkinActVideoFeature\Model\VideoCategory $item */
            return (int) $item->getCategoryId();
        }, $categories);

        $categoryVideosLinksToDelete = [];
        foreach ($this->getCategoryVideos() as $categoryVideo) {
            if (!in_array((int) $categoryVideo->getCategory()->getCategoryId(), $categoriesIds, true)) {
                $categoryVideosLinksToDelete[] = $categoryVideo;
            }
        }

        if ($categoryVideosLinksToDelete) {
            \XLite\Core\Database::getRepo('\Qualiteam\SkinActVideoFeature\Model\CategoryVideos')->deleteInBatch(
                $categoryVideosLinksToDelete
            );
        }

        if ($categoriesToAdd) {
            $this->addCategoryVideosLinksByCategories($categoriesToAdd);
        }
    }

    /**
     * @param \Qualiteam\SkinActVideoFeature\Model\VideoCategory $category
     *
     * @return bool
     */
    public function hasCategoryVideosLinkByCategory($category)
    {
        return (bool) $this->findCategoryVideosLinkByCategory($category);
    }

    /**
     * @param \Qualiteam\SkinActVideoFeature\Model\VideoCategory $category
     *
     * @return \Qualiteam\SkinActVideoFeature\Model\CategoryVideos
     */
    public function findCategoryVideosLinkByCategory($category)
    {
        /** @var \Qualiteam\SkinActVideoFeature\Model\CategoryVideos $categoryVideo */
        foreach ($this->getCategoryVideos() as $categoryVideo) {
            if ((int) $categoryVideo->getCategory()->getCategoryId() === (int) $category->getCategoryId()) {
                return $categoryVideo;
            }
        }

        return null;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $description
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setDescription($description)
    {
        return $this->setTranslationField(__FUNCTION__, $description);
    }

    /**
     * @return string
     */
    public function getVideoCode(): ?string
    {
        return $this->video_code;
    }

    /**
     * @param string|null $video_code
     */
    public function setVideoCode(?string $video_code): void
    {
        $this->video_code = $video_code;
    }

    /**
     * @return string
     */
    public function getYoutubeVideoId(): ?string
    {
        return $this->youtube_video_id;
    }

    /**
     * @param string|null $video_code
     */
    public function setYoutubeVideoId(?string $youtube_video_id): void
    {
        $this->youtube_video_id = $youtube_video_id;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->getPos();
    }

    /**
     * Set position
     *
     * @param integer $position Video position
     *
     * @return self
     */
    public function setPosition($position)
    {
        return $this->setPos($position);
    }

    /**
     * Set pos
     *
     * @param integer $pos
     *
     * @return EducationalVideo
     */
    public function setPos($pos)
    {
        $this->pos = $pos;
        return $this;
    }
}