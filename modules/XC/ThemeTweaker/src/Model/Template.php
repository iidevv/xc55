<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product option group item
 *
 * @ORM\Entity
 * @ORM\Table  (name="theme_tweaker_template")
 */
class Template extends \XLite\Model\AEntity
{
    /**
     * Option unique id
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer")
     */
    protected $id;

    /**
     * Is enabled
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean", options={"default": 1})
     */
    protected $enabled = true;

    /**
     * Original template
     *
     * @var string
     *
     * @ORM\Column (type="string")
     */
    protected $template;

    /**
     * Body
     *
     * @var string
     *
     * @ORM\Column (type="text", nullable=true)
     */
    protected $body;

    /**
     * Last modified timestamp
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $date;

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
     * Set template
     *
     * @param string $template
     * @return Template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    /**
     * Return Enabled
     *
     * @return string
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set Enabled
     *
     * @param boolean $enabled
     *
     * @return $this
     */
    public function setEnabled($enabled)
    {
        $this->enabled = (bool)$enabled;
        return $this;
    }

    /**
     * Get template
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set body
     *
     * @param string $body
     *
     * @return static
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Set date
     *
     * @param integer $date
     * @return Template
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get date
     *
     * @return integer
     */
    public function getDate()
    {
        return $this->date;
    }
}
