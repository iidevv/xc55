<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Return reason multilingual data.
 *
 * @ORM\Entity
 * @ORM\Table  (name="return_reason_translations",
 *      indexes={
 *          @ORM\Index (name="ci", columns={"code","id"}),
 *          @ORM\Index (name="id", columns={"id"})
 *      }
 * )
 */
class ReturnReasonTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Multilingual return reason
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255, options={ "default": "" })
     */
    protected $reasonName = '';

    /**
     * @var \QSL\Returns\Model\ReturnReason
     *
     * @ORM\ManyToOne (targetEntity="QSL\Returns\Model\ReturnReason", inversedBy="translations")
     * @ORM\JoinColumn (name="id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $owner;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set return reason
     *
     * @param string $name Return reason
     *
     * @return ReturnReasonTranslation
     */
    public function setReasonName($name)
    {
        $this->reasonName = $name;

        return $this;
    }

    /**
     * Get return reason
     *
     * @return string
     */
    public function getReasonName()
    {
        return $this->reasonName;
    }
}
