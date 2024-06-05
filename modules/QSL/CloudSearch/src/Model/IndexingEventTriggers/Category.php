<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CloudSearch\Model\IndexingEventTriggers;

use Doctrine\ORM\Mapping as ORM;
use QSL\CloudSearch\Core\IndexingEvent\IndexingEventTriggerInterface;
use XCart\Extender\Mapping\Extender;

/**
 * Category model
 *
 * @ORM\Table (indexes={
 *      @ORM\Index (name="csLastUpdate", columns={"csLastUpdate"})
 *  }
 * )
 *
 * @ORM\MappedSuperclass
 * @Extender\Mixin
 */
class Category extends \XLite\Model\Category implements IndexingEventTriggerInterface
{
    /**
     * Last update timestamp
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $csLastUpdate = 0;

    public function getCloudSearchEntityType()
    {
        return self::INDEXING_EVENT_CATEGORY_ENTITY;
    }

    public function getCloudSearchEntityIds()
    {
        return [$this->getId()];
    }

    public function getCloudSearchEventAction()
    {
        return null;
    }

    /**
     * @return int
     */
    public function getCsLastUpdate()
    {
        return $this->csLastUpdate;
    }

    /**
     * @param int $csLastUpdate
     */
    public function setCsLastUpdate($csLastUpdate)
    {
        $this->csLastUpdate = $csLastUpdate;
    }
}
