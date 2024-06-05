<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBase\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Special offer types multilingual data
 *
 * @ORM\Entity
 *
 * @ORM\Table (name="special_offer_type_translations",
 *         indexes={
 *              @ORM\Index (name="ci", columns={"code","id"}),
 *              @ORM\Index (name="id", columns={"id"}),
 *              @ORM\Index (name="name", columns={"name"})
 *         }
 * )
 */
class OfferTypeTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Administrative name of the offer type.
     *
     * @var string
     * @ORM\Column (type="string", length=255)
     */
    protected $name;

    /**
     * @var \QSL\SpecialOffersBase\Model\OfferType
     *
     * @ORM\ManyToOne (targetEntity="QSL\SpecialOffersBase\Model\OfferType", inversedBy="translations")
     * @ORM\JoinColumn (name="id", referencedColumnName="type_id", onDelete="CASCADE")
     */
    protected $owner;

    /**
     * Sets the administative name for the offer type.
     *
     * @param string $name Administrative name
     *
     * @return OfferTypeTranslation
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Returns the administative name of the type.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the identifier of the translation.
     *
     * @return integer
     */
    public function getLabelId()
    {
        return $this->label_id;
    }

    /**
     * Sets the language code for the translation.
     *
     * @param string $code Code
     *
     * @return OfferTypeTranslation
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Returns the language code for the translation.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
}
