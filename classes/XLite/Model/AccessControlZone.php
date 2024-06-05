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
 *     name="access_control_zones",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint (name="cz", columns={"cell_id", "type_id"})
 *     }
 * )
 */
class AccessControlZone extends \XLite\Model\AEntity
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column (type="integer", options={"unsigned": true})
     */
    protected $id;

    /**
     * @var \XLite\Model\AccessControlCell
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\AccessControlCell", inversedBy="access_control_zones")
     * @ORM\JoinColumn (name="cell_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $cell;

    /**
     * @var \XLite\Model\AccessControlZoneType
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\AccessControlZoneType")
     * @ORM\JoinColumn (name="type_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $type;

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
     * Return Cell
     *
     * @return AccessControlCell
     */
    public function getCell()
    {
        return $this->cell;
    }

    /**
     * Set Cell
     *
     * @param AccessControlCell $cell
     *
     * @return $this
     */
    public function setCell($cell)
    {
        $this->cell = $cell;
        return $this;
    }

    /**
     * Return Zone Type
     *
     * @return AccessControlZoneType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set Zone Type
     *
     * @param AccessControlZoneType $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Set Zone by zone name
     *
     * @param string $name
     *
     * @return $this
     * @throws \XLite\Core\Exception\AccessControlZoneTypeNotFoundException
     */
    public function setTypeByName($name)
    {
        if ($zone = \XLite\Core\Database::getRepo('\XLite\Model\AccessControlZoneType')->getZoneByName($name)) {
            $this->type = $zone;
        } else {
            throw new \XLite\Core\Exception\AccessControlZoneTypeNotFoundException("Access control zone for name \"{$name}\" not found.");
        }

        return $this;
    }

    /**
     * Set Zone by zone
     *
     * @param \XLite\Model\AccessControlZone $zone
     *
     * @return $this
     */
    public function setTypeByZone(\XLite\Model\AccessControlZone $zone)
    {
        $this->type = $zone->getType();

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
        return $this->getType()->checkIdentity($zone);
    }
}
