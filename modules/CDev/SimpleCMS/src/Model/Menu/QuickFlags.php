<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\Model\Menu;

use Doctrine\ORM\Mapping as ORM;

/**
 * Menu quick flags
 *
 * @ORM\Entity
 * @ORM\Table  (name="menu_quick_flags")
 */
class QuickFlags extends \XLite\Model\AEntity
{
    /**
     * Doctrine ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer")
     */
    protected $id;

    /**
     * Total number of submenus
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $submenus_count_all = 0;

    /**
     * Number of enabled submenus
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $submenus_count_enabled = 0;

    /**
     * Relation to a menu entity
     *
     * @var \CDev\SimpleCMS\Model\Menu
     *
     * @ORM\OneToOne   (targetEntity="CDev\SimpleCMS\Model\Menu", inversedBy="quickFlags")
     * @ORM\JoinColumn (name="menu_id", referencedColumnName="id")
     */
    protected $menu;

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
     * Set submenus_count_all
     *
     * @param integer $submenusCountAll
     * @return QuickFlags
     */
    public function setSubmenusCountAll($submenusCountAll)
    {
        $this->submenus_count_all = $submenusCountAll;
        return $this;
    }

    /**
     * Get submenus_count_all
     *
     * @return integer
     */
    public function getSubmenusCountAll()
    {
        return $this->submenus_count_all;
    }

    /**
     * Set submenus_count_enabled
     *
     * @param integer $submenusCountEnabled
     * @return QuickFlags
     */
    public function setSubmenusCountEnabled($submenusCountEnabled)
    {
        $this->submenus_count_enabled = $submenusCountEnabled;
        return $this;
    }

    /**
     * Get submenus_count_enabled
     *
     * @return integer
     */
    public function getSubmenusCountEnabled()
    {
        return $this->submenus_count_enabled;
    }

    /**
     * Set menu
     *
     * @param \CDev\SimpleCMS\Model\Menu $menu
     * @return QuickFlags
     */
    public function setMenu(\CDev\SimpleCMS\Model\Menu $menu = null)
    {
        $this->menu = $menu;
        return $this;
    }

    /**
     * Get menu
     *
     * @return \CDev\SimpleCMS\Model\Menu
     */
    public function getMenu()
    {
        return $this->menu;
    }
}
