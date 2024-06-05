<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Payment;

use Doctrine\ORM\Mapping as ORM;
use Includes\Utils\ConfigParser;
use Includes\Utils\Module\Module;
use XCart\Domain\ModuleManagerDomain;
use XLite\Core\Cache\ExecuteCachedTrait;

/**
 * Payment method
 *
 * @ORM\Entity
 * @ORM\Table  (name="payment_methods",
 *      indexes={
 *          @ORM\Index (name="orderby", columns={"orderby"}),
 *          @ORM\Index (name="class", columns={"class","enabled"}),
 *          @ORM\Index (name="enabled", columns={"enabled"}),
 *          @ORM\Index (name="serviceName", columns={"service_name"})
 *      }
 * )
 */
class Method extends \XLite\Model\Base\I18n
{
    use ExecuteCachedTrait;

    /**
     * Type codes
     */
    public const TYPE_ALLINONE    = 'A';
    public const TYPE_CC_GATEWAY  = 'C';
    public const TYPE_ALTERNATIVE = 'N';
    public const TYPE_OFFLINE     = 'O';

    /**
     * Payment method unique id
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer")
     */
    protected $method_id;

    /**
     * Method service name (gateway or API name)
     *
     * @var string
     *
     * @ORM\Column (type="string", length=128)
     */
    protected $service_name;

    /**
     * Process class name
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $class;

    /**
     * Specific module family name
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $moduleName = '';

    /**
     * Position
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $orderby = 0;

    /**
     * Position in popup
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $adminOrderby = 0;

    /**
     * Enabled status
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $enabled = false;

    /**
     * Added status
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $added = false;

    /**
     * Credit card rate
     *
     * @var string
     *
     * @ORM\Column (type="string")
     */
    protected $creditCardRate = '';

    /**
     * Transaction fee
     *
     * @var string
     *
     * @ORM\Column (type="string")
     */
    protected $transactionFee = '';

    /**
     * Predefined status
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $predefined = false;

    /**
     * Type
     *
     * @var string
     *
     * @ORM\Column (type="string", options={ "fixed": true }, length=1)
     */
    protected $type = self::TYPE_OFFLINE;

    /**
     * Settings
     *
     * @var \XLite\Model\Payment\MethodSetting
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\Payment\MethodSetting", mappedBy="payment_method", cascade={"all"})
     */
    protected $settings;

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
     * @ORM\Column (type="string", nullable=true)
     */
    protected $modulePageURL;

    /**
     * Countries of merchant (merchants from these countries can sign up and use this method)
     *
     * @var array
     *
     * @ORM\Column (type="array", nullable=true)
     */
    protected $countries;

    /**
     * Excluded countries (merchants from these countries cannot sign up for payment account)
     *
     * @var array
     *
     * @ORM\Column (type="array", nullable=true)
     */
    protected $exCountries;

    /**
     * Settings
     *
     * @var \XLite\Model\Payment\MethodCountryPosition[]
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\Payment\MethodCountryPosition", mappedBy="paymentMethod", cascade={"all"})
     */
    protected $countryPositions;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\Payment\MethodTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * Get processor
     *
     * @return \XLite\Model\Payment\Base\Processor
     */
    public function getProcessor()
    {
        $class = $this->getClass();

        return class_exists($class) ? $class::getInstance() : null;
    }

    /**
     * @return bool
     */
    public function isExisting()
    {
        if ($this->isModuleInstalled()) {
            $this->isFileWithPaymentClassExists();
            $result = $this->isModuleEnabled()
                ? $this->isPaymentClassExists()
                : $this->isFileWithPaymentClassExists();
        } else {
            $result = true;
        }

        return $result;
    }

    protected function isPaymentClassExists()
    {
        return class_exists($this->getClass());
    }

    protected function isFileWithPaymentClassExists()
    {
        $class = $this->getClass();

        if (
            strpos($class, 'XLite') !== 0
            && preg_match('/(\w+\\\\\w+)\\\\(.*)/', $class, $matches)
        ) {
            return file_exists(
                LC_DIR_MODULES
                . str_replace('\\', '/', $matches[1] . '/src/' . $matches[2])
                . '.php'
            );
        }

        return file_exists(
            LC_DIR_CLASSES
            . str_replace('\\', '/', $class)
            . '.php'
        );
    }

    /**
     * Check - enabled method or not
     * FIXME - must be removed
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return ($this->getEnabled() || $this->isForcedEnabled())
            && $this->getAdded()
            && $this->isModuleEnabled()
            && $this->getProcessor()
            && $this->getProcessor()->isConfigured($this);
    }

    /**
     * Set class
     *
     * @return void
     */
    public function setClass($class)
    {
        $this->class = $class;

        if (strpos($class, 'XLite') !== 0) {
            [$author, $name] = explode('\\', $class, 3);

            $this->setModuleName($author . '_' . $name);
        }
    }

    /**
     * Get setting value by name
     *
     * @param string $name Name
     *
     * @return string|void
     */
    public function getSetting($name)
    {
        $entity = $this->getSettingEntity($name);

        return $entity ? $entity->getValue() : null;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->getOrderby();
    }

    /**
     * Set position
     *
     * @param integer $position Position
     *
     * @return integer
     */
    public function setPosition($position)
    {
        return $this->setOrderby($position);
    }

    /**
     * Get setting by name
     *
     * @param string $name Name
     *
     * @return \XLite\Model\Payment\MethodSetting
     */
    public function getSettingEntity($name)
    {
        $result = null;

        foreach ($this->getSettings() as $setting) {
            if ($setting->getName() == $name) {
                $result = $setting;
                break;
            }
        }

        return $result;
    }

    /**
     * Set setting value by name
     *
     * @param string $name  Name
     * @param string $value Value
     *
     * @return boolean
     */
    public function setSetting($name, $value)
    {
        $result = false;

        // Update settings which is already stored in database
        $setting = $this->getSettingEntity($name);

        if ($setting) {
            $setting->setValue(strval($value));
            $result = true;
        } else {
            // Create setting which is not in database but specified in the processor class

            $processor = $this->getProcessor();

            if ($processor && method_exists($processor, 'getAvailableSettings')) {
                $availableSettings = $processor->getAvailableSettings();

                if (in_array($name, $availableSettings)) {
                    $setting = new \XLite\Model\Payment\MethodSetting();
                    $setting->setName($name);
                    $setting->setValue(strval($value));
                    $setting->setPaymentMethod($this);
                    $this->addSettings($setting);

                    \XLite\Core\Database::getEM()->persist($setting);
                }
            }
        }

        return $result;
    }

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     *
     * @return void
     */
    public function __construct(array $data = [])
    {
        $this->settings     = new \Doctrine\Common\Collections\ArrayCollection();
        $this->transactions = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Call processor methods
     *
     * @param string $method    Method name
     * @param array  $arguments Arguments OPTIONAL
     *
     * @return mixed
     */
    public function __call($method, array $arguments = [])
    {
        array_unshift($arguments, $this);

        return $this->getProcessor()
            ? call_user_func_array([$this->getProcessor(), $method], $arguments)
            : null;
    }

    /**
     * Get warning note
     *
     * @return string
     */
    public function getWarningNote()
    {
        $message = null;

        if ($this->getProcessor() && !$this->getProcessor()->isConfigured($this)) {
            $message = static::t('The method is not configured and cannot be used');
        }

        if (!$message) {
            $message = $this->getProcessor() ? $this->getProcessor()->getWarningNote($this) : null;
        }

        return $message;
    }

    /**
     * Get payment method admin zone icon URL
     *
     * @return string
     */
    public function getAdminIconURL()
    {
        $processor = $this->getProcessor();
        [$author, $name] = explode('_', $this->getModuleName());

        $url = $processor
            ? $processor->getAdminIconURL($this)
            : null;

        if ($url === true) {
            $url = $author && $name
                ? \XLite\Core\Layout::getInstance()
                    ->getResourceWebPath('modules/' . $author . '/' . $name . '/method_icon.png')
                : null;
        }

        if (!$url) {
            $addonImagesUrl = ConfigParser::getOptions(['marketplace', 'addon_images_url']);
            $url            = "{$addonImagesUrl}{$author}/{$name}/list_icon.jpg";
        }

        return $url;
    }

    /**
     * Get payment method alternative admin zone icon URL
     *
     * @return string
     */
    public function getAltAdminIconURL()
    {
        [$author, $name] = Module::explodeModuleId($this->getProcessor()->getModuleId());

        return $author && $name
            ? \XLite\Core\Layout::getInstance()->getResourceWebPath(
                'modules/' . $author . '/' . $name . '/method_icon_' . $this->getAdaptedServiceName() . '.png'
            )
            : null;
    }

    /**
     * Get adapted service name (e.g. 'Sage Pay form protocol' will be converted to 'Sage_Pay_form_protocol')
     *
     * @return string
     */
    public function getAdaptedServiceName()
    {
        return preg_replace('/_+/', '_', preg_replace('/[^\w\d]+/', '_', $this->getServiceName()));
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled Property value
     *
     * @return \XLite\Model\Payment\Method
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        if ($this->getProcessor()) {
            $this->getProcessor()->enableMethod($this);
        }

        return $this;
    }

    /**
     * Translation getter
     *
     * @return string
     */
    public function getDescription()
    {
        $description = $this->getSoftTranslation()->getDescription();

        if (\XLite\Core\Auth::getInstance()->isOperatingAsUserMode()) {
            $methods = \XLite\Core\Auth::getInstance()->getOperateAsUserPaymentMethods();

            $currentServiceName = $this->getServiceName();

            $found = array_reduce(
                $methods,
                static function ($carry, $method) use ($currentServiceName) {
                    return $carry ?: $method->getServiceName() === $currentServiceName;
                },
                false
            );

            if ($found && !$this->isEnabled()) {
                $description = static::t('This method is displayed because you are logged in as admin and operating as another user');
            }
        }

        return $description;
    }

    /**
     * Set 'added' property
     *
     * @param boolean $added Property value
     *
     * @return \XLite\Model\Payment\Method
     */
    public function setAdded($added)
    {
        $this->added = $added;

        if (!$added) {
            $this->setEnabled(false);
        }

        return $this;
    }

    /**
     * Get message why we can't switch payment method
     *
     * @return string
     */
    public function getNotSwitchableReason()
    {
        return static::t('This payment method is not configured.');
    }

    /**
     * Get payment module ID
     *
     * @return string|null
     */
    public function getModuleId()
    {
        [$author, $name] = explode('_', $this->getModuleName());

        $moduleId = Module::buildId($author, $name);

        return $this->getModuleManagerDomain()->isEnabled($moduleId)
            ? $moduleId
            : null;
    }

    /**
     * @return bool
     */
    public function isModuleInstalled()
    {
        if (!$this->getModuleName()) {
            return false; // for example PhoneOrdering
        }

        [$author, $name] = explode('_', $this->getModuleName());

        $moduleId = Module::buildId($author, $name);

        return $this->getModuleManagerDomain()->isInstalled($moduleId);
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
     * Set service_name
     *
     * @param string $serviceName
     *
     * @return Method
     */
    public function setServiceName($serviceName)
    {
        $this->service_name = $serviceName;

        return $this;
    }

    /**
     * Get service_name
     *
     * @return string
     */
    public function getServiceName()
    {
        return $this->service_name;
    }

    /**
     * Get class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
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
     * Get moduleName
     *
     * @return string
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }

    /**
     * Set orderby
     *
     * @param integer $orderby
     *
     * @return Method
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
     * Set adminOrderby
     *
     * @param integer $adminOrderby
     *
     * @return Method
     */
    public function setAdminOrderby($adminOrderby)
    {
        $this->adminOrderby = $adminOrderby;

        return $this;
    }

    /**
     * Get adminOrderby
     *
     * @return integer
     */
    public function getAdminOrderby()
    {
        return $this->adminOrderby;
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
     * Get moduleEnabled
     *
     * @return boolean
     */
    public function isModuleEnabled()
    {
        $moduleName = $this->getModuleName();

        return $this->executeCachedRuntime(
            function () use ($moduleName) {
                $result = true;

                if ($moduleName) {
                    [$author, $name] = explode('_', $moduleName);

                    $moduleId = Module::buildId($author, $name);

                    $result = $this->getModuleManagerDomain()->isEnabled($moduleId);
                }

                return $result;
            },
            [__CLASS__, __METHOD__, $moduleName]
        );
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
     * Set type
     *
     * @param string $type
     *
     * @return Method
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
     * @param string $modulePageURL
     *
     * @return Method
     */
    public function setModulePageURL($modulePageURL)
    {
        $this->modulePageURL = $modulePageURL;

        return $this;
    }

    /**
     * @return string
     */
    public function getModulePageURL()
    {
        return $this->modulePageURL;
    }

    /**
     * Set countries
     *
     * @param array $countries
     *
     * @return Method
     */
    public function setCountries($countries)
    {
        $this->countries = $countries;

        return $this;
    }

    /**
     * Get countries
     *
     * @return array
     */
    public function getCountries()
    {
        return $this->countries;
    }

    /**
     * Set exCountries
     *
     * @param array $exCountries
     *
     * @return Method
     */
    public function setExCountries($exCountries)
    {
        $this->exCountries = $exCountries;

        return $this;
    }

    /**
     * Get exCountries
     *
     * @return array
     */
    public function getExCountries()
    {
        return $this->exCountries;
    }

    /**
     * Add settings
     *
     * @param \XLite\Model\Payment\MethodSetting $settings
     *
     * @return Method
     */
    public function addSettings(\XLite\Model\Payment\MethodSetting $settings)
    {
        $this->settings[] = $settings;

        return $this;
    }

    /**
     * Get settings
     *
     * @return \Doctrine\Common\Collections\Collection|\XLite\Model\Payment\MethodSetting[]
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @return MethodCountryPosition[]
     */
    public function getCountryPositions()
    {
        return $this->countryPositions;
    }

    /**
     * @param string $countryCode
     *
     * @return MethodCountryPosition|null
     */
    public function getCountryPosition($countryCode): ?MethodCountryPosition
    {
        foreach ($this->countryPositions ?: [] as $countryPosition) {
            if ($countryPosition->getCountryCode() === $countryCode) {
                return $countryPosition;
            }
        }

        return null;
    }

    /**
     * @param MethodCountryPosition $countryPosition
     */
    public function addCountryPositions($countryPosition): void
    {
        $this->countryPositions[] = $countryPosition;
    }

    /**
     * @param string $creditCardRate
     */
    public function setCreditCardRate($creditCardRate)
    {
        $this->creditCardRate = $creditCardRate;
    }

    /**
     * @return string
     */
    public function getCreditCardRate()
    {
        return $this->creditCardRate;
    }

    /**
     * @param string $transactionFee
     */
    public function setTransactionFee($transactionFee)
    {
        $this->transactionFee = $transactionFee;
    }

    /**
     * @return string
     */
    public function getTransactionFee()
    {
        return $this->transactionFee;
    }

    /**
     * @return boolean
     */
    public function isOffline()
    {
        return $this->type === self::TYPE_OFFLINE;
    }

    /**
     * @return ModuleManagerDomain
     */
    protected function getModuleManagerDomain()
    {
        return \XCart\Container::getContainer()->get(ModuleManagerDomain::class);
    }

    // {{{ Translation Getters / setters

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $title
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setTitle($title)
    {
        return $this->setTranslationField(__FUNCTION__, $title);
    }

    /**
     * @param string $description
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setDescription($description)
    {
        return $this->setTranslationField(__FUNCTION__, $description);
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

    /**
     * @return string
     */
    public function getAltAdminDescription()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $altAdminDescription
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setAltAdminDescription($altAdminDescription)
    {
        return $this->setTranslationField(__FUNCTION__, $altAdminDescription);
    }

    /**
     * @return string
     */
    public function getInstruction()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $instruction
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setInstruction($instruction)
    {
        return $this->setTranslationField(__FUNCTION__, $instruction);
    }

    // }}}
}
