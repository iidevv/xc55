<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Currency multilingual data
 *
 * @ORM\Entity
 * @ORM\Table (name="currency_translations",
 *      indexes={
 *          @ORM\Index (name="ci", columns={"code","id"}),
 *          @ORM\Index (name="id", columns={"id"})
 *      }
 * )
 */
class CurrencyTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Currency name
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $name;

    /**
     * @var \XLite\Model\Currency
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\Currency", inversedBy="translations")
     * @ORM\JoinColumn (name="id", referencedColumnName="currency_id", onDelete="CASCADE")
     */
    protected $owner;

    /**
     * Set name
     *
     * @param string $name
     * @return CurrencyTranslation
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
     * @return CurrencyTranslation
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
