<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Model\EntityVersion;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entities implementing EntityTypeInterface will have their entity version field changed automatically on every update.
 * Use EntityVersionTrait to add actual implementation.
 */
trait EntityVersionTrait
{
    /**
     * Entity version
     *
     * @var string
     *
     * @ORM\Column (type="guid", nullable=true)
     */
    protected $entityVersion;

    public function getEntityVersion()
    {
        return $this->entityVersion;
    }

    public function setEntityVersion($uuid)
    {
        $this->entityVersion = $uuid;
    }
}
