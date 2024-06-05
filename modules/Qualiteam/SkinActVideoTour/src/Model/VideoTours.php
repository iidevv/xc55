<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoTour\Model;

use Doctrine\ORM\Mapping as ORM;
use Qualiteam\SkinActVideoTour\Helper\Youtube;
use XLite\Core\Database;

/**
 * Class video tour
 * @ORM\Entity
 * @ORM\Table  (name="video_tours")
 */
class VideoTours extends \XLite\Model\Base\I18n
{
    /**
     * ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * @var \XLite\Model\Product
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\Product", inversedBy="video_tours")
     * @ORM\JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * Video url
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $video_url;

    /**
     * Youtube video id
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $youtube_id;

    /**
     * Flag if the field is an enabled one
     *
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $enabled = true;

    /**
     * @var int
     *
     * @ORM\Column (type="integer")
     */
    protected $position = 0;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="Qualiteam\SkinActVideoTour\Model\VideoToursTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param $productId
     *
     * @return void
     */
    public function setProduct($productId): void
    {
        if ($productId) {
            $product = $this->prepareProductValue($productId);

            if ($product) {
                $this->product = $product;
            }
        }
    }

    /**
     * @param int $productId
     *
     * @return object|null
     */
    protected function prepareProductValue(int $productId) {
        return Database::getRepo(\XLite\Model\Product::class)
            ->findOneBy(['product_id' => $productId]);
    }

    /**
     * @return \XLite\Model\Product|null
     */
    public function getProduct(): ?\XLite\Model\Product
    {
        return $this->product;
    }

    /**
     * @return string|null
     */
    public function getVideoUrl(): ?string
    {
        return $this->video_url;
    }

    /**
     * @param string $video_url
     */
    public function setVideoUrl(string $video_url): void
    {
        $this->video_url = $video_url;
    }

    /**
     * @return bool|null
     */
    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @return int|null
     */
    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition(int $position): void
    {
        $this->position = $position;
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
     * @return string|null
     */
    public function getYoutubeId(): ?string
    {
        return $this->youtube_id;
    }

    /**
     * @param string $youtube_id
     */
    public function setYoutubeId(string $youtube_id): void
    {
        $this->youtube_id = $youtube_id;
    }

    /**
     * Get youtube embed video url
     *
     * @return string
     */
    public function getYoutubeEmbedUrl(): string
    {
        return Youtube::getYoutubeEmbedVideoUrl(
            $this->getYoutubeId()
        );
    }
}