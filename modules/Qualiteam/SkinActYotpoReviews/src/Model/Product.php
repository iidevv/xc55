<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;
use Qualiteam\SkinActYotpoReviews\Core\Strategy\UpdateProduct\UpdateProduct;
use XLite\Core\Converter;

/**
 * @Extender\Mixin
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\MappedSuperclass
 */
class Product extends \XLite\Model\Product
{
    /**
     * Yotpo product id
     *
     * @var int
     *
     * @ORM\Column (type="bigint", nullable=true, options={ "unsigned": true })
     */
    protected $yotpo_id;

    /**
     * Is yotpo sync
     *
     * @var bool
     *
     * @ORM\Column (type="boolean", nullable=true, options={ "default": false })
     */
    protected $isYotpoSync = false;

    /**
     * Yotpo average rating
     *
     * @var float
     *
     * @ORM\Column (type="float", nullable=true, options={ "default": 0.00 })
     */
    protected $average_rating;

    /**
     * Yotpo votes count
     *
     * @var int
     *
     * @ORM\Column (type="integer", nullable=true, options={ "default": 0 })
     */
    protected $votes_count;

    public function cloneEntity()
    {
        $newProduct = parent::cloneEntity();

        $newProduct->setYotpoId(null);
        $newProduct->setAverageRating(null);
        $newProduct->setVotesCount(null);

        return $newProduct;
    }

    /**
     * @ORM\PreUpdate
     *
     * @return void
     */
    public function prepareBeforeUpdate()
    {
        parent::prepareBeforeUpdate();

        $strategy = new UpdateProduct($this);
        $strategy->execute();
    }

    /**
     * @return int|null
     */
    public function getYotpoId(): ?int
    {
        return $this->yotpo_id;
    }

    /**
     * @param ?int $yotpoId
     *
     * @return void
     */
    public function setYotpoId(?int $yotpoId): void
    {
        $this->yotpo_id = $yotpoId;
    }

    /**
     * @return bool|null
     */
    public function isYotpoSync(): ?bool
    {
        return $this->isYotpoSync;
    }

    /**
     * @param bool|null $isYotpoSync
     *
     * @return void
     */
    public function setIsYotpoSync(?bool $isYotpoSync): void
    {
        $this->isYotpoSync = $isYotpoSync;
    }

    /**
     * @return string
     */
    public function getReviewListUrl(): string
    {
        return Converter::buildFullURL(
            'custom_reviews',
            '',
            [
                'product_id' => $this->getProductId(),
            ],
            \XLite::getCustomerScript()
        );
    }

    /**
     * @return ?string
     */
    public function getAverageRating(): ?string
    {
        return $this->average_rating;
    }

    /**
     * @param string|null $value
     *
     * @return void
     */
    public function setAverageRating(?string $value): void
    {
        $this->average_rating = $value;
    }

    /**
     * @return ?string
     */
    public function getVotesCount(): ?string
    {
        return $this->votes_count;
    }

    /**
     * @param string|null $value
     *
     * @return void
     */
    public function setVotesCount(?string $value): void
    {
        $this->votes_count = $value;
    }

    public function getReviewsCount()
    {
        return $this->votes_count;
    }
}