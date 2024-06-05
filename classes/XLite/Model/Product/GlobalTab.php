<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Product;

use Doctrine\ORM\Mapping as ORM;

/**
 * The "tab" model class
 *
 * @ORM\Entity
 * @ORM\Table  (name="global_product_tabs",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint (name="service_name", columns={"service_name"})
 *      }
 * )
 */
class GlobalTab extends \XLite\Model\AEntity implements \XLite\Model\Product\IProductTab, \XLite\Model\Base\IModuleRelatedEntity
{
    public const TYPE_WIDGET = 'widget';
    public const TYPE_LIST = 'list';
    public const TYPE_TEMPLATE = 'template';


    /**
     * Tab unique ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Tab position
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $position = 0;

    /**
     * Tab name
     *
     * @var string
     *
     * @ORM\Column (type="string", nullable=true)
     */
    protected $service_name;

    /**
     * Tab provider(module namespace or core)
     *
     * @var \XLite\Model\Product\GlobalTabProvider[]
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\Product\GlobalTabProvider", mappedBy="tab", cascade={"all"})
     */
    protected $providers;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected ?string $module = null;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->providers = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
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
     * Return Position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set Position
     *
     * @param int $position
     *
     * @return $this
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * Return Name
     *
     * @return string|null
     */
    public function getServiceName()
    {
        return $this->service_name;
    }

    /**
     * Set Name
     *
     * @param string $service_name
     *
     * @return $this
     */
    public function setServiceName($service_name)
    {
        $this->service_name = $service_name;
        return $this;
    }

    /**
     * Return Providers
     *
     * @return GlobalTabProvider[]
     */
    public function getProviders()
    {
        return $this->providers;
    }

    /**
     * Set Providers
     *
     * @param GlobalTabProvider $provider
     *
     * @return $this
     */
    public function addProvider($provider)
    {
        $this->providers[] = $provider;
        return $this;
    }

    /**
     * Get provider by code
     *
     * @param $code
     *
     * @return array
     */
    public function getProviderByCode($code)
    {
        $provider = array_filter($this->getProviders()->toArray(), static function ($v, $k) use ($code) {
            /** @var \XLite\Model\Product\GlobalTabProvider $v */
            return $v->getCode() === $code;
        }, ARRAY_FILTER_USE_BOTH);

        return !empty($provider) ? array_shift($provider) : null;
    }

    /**
     * Check if at least one provider available
     *
     * @return boolean
     */
    public function checkProviders()
    {
        foreach ($this->getProviders() as $provider) {
            if ($provider->checkProvider()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if tab available
     *
     * @return bool
     */
    public function isAvailable()
    {
        return $this->checkProviders();
    }

    /**
     * Returns tab name
     *
     * @return string
     */
    public function getName()
    {
        return static::t($this->getServiceName());
    }

    public function getModule(): ?string
    {
        return $this->module;
    }

    public function setModule(string $module): void
    {
        $this->module = $module;
    }
}
