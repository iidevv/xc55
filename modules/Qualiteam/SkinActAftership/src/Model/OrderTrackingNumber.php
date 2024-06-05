<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Model;

use Doctrine\ORM\Mapping as ORM;
use Qualiteam\SkinActAftership\Core\Message\AftershipErrorWorkToDo;
use Qualiteam\SkinActAftership\Core\Message\AftershipErrorWorkToDoDataContainer;
use XCart\Container;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;

/**
 * @Extender\Mixin
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
class OrderTrackingNumber extends \XLite\Model\OrderTrackingNumber
{
    /**
     * @var string
     *
     * @ORM\Column(name="aftership_courier_name", type="string", nullable=true)
     */
    protected $aftership_courier_name;

    /**
     * @var bool
     *
     * @ORM\Column(name="aftership_sync", type="boolean", options={"default" : false}, nullable=true)
     */
    protected $aftership_sync;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_aftership_slug_error", type="boolean", options={"default" : false}, nullable=true)
     */
    protected $isAftershipSlugError;

    /**
     * Get aftership sync
     *
     * @return bool|null
     */
    public function getAftershipSync(): ?bool
    {
        return $this->aftership_sync;
    }

    /**
     * Set aftership sync
     *
     * @param bool $aftershipSync
     */
    public function setAftershipSync(?bool $aftershipSync): void
    {
        $this->aftership_sync = $aftershipSync;
    }

    /**
     * Get aftership slug error
     *
     * @param bool|null $isAftershipSlugError
     *
     * @return void
     */
    public function setShipstationSlugError(?bool $isAftershipSlugError): void
    {
        $this->isAftershipSlugError = $isAftershipSlugError;
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdateAftershipError(): void
    {
        if ($this->getAftershipCourierName()
            && $this->isAftershipSlugError()
        ) {
            $bus = Container::getContainer()->get('messenger.default_bus');
            Database::getEM()->addAfterFlushCallback(function () use ($bus) {
                $bus->dispatch(new AftershipErrorWorkToDo(
                        new AftershipErrorWorkToDoDataContainer($this->getTrackingId())
                    )
                );
            });
        }
    }

    /**
     * Get aftership courier name
     *
     * @return string
     */
    public function getAftershipCourierName(): string
    {
        return $this->aftership_courier_name ?? '';
    }

    /**
     * Set aftership courier name
     *
     * @param string|null $aftershipCourierName
     */
    public function setAftershipCourierName(?string $aftershipCourierName): void
    {
        $this->aftership_courier_name = $aftershipCourierName;
    }

    /**
     * Set shipstation slug error
     *
     * @return bool|null
     */
    public function isAftershipSlugError(): ?bool
    {
        return $this->isAftershipSlugError;
    }
}
