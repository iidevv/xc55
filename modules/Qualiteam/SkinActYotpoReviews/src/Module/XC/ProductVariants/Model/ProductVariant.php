<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Module\XC\ProductVariants\Model;

use Doctrine\ORM\Mapping as ORM;
use Qualiteam\SkinActYotpoReviews\Model\ProductVariant\Update;
use Symfony\Component\Messenger\MessageBusInterface;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\MappedSuperclass
 */
class ProductVariant extends \XC\ProductVariants\Model\ProductVariant
{
    protected ?MessageBusInterface $bus;

    /**
     * Yotpo product id
     *
     * @var int
     *
     * @ORM\Column (type="bigint", nullable=true, options={"unsigned": true })
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

    public function cloneEntity()
    {
        $newVariant = parent::cloneEntity();

        $newVariant->setYotpoId(null);

        return $newVariant;
    }

    /**
     * @ORM\PreUpdate
     *
     * @return void
     */
    public function prepareBeforeUpdateVariant()
    {
        $update = new Update($this);
        $update->execute();
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
}