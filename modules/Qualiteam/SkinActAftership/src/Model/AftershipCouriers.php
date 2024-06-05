<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * This entity is collection all couriers "aftership" service
 *
 * @ORM\Entity
 * @ORM\Table(name="aftership_couriers", indexes={
 *     @ORM\Index(name="aftership_couriers_slug_idx", columns={"slug"})
 * })
 */
class AftershipCouriers extends \XLite\Model\AEntity
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
     * Courier slug
     *
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    protected $slug;

    /**
     * Courier name
     *
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    protected $name;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}