<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Model;

use XLite\Model\AEntity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table (name="shipstation_code_mapping",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint (name="pair", columns={"shipstation_slug","aftership_slug"})
 *      },
 *      indexes={
 *          @ORM\Index (name="shipstation_slug", columns={"shipstation_slug"}),
 *          @ORM\Index (name="aftership_slug", columns={"aftership_slug"})
 *      }
 * )
 * @ORM\HasLifecycleCallbacks
 */
class ShipstationCodeMapping extends AEntity
{
    /**
     * Node unique ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Shipstation slug
     *
     * @ORM\Column (type="string", length="100")
     */
    protected $shipstation_slug;

    /**
     * Aftership slug
     *
     * @ORM\Column (type="string", length="100")
     */
    protected $aftership_slug;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getShipstationSlug(): ?string
    {
        return $this->shipstation_slug;
    }

    /**
     * @param string $shipstation_slug
     */
    public function setShipstationSlug(string $shipstation_slug): void
    {
        $this->shipstation_slug = $shipstation_slug;
    }

    /**
     * @return string|null
     */
    public function getAftershipSlug(): ?string
    {
        return $this->aftership_slug;
    }

    /**
     * @param string $aftership_slug
     */
    public function setAftershipSlug(string $aftership_slug): void
    {
        $this->aftership_slug = $aftership_slug;
    }
}