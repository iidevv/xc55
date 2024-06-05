<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Return action multilingual data.
 *
 * @ORM\Entity
 * @ORM\Table  (name="return_action_translations",
 *      indexes={
 *          @ORM\Index (name="ci", columns={"code","id"}),
 *          @ORM\Index (name="id", columns={"id"})
 *      }
 * )
 */
class ReturnActionTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Multilingual return action
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255, options={ "default": "" })
     */
    protected $actionName = '';

    /**
     * @var \QSL\Returns\Model\ReturnAction
     *
     * @ORM\ManyToOne (targetEntity="QSL\Returns\Model\ReturnAction", inversedBy="translations")
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
     * Set return action
     *
     * @param string $name Return action
     *
     * @return ReturnActionTranslation
     */
    public function setActionName($name)
    {
        $this->actionName = $name;

        return $this;
    }

    /**
     * Get return reason
     *
     * @return string
     */
    public function getActionName()
    {
        return $this->actionName;
    }
}
