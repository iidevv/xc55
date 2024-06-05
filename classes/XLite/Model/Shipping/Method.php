<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Shipping;

use Doctrine\ORM\Mapping as ORM;
use Includes\Utils\ConfigParser;
use XLite\View\FormField\Input\PriceOrPercent;

/**
 * Shipping method model
 *
 * @ORM\Entity
 * @ORM\Table  (name="shipping_methods",
 *      indexes={
 *          @ORM\Index (name="processor", columns={"processor"}),
 *          @ORM\Index (name="carrier", columns={"carrier"}),
 *          @ORM\Index (name="enabled", columns={"enabled"}),
 *          @ORM\Index (name="position", columns={"position"})
 *      }
 * )
 */
class Method extends \XLite\Model\Base\I18n implements \XLite\Model\Base\IModuleRelatedEntity
{
    /**
     * A unique ID of the method
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer")
     */
    protected $method_id;

    /**
     * Processor class name
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $processor = '';

    /**
     * Carrier of the method (for instance, "UPS" or "USPS")
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $carrier = '';

    /**
     * Unique code of shipping method (within processor space)
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $code = '';

    /**
     * Whether the method is enabled or disabled
     *
     * @var string
     *
     * @ORM\Column (type="boolean")
     */
    protected $enabled = false;

    /**
     * A position of the method among other registered methods
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $position = 0;

    /**
     * Shipping rates (relation)
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\Shipping\Markup", mappedBy="shipping_method", cascade={"all"})
     */
    protected $shipping_markups;

    /**
     * Tax class (relation)
     *
     * @var \XLite\Model\TaxClass
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\TaxClass")
     * @ORM\JoinColumn (name="tax_class_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $taxClass;

    /**
     * Added status
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $added = false;

    /**
     * Specific module family name
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $moduleName = '';

    /**
     * Flag:
     *   1 - method has been got from marketplace,
     *   0 - method has been added after distr or module installation
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $fromMarketplace = false;

    /**
     * Table type
     *
     * @var string
     *
     * @ORM\Column (type="string", options={ "fixed": true }, length=3, nullable=true)
     */
    protected $tableType;

    /**
     * Handling fee (surcharge) for online methods
     *
     * @var float
     *
     * @ORM\Column (type="decimal", precision=14, scale=4)
     */
    protected $handlingFee = 0;

    /**
     * Handling fee type(absolute or percent)
     *
     * @var string
     *
     * @ORM\Column (type="string", length=1)
     */
    protected $handlingFeeType = \XLite\View\FormField\Select\AbsoluteOrPercent::TYPE_ABSOLUTE;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\Shipping\MethodTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected ?string $module;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->shipping_markups = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Get processor class object
     *
     * @return null|\XLite\Model\Shipping\Processor\AProcessor
     */
    public function getProcessorObject()
    {
        return \XLite\Model\Shipping::getProcessorObjectByProcessorId($this->getProcessor());
    }

    /**
     * Returns processor module
     *
     * @return string|null
     */
    public function getProcessorModule()
    {
        return ($processor = $this->getProcessorObject())
            ? $processor->getModule()
            : $this->getModuleName();
    }

    /**
     * Get shipping method admin icon URL
     *
     * @return string
     */
    public function getAdminIconURL()
    {
        [$author, $name] = explode('-', $this->getModuleName());

        $url = $this->getProcessorObject()
            ? $this->getProcessorObject()->getAdminIconURL($this)
            : false;

        if ($url === true || $url === null) {
            $url = $author && $name
                ? \XLite\Core\Layout::getInstance()
                    ->getResourceWebPath('modules/' . $author . '/' . $name . '/method_icon.jpg')
                : null;
        }

        if (!$url) {
            $addonImagesUrl = ConfigParser::getOptions(['marketplace', 'addon_images_url']);
            $url            = "{$addonImagesUrl}{$author}/{$name}/list_icon.jpg";
        }

        return $url;
    }

    /**
     * Return true if rates exists for this shipping method
     *
     * @return boolean
     */
    public function hasRates()
    {
        return (bool) $this->getRatesCount();
    }

    /**
     * Get count of rates specified for this shipping method
     *
     * @return integer
     */
    public function getRatesCount()
    {
        return count($this->getShippingMarkups());
    }

    /**
     * Check if method is enabled
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled
            && ($this->getProcessorObject() === null || $this->getProcessorObject()->isConfigured());
    }

    /**
     * Returns present status
     *
     * @return boolean
     */
    public function isAdded()
    {
        return (bool) $this->added;
    }

    /**
     * Set present status
     *
     * @param boolean $value Value
     */
    public function setAdded($value)
    {
        $changed     = $this->added !== $value;
        $this->added = (bool) $value;

        if (!$this->added) {
            $this->setEnabled(false);
        } elseif ($changed) {
            $this->setPosition($this->getRepository()->getMaxPosition('offline') + 10);
        }
    }

    /**
     * Check if shipping method is from marketplace
     *
     * @return bool
     */
    public function isFromMarketplace()
    {
        return (bool) $this->getFromMarketplace();
    }

    /**
     * Returns module author and name (with underscore as separator)
     *
     * @return string
     */
    public function getModuleName()
    {
        $result = $this->moduleName;

        if (!$this->isFromMarketplace()) {
            $processor = $this->getProcessorObject();
            if ($processor) {
                $result = $processor->getModule();
            }
        }

        return str_replace('_', '-', $result);
    }

    /**
     * Return parent method for online carrier service
     *
     * @return \XLite\Model\Shipping\Method
     */
    public function getParentMethod()
    {
        return $this->getProcessor() !== 'offline' && $this->getCarrier() !== ''
            ? $this->getRepository()->findOnlineCarrier($this->getProcessor())
            : null;
    }

    /**
     * Retuns children methods for online carrier
     *
     * @return array
     */
    public function getChildrenMethods()
    {
        return $this->getProcessor() !== 'offline' && $this->getCarrier() === ''
            ? $this->getRepository()->findMethodsByProcessor($this->getProcessor(), false)
            : [];
    }

    /**
     * Returns handling fee
     *
     * @return float
     */
    public function getHandlingFee()
    {
        return [
            PriceOrPercent::PRICE_VALUE => $this->getHandlingFeeValue(),
            PriceOrPercent::TYPE_VALUE  => $this->getHandlingFeeType(),
        ];
    }

    /**
     * Returns handling fee
     *
     * @return float
     */
    public function getHandlingFeeValue()
    {
        $parentMethod = $this->getParentMethod();

        return $parentMethod ? $parentMethod->getHandlingFeeValue() : $this->handlingFee;
    }

    /**
     * Returns handling fee
     *
     * @param float
     *
     * @return float
     */
    public function setHandlingFeeValue($value)
    {
        $this->handlingFee = $value;

        return $this;
    }

    /**
     * Get method_id
     *
     * @return integer
     */
    public function getMethodId()
    {
        return $this->method_id;
    }

    /**
     * Set processor
     *
     * @param string $processor
     *
     * @return Method
     */
    public function setProcessor($processor)
    {
        $this->processor = $processor;

        return $this;
    }

    /**
     * Get processor
     *
     * @return string
     */
    public function getProcessor()
    {
        return $this->processor;
    }

    /**
     * Set carrier
     *
     * @param string $carrier
     *
     * @return Method
     */
    public function setCarrier($carrier)
    {
        $this->carrier = $carrier;

        return $this;
    }

    /**
     * Get carrier
     *
     * @return string
     */
    public function getCarrier()
    {
        return $this->carrier;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Method
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

    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return Method
     */
    public function setEnabled($enabled)
    {
        $this->enabled = (bool) $enabled;

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
     *
     * @return Method
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
     * Get added
     *
     * @return boolean
     */
    public function getAdded()
    {
        return $this->added;
    }

    /**
     * Set moduleName
     *
     * @param string $moduleName
     *
     * @return Method
     */
    public function setModuleName($moduleName)
    {
        $this->moduleName = $moduleName;

        return $this;
    }

    /**
     * Set fromMarketplace
     *
     * @param boolean $fromMarketplace
     *
     * @return Method
     */
    public function setFromMarketplace($fromMarketplace)
    {
        $this->fromMarketplace = $fromMarketplace;

        return $this;
    }

    /**
     * Get fromMarketplace
     *
     * @return boolean
     */
    public function getFromMarketplace()
    {
        return $this->fromMarketplace;
    }

    /**
     * Set tableType
     *
     * @param string $tableType
     *
     * @return Method
     */
    public function setTableType($tableType)
    {
        $this->tableType = $tableType;

        return $this;
    }

    /**
     * Get tableType
     *
     * @return string
     */
    public function getTableType()
    {
        return $this->tableType;
    }

    /**
     * Set handlingFee
     *
     * @param array $handlingFee
     *
     * @return Method
     */
    public function setHandlingFee($handlingFee)
    {
        $this->setHandlingFeeValue(
            $handlingFee[PriceOrPercent::PRICE_VALUE] ?? 0
        );

        $this->setHandlingFeeType(
            $handlingFee[PriceOrPercent::TYPE_VALUE] ?? \XLite\View\FormField\Select\AbsoluteOrPercent::TYPE_ABSOLUTE
        );

        return $this;
    }

    /**
     * Return handling fee type, possible values:
     * \XLite\View\FormField\Select\AbsoluteOrPercent::TYPE_ABSOLUTE
     * \XLite\View\FormField\Select\AbsoluteOrPercent::TYPE_PERCENT
     *
     * @return string
     */
    public function getHandlingFeeType()
    {
        $parentMethod = $this->getParentMethod();

        return $parentMethod ? $parentMethod->getHandlingFeeType() : $this->handlingFeeType;
    }

    /**
     * Set shipping handling fee type (% or absolute)
     *
     * @param string $handlingFeeType
     *
     * @return $this
     */
    public function setHandlingFeeType($handlingFeeType)
    {
        $this->handlingFeeType = $handlingFeeType;

        return $this;
    }

    /**
     * Add shipping_markups
     *
     * @param \XLite\Model\Shipping\Markup $shippingMarkups
     *
     * @return Method
     */
    public function addShippingMarkups(\XLite\Model\Shipping\Markup $shippingMarkups)
    {
        $this->shipping_markups[] = $shippingMarkups;

        return $this;
    }

    /**
     * Get shipping_markups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getShippingMarkups()
    {
        return $this->shipping_markups;
    }

    /**
     * Set taxClass
     *
     * @param \XLite\Model\TaxClass $taxClass
     *
     * @return Method
     */
    public function setTaxClass(\XLite\Model\TaxClass $taxClass = null)
    {
        $this->taxClass = $taxClass;

        return $this;
    }

    /**
     * Get taxClass
     *
     * @return \XLite\Model\TaxClass
     */
    public function getTaxClass()
    {
        return $this->taxClass;
    }

    /**
     * Get translation
     *
     * @param string  $code             Language code OPTIONAL
     * @param boolean $allowEmptyResult Flag OPTIONAL
     *
     * @return \XLite\Model\Base\Translation
     */
    public function getTranslation($code = null, $allowEmptyResult = false)
    {
        $translation = parent::getTranslation($code, $allowEmptyResult);

        if (
            $translation
            && $translation->getName() === ''
            && $translation->getCode() !== 'en'
        ) {
            $defaultTranslation = $this->getHardTranslation('en');

            if ($defaultTranslation) {
                $translation->setName(
                    $defaultTranslation->getName()
                );
            }
        }

        return $translation;
    }

    public function getModule(): ?string
    {
        return $this->module;
    }

    public function setModule(?string $module): void
    {
        $this->module = $module;
    }

    // {{{ Translation Getters / setters

    /**
     * @return string
     */
    public function getDeliveryTime()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $deliveryTime
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setDeliveryTime($deliveryTime)
    {
        return $this->setTranslationField(__FUNCTION__, $deliveryTime);
    }

    /**
     * @return string
     */
    public function getAdminDescription()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $adminDescription
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setAdminDescription($adminDescription)
    {
        return $this->setTranslationField(__FUNCTION__, $adminDescription);
    }

    // }}}

    public function canBeEstimated(): bool
    {
        return $this->isEnabled() && $this->hasRates();
    }
}
