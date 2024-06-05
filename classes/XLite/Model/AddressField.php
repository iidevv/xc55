<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table (name="address_field")
 */
class AddressField extends \XLite\Model\Base\I18n implements \XLite\Model\Base\IModuleRelatedEntity
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column (type="integer", nullable=false)
     */
    protected $id;

    /**
     * Service name for address field
     * For example: firstname, lastname, country_code and so on.
     *
     * The field is named with this value in the address forms.
     * Also the "service-name-{$serviceName}" CSS class is added to the field
     *
     * @var string
     * @ORM\Column(type="string", length=128, unique=true, nullable=false)
     */
    protected $serviceName;

    /**
     * Getter name for address field (for AView::getAddressSectionData)
     * For example:
     * country for country_code
     * state for state_id, custom_state
     *
     * The field is named with this value in the address forms.
     * Also the "service-name-{$serviceName}" CSS class is added to the field
     *
     * @var string
     * @ORM\Column(type="string", length=128, nullable=false)
     */
    protected $viewGetterName = '';

    /**
     * Schema class for "Form field widget".
     * This class will be used in form widgets
     *
     * Possible values are:
     *
     * \XLite\View\FormField\Input\Text
     * \XLite\View\FormField\Select\Country
     * \XLite\View\FormField\Select\Title
     *
     * For more information check "\XLite\View\FormField\*" class family.
     *
     * The '\XLite\View\FormField\Input\Text' class (standard input text field)
     * is taken for additional fields by default.
     *
     * @var string
     * @ORM\Column(type="string", length=256, nullable=false)
     */
    protected $schemaClass = '\XLite\View\FormField\Input\Text';

    /**
     * Flag if the field is an additional one (This field could be removed)
     *
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $additional = true;

    /**
     * Flag if the field is a required one
     *
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $required = true;

    /**
     * Flag if the field is an enabled one
     *
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $enabled = true;

    /**
     * @var int
     *
     * @ORM\Column (type="integer")
     */
    protected $position = 0;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\AddressFieldTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected ?string $module;

    public function getModule(): ?string
    {
        return $this->module;
    }

    public function setModule(string $module): void
    {
        $this->module = $module;
    }

    /**
     * Return CSS classes for this field name entry
     *
     * @return string
     */
    public function getCSSFieldName()
    {
        return 'address-' . $this->getServiceName() . ($this->getAdditional() ? ' field-additional' : '');
    }


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
     * Set serviceName
     *
     * @param string $serviceName
     * @return AddressField
     */
    public function setServiceName($serviceName)
    {
        $this->serviceName = $serviceName;
        return $this;
    }

    /**
     * Get serviceName
     *
     * @return string
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }

    /**
     * Set viewGetterName
     *
     * @param string $viewGetterName
     * @return AddressField
     */
    public function setViewGetterName($viewGetterName)
    {
        $this->viewGetterName = $viewGetterName;
        return $this;
    }

    /**
     * Get viewGetterName
     *
     * @return string
     */
    public function getViewGetterName()
    {
        return $this->viewGetterName;
    }

    /**
     * Set schemaClass
     *
     * @param string $schemaClass
     * @return AddressField
     */
    public function setSchemaClass($schemaClass)
    {
        $this->schemaClass = $schemaClass;
        return $this;
    }

    /**
     * Get schemaClass
     *
     * @return string
     */
    public function getSchemaClass()
    {
        return $this->schemaClass;
    }

    /**
     * Set additional
     *
     * @param boolean $additional
     * @return AddressField
     */
    public function setAdditional($additional)
    {
        $this->additional = $additional;
        return $this;
    }

    /**
     * Get additional
     *
     * @return boolean
     */
    public function getAdditional()
    {
        return $this->additional;
    }

    /**
     * Set required
     *
     * @param boolean $required
     * @return AddressField
     */
    public function setRequired($required)
    {
        $this->required = $required;
        return $this;
    }

    /**
     * Get required
     *
     * @return boolean
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return AddressField
     */
    public function setEnabled($enabled)
    {
        $this->enabled = (bool)$enabled;
        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return AddressField
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @return bool
     */
    public function isHidden(): bool
    {
        return static::isHiddenField($this->getServiceName());
    }

    /**
     * Whether the field should not be showed in the address bloks, etc.
     *
     * @param string $serviceName Service name.
     *
     * @return bool True if the field should not be among the visible and editable form fields, false otherwise.
     */
    public static function isHiddenField(string $serviceName): bool
    {
        return in_array($serviceName, [ 'email' ], true);
    }
}
