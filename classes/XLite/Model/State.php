<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * State
 *
 * @ORM\Entity
 * @ORM\Table (name="states",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint (name="code", columns={"code","country_code"})
 *      },
 *      indexes={
 *          @ORM\Index (name="state", columns={"state"})
 *      }
 * )
 * @ORM\HasLifecycleCallbacks
 */
class State extends \XLite\Model\AEntity
{
    /**
     * State unique id
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column (type="integer")
     */
    protected $state_id;

    /**
     * State name
     *
     * @var string
     *
     * @ORM\Column (type="string", length=64)
     */
    protected $state;

    /**
     * State code
     *
     * @var string
     *
     * @ORM\Column (type="string", length=64)
     */
    protected $code;

    /**
     * Country (relation)
     *
     * @var \XLite\Model\Country
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\Country", inversedBy="states", cascade={"merge","detach"})
     * @ORM\JoinColumn (name="country_code", referencedColumnName="code", onDelete="CASCADE")
     */
    protected $country;

    /**
     * Region (relation)
     *
     * @var \XLite\Model\Region
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\Region", inversedBy="states", cascade={"merge","detach"})
     * @ORM\JoinColumn (name="region_code", referencedColumnName="code", onDelete="CASCADE")
     */
    protected $region;

    /**
     * Set code
     *
     * @param string $code Code
     *
     * @return void
     */
    public function setCode($code)
    {
        if ($this->code != $code && $this->getCountry()) {
            $elements = \XLite\Core\Database::getRepo('XLite\Model\ZoneElement')->findBy(
                [
                    'element_type'  => \XLite\Model\ZoneElement::ZONE_ELEMENT_STATE,
                    'element_value' => $this->getCountry()->getCode() . '_' . $this->code,
                ]
            );

            foreach ($elements as $element) {
                $element->setElementValue($this->getCountry()->getCode() . '_' . $code);
            }

            if ($elements) {
                \XLite\Core\Database::getRepo('XLite\Model\Zone')->cleanCache();
            }
        }

        $this->code = $code;

        return $this;
    }

    /**
     * Remove zone elements
     *
     * @return void
     * @ORM\PreRemove
     */
    public function removeZoneElements()
    {
        $elements = \XLite\Core\Database::getRepo('XLite\Model\ZoneElement')->findBy(
            [
                'element_type'  => \XLite\Model\ZoneElement::ZONE_ELEMENT_STATE,
                'element_value' => $this->getCountry()->getCode() . '_' . $this->getCode(),
            ]
        );

        foreach ($elements as $element) {
            \XLite\Core\Database::getEM()->remove($element);
        }
    }

    /**
     * Get state_id
     *
     * @return integer
     */
    public function getStateId()
    {
        return $this->state_id;
    }

    /**
     * Set state
     *
     * @param string $state
     * @return State
     */
    public function setState($state)
    {
        $this->state = $state;
        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
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
     * Set country
     *
     * @param \XLite\Model\Country $country
     * @return State
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
     * Set region
     *
     * @param \XLite\Model\Region $region
     * @return State
     */
    public function setRegion(\XLite\Model\Region $region = null)
    {
        $this->region = $region;
        return $this;
    }

    /**
     * Get region
     *
     * @return \XLite\Model\Region
     */
    public function getRegion()
    {
        return $this->region;
    }
}
