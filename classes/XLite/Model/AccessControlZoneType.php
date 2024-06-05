<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table (
 *     name="access_control_zone_types",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint (name="name", columns={"name"})
 *     },
 *     indexes={
 *         @ORM\Index (name="name", columns={"name"})
 *     }
 * )
 */
class AccessControlZoneType extends \XLite\Model\AEntity
{
    public const ZONE_TYPE_ORDER = 'order';

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column (type="integer", options={"unsigned": true})
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column (type="string")
     */
    protected $name;

    /**
     * Return Id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set Id
     *
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Return Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set Name
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Check if zone name match parameter
     *
     * @param string $zone
     *
     * @return mixed
     */
    public function checkIdentity($zone)
    {
        return $this->getName() === $zone;
    }
}
