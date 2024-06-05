<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Memebership translations
 *
 * @ORM\Entity
 * @ORM\Table (name="membership_translations",
 *      indexes={
 *          @ORM\Index (name="ci", columns={"code","id"}),
 *          @ORM\Index (name="id", columns={"id"})
 *      }
 * )
 */
class MembershipTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Membership name
     *
     * @var string
     *
     * @ORM\Column (type="string", length=128)
     */
    protected $name;

    /**
     * @var \XLite\Model\Membership
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\Membership", inversedBy="translations")
     * @ORM\JoinColumn (name="id", referencedColumnName="membership_id", onDelete="CASCADE")
     */
    protected $owner;

    /**
     * Set name
     *
     * @param string $name
     * @return MembershipTranslation
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
     * @return MembershipTranslation
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
