<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// vim: set ts=4 sw=4 sts=4 et:

namespace Qualiteam\SkinActMagicImages\Model;


use Doctrine\ORM\Mapping as ORM;

/**
 * Magic360 configuration registry
 *
 * @ORM\Entity (repositoryClass="\Qualiteam\SkinActMagicImages\Model\Repo\Config")
 * @ORM\Table  (name="magic360_config",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint (name="pn", columns={"profile", "name"})
 *      },
 *      indexes={
 *          @ORM\Index (name="profile", columns={"profile"}),
 *          @ORM\Index (name="name", columns={"name"}),
 *          @ORM\Index (name="orderby", columns={"orderby"})
 *      }
 * )
 */
class Config extends \XLite\Model\Base\I18n
{
    /**
     * Option's statuses
     */
    const OPTION_IS_INACTIVE      = 0;
    const OPTION_IS_ACTIVE        = 1;
    const OPTION_IS_ALWAYS_ACTIVE = 2;

    /**
     * Option unique id
     *
     * @var string
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column (type="integer")
     */
    protected $config_id;

    /**
     * Option profile
     *
     * @var string
     *
     * @ORM\Column (type="string", length=64)
     */
    protected $profile;

    /**
     * Option name
     *
     * @var string
     *
     * @ORM\Column (type="string", length=64)
     */
    protected $name;

    /**
     * Option type
     *
     * @var string
     *
     * @ORM\Column (type="string", length=128)
     */
    protected $type = '';

    /**
     * Option position within profile
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
     * Option default value
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $default_value = '';

    /**
     * Option's status
     *
     * @var int
     *
     * @ORM\Column (type="smallint")
     */
    protected $status = self::OPTION_IS_ALWAYS_ACTIVE;

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
     * @ORM\OneToMany (targetEntity="Qualiteam\SkinActMagicImages\Model\ConfigTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * Get option unique id
     *
     * @return integer
     */
    public function getConfigId()
    {
        return $this->config_id;
    }

    /**
     * Get profile
     *
     * @return string
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * Set profile
     *
     * @param string $profile
     *
     * @return Config
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;

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
     * Set name
     *
     * @param string $name
     *
     * @return Config
     */
    public function setName($name)
    {
        $this->name = $name;

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
     * Set type
     *
     * @param string $type
     *
     * @return Config
     */
    public function setType($type)
    {
        $this->type = $type;

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
     * Set orderby
     *
     * @param integer $orderby
     *
     * @return Config
     */
    public function setOrderby($orderby)
    {
        $this->orderby = $orderby;

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
     * Set value
     *
     * @param string $value
     *
     * @return Config
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get default value
     *
     * @return string
     */
    public function getDefaultValue()
    {
        return $this->default_value;
    }

    /**
     * Set default value
     *
     * @param string $defaultValue
     *
     * @return Config
     */
    public function setDefaultValue($defaultValue)
    {
        $this->default_value = $defaultValue;

        return $this;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Config
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get widget parameters
     *
     * @return array
     */
    public function getWidgetParameters()
    {
        return $this->widgetParameters;
    }

    /**
     * Set widget parameters
     *
     * @param array $widgetParameters
     *
     * @return Config
     */
    public function setWidgetParameters($widgetParameters)
    {
        $this->widgetParameters = $widgetParameters;

        return $this;
    }

    /**
     * Translation getter. AUTOGENERATED
     *
     * @return string
     */
    public function getOptionName()
    {
        return $this->getSoftTranslation()->getOptionName();
    }

    /**
     * Translation setter. AUTOGENERATED
     *
     * @param string $value value to set
     *
     * @return void
     */
    public function setOptionName($value)
    {
        $translation = $this->getTranslation();

        if (!$this->hasTranslation($translation->getCode())) {
            $this->addTranslations($translation);
        }

        return $translation->setOptionName($value);
    }

    /**
     * Translation getter. AUTOGENERATED
     *
     * @return string
     */
    public function getOptionComment()
    {
        return $this->getSoftTranslation()->getOptionComment();
    }

    /**
     * Translation setter. AUTOGENERATED
     *
     * @param string $value value to set
     *
     * @return void
     */
    public function setOptionComment($value)
    {
        $translation = $this->getTranslation();

        if (!$this->hasTranslation($translation->getCode())) {
            $this->addTranslations($translation);
        }

        return $translation->setOptionComment($value);
    }
}
