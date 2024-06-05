<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Country translations
 *
 * @ORM\Entity
 * @ORM\Table (name="country_translations",
 *      indexes={
 *          @ORM\Index (name="ci", columns={"code","id"}),
 *          @ORM\Index (name="country", columns={"country"}),
 *          @ORM\Index (name="id", columns={"id"})
 *      }
 * )
 */
class CountryTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Country name
     *
     * @var string
     *
     * @ORM\Column (type="string", length=64)
     */
    protected $country;

    /**
     * @var \XLite\Model\Country
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\Country", inversedBy="translations")
     * @ORM\JoinColumn (name="id", referencedColumnName="code", onDelete="CASCADE")
     */
    protected $owner;

    /**
     * Set country
     *
     * @param string $country
     * @return CountryTranslation
     */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Get label_id
     *
     * @return integer
     */
    public function getLabelId()
    {
        return $this->label_id;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return CountryTranslation
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
}
