<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Menu translation
 *
 * @ORM\Entity
 * @ORM\Table  (name="menu_translations",
 *      indexes={
 *          @ORM\Index (name="ci", columns={"code", "id"}),
 *          @ORM\Index (name="id", columns={"id"})
 *      }
 * )
 */
class MenuTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Name
     *
     * @var string
     *
     * @ORM\Column (type="string")
     */
    protected $name;

    /**
     * @var \CDev\SimpleCMS\Model\Menu
     *
     * @ORM\ManyToOne (targetEntity="CDev\SimpleCMS\Model\Menu", inversedBy="translations")
     * @ORM\JoinColumn (name="id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $owner;

    /**
     * Set name
     *
     * @param string $name
     * @return MenuTranslation
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
     * @return MenuTranslation
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
