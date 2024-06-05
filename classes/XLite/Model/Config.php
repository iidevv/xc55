<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * DB-based configuration registry
 *
 * @ORM\Entity
 * @ORM\Table  (name="config",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint (name="nc", columns={"name", "category"})
 *      },
 *      indexes={
 *          @ORM\Index (name="orderby", columns={"orderby"}),
 *          @ORM\Index (name="type", columns={"type"})
 *      }
 * )
 */
class Config extends \XLite\Model\Base\I18n
{
    /**
     * Name for the Shipping category options
     */
    public const SHIPPING_CATEGORY = 'Shipping';

    /**
     * Prefix for the shipping values
     */
    public const SHIPPING_VALUES_PREFIX = 'anonymous_';

    /**
     * Option unique name
     *
     * @var string
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column (type="integer")
     */
    protected $config_id;

    /**
     * Option name
     *
     * @var string
     *
     * @ORM\Column (type="string", length=32)
     */
    protected $name;

    /**
     * Option category
     *
     * @var string
     *
     * @ORM\Column (type="string", length=64)
     */
    protected $category;

    /**
     * Option type
     * Allowed values:'','text','textarea','checkbox','country','state','select','serialized','separator'
     *     or form field class name
     *
     * @var string
     *
     * @ORM\Column (type="string", length=128)
     */
    protected $type = '';

    /**
     * Option position within category
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $orderby = 0;

    /**
     * Option value
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $value = '';

    /**
     * New value temporary field
     *
     * @var string
     */
    protected $newValue;

    /**
     * Widget parameters
     *
     * @var array
     *
     * @ORM\Column (type="array", nullable=true)
     */
    protected $widgetParameters;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\ConfigTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * Set new value
     *
     * @param string $value Value
     *
     * @return void
     */
    public function setNewValue($value)
    {
        $this->newValue = $value;
    }

    /**
     * Returns new value
     *
     * @return string
     */
    public function getNewValue()
    {
        return $this->newValue;
    }

    /**
     * Get config_id
     *
     * @return integer
     */
    public function getConfigId()
    {
        return $this->config_id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Config
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
     * Set category
     *
     * @param string $category
     * @return Config
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Config
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set orderby
     *
     * @param integer $orderby
     * @return Config
     */
    public function setOrderby($orderby)
    {
        $this->orderby = $orderby;
        return $this;
    }

    /**
     * Get orderby
     *
     * @return integer
     */
    public function getOrderby()
    {
        return $this->orderby;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return Config
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set widgetParameters
     *
     * @param array $widgetParameters
     * @return Config
     */
    public function setWidgetParameters($widgetParameters)
    {
        $this->widgetParameters = $widgetParameters;
        return $this;
    }

    /**
     * Get widgetParameters
     *
     * @return array
     */
    public function getWidgetParameters()
    {
        return $this->widgetParameters;
    }

    /**
     * Detach self
     */
    public function detach()
    {
        \XLite\Core\Database::getEM()->detach($this);

        // prevents detach if not initialized
        if (empty($this->translations) || !$this->translations->isInitialized()) {
            return;
        }

        foreach ($this->getTranslations() as $translation) {
            $translation->detach();
        }
    }

    // {{{ Translation Getters / setters

    /**
     * @return string
     */
    public function getOptionName()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $optionName
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setOptionName($optionName)
    {
        return $this->setTranslationField(__FUNCTION__, $optionName);
    }

    /**
     * @return string
     */
    public function getOptionComment()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $optionComment
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setOptionComment($optionComment)
    {
        return $this->setTranslationField(__FUNCTION__, $optionComment);
    }

    // }}}
}
