<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Region is a model for grouping states
 *
 * @ORM\Entity
 * @ORM\Table (name="regions",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint (name="code", columns={"code","country_code"})
 *      },
 *      indexes={
 *          @ORM\Index (name="name", columns={"name"})
 *      }
 * )
 * @ORM\HasLifecycleCallbacks
 */
class Region extends \XLite\Model\AEntity
{
    /**
     * Region code
     *
     * @var string
     *
     * @ORM\Id
     * @ORM\Column (type="string", length=10)
     */
    protected $code;

    /**
     * Region name
     *
     * @var string
     *
     * @ORM\Column (type="string", length=64)
     */
    protected $name;

    /**
     * Region weight
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $weight;


    /**
     * Country (relation)
     *
     * @var \XLite\Model\Country
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\Country", inversedBy="regions", cascade={"merge","detach"})
     * @ORM\JoinColumn (name="country_code", referencedColumnName="code", onDelete="CASCADE")
     */
    protected $country;

    /**
     * States (relation)
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\State", mappedBy="region", cascade={"all"})
     * @ORM\OrderBy   ({"state" = "ASC"})
     */
    protected $states;

    /**
     * Set code
     *
     * @param string $code
     * @return Region
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Region
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set weight
     *
     * @param integer $weight
     * @return Region
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
        return $this;
    }

    /**
     * Get weight
     *
     * @return integer
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set country
     *
     * @param \XLite\Model\Country $country
     * @return Region
     */
    public function setCountry(\XLite\Model\Country $country = null)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * Get country
     *
     * @return \XLite\Model\Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Add states
     *
     * @param \XLite\Model\State $states
     * @return Region
     */
    public function addStates(\XLite\Model\State $states)
    {
        $this->states[] = $states;
        return $this;
    }

    /**
     * Get states
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStates()
    {
        return $this->states;
    }
}
