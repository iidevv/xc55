<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use ApiPlatform\Core\Annotation as ApiPlatform;
use Doctrine\ORM\Mapping as ORM;
use XLite\API\Endpoint\ProfileAddress\DTO\ProfileAddressInput as Input;
use XLite\API\Endpoint\ProfileAddress\DTO\ProfileAddressOutput as Output;

/**
 * @ORM\Entity
 * @ORM\Table (
 *     name="profile_addresses",
 *     indexes={
 *         @ORM\Index (name="is_billing", columns={"is_billing"}),
 *         @ORM\Index (name="is_shipping", columns={"is_shipping"})
 *     }
 * )
 * @ORM\HasLifecycleCallbacks
 * @ApiPlatform\ApiResource(
 *     input=Input::class,
 *     output=Output::class,
 *     itemOperations={
 *          "get"={
 *              "method"="GET",
 *              "path"="/profiles/{profile_id}/addresses/{address_id}.{_format}",
 *              "identifiers"={"profile_id", "address_id"},
 *              "requirements"={"profile_id"="\d+", "address_id"="\d+"},
 *              "openapi_context"={
 *                  "summary"="Retrieve an address from a profile",
 *                  "parameters"={
 *                      {"name"="profile_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                      {"name"="address_id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "put"={
 *              "method"="PUT",
 *              "path"="/profiles/{profile_id}/addresses/{address_id}.{_format}",
 *              "identifiers"={"profile_id", "address_id"},
 *              "requirements"={"profile_id"="\d+", "address_id"="\d+"},
 *              "openapi_context"={
 *                  "summary"="Update an address of a profile",
 *                  "parameters"={
 *                      {"name"="profile_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                      {"name"="address_id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "delete"={
 *              "method"="DELETE",
 *              "path"="/profiles/{profile_id}/addresses/{address_id}.{_format}",
 *              "identifiers"={"profile_id", "address_id"},
 *              "requirements"={"profile_id"="\d+", "address_id"="\d+"},
 *              "openapi_context"={
 *                  "summary"="Delete an address from a profile",
 *                  "parameters"={
 *                      {"name"="profile_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                      {"name"="address_id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          }
 *     },
 *     collectionOperations={
 *          "get"={
 *              "method"="GET",
 *              "path"="/profiles/{profile_id}/addresses.{_format}",
 *              "identifiers"={"profile_id", "address_id"},
 *              "requirements"={"profile_id"="\d+"},
 *              "openapi_context"={
 *                  "summary"="Retrieve a list of addresses from a profile",
 *                  "parameters"={
 *                      {"name"="profile_id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  },
 *              }
 *          },
 *          "post"={
 *              "method"="POST",
 *              "path"="/profiles/{profile_id}/addresses.{_format}",
 *              "controller"="xcart.api.profile_address.controller",
 *              "identifiers"={"profile_id", "address_id"},
 *              "requirements"={"profile_id"="\d+"},
 *              "openapi_context"={
 *                  "summary"="Add an address to a profile",
 *                  "parameters"={
 *                      {"name"="profile_id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          }
 *     }
 * )
 *
 * @method string getFirstname
 * @method string getLastname
 * @method string getStreet
 * @method string getZipcode
 * @method string getCity
 */
class Address extends \XLite\Model\Base\PersonalAddress
{
    /**
     * Address type codes
     */
    public const BILLING  = 'b';
    public const SHIPPING = 's';

    /**
     * Address fields collection
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\AddressFieldValue", mappedBy="address", cascade={"all"})
     */
    protected $addressFields;

    /**
     * Flag: is it a billing address
     *
     * @var bool
     *
     * @ORM\Column (type="boolean")
     */
    protected $is_billing = false;

    /**
     * Flag: is it a shipping address
     *
     * @var bool
     *
     * @ORM\Column (type="boolean")
     */
    protected $is_shipping = false;

    /**
     * Flag: is it a work address
     *
     * @var bool
     *
     * @ORM\Column (type="boolean")
     */
    protected $isWork = false;

    /**
     * @var \XLite\Model\Profile
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\Profile", inversedBy="addresses", cascade={"persist","merge","detach"})
     * @ORM\JoinColumn (name="profile_id", referencedColumnName="profile_id", onDelete="CASCADE")
     */
    protected $profile;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->addressFields = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Universal setter
     *
     * @param string $property
     * @param mixed  $value
     *
     * @return true|null Returns TRUE if the setting succeeds. NULL if the setting fails
     */
    public function setterProperty($property, $value)
    {
        $result = parent::setterProperty($property, $value);

        if ($result === null) {
            $addressField = \XLite\Core\Database::getRepo('XLite\Model\AddressField')
                ->findOneBy(['serviceName' => $property]);

            if ($addressField) {
                $repo = \XLite\Core\Database::getRepo('XLite\Model\AddressFieldValue');

                $addressFieldValue = $this->getFieldValue($property);

                if ($addressFieldValue) {
                    $addressFieldValue->setValue($value);
                    if ($this->isPersistent()) {
                        $repo->update($addressFieldValue);
                    }
                } else {
                    $addressFieldValue = new \XLite\Model\AddressFieldValue();
                    $addressFieldValue->map([
                        'address'      => $this,
                        'addressField' => $addressField,
                        'value'        => $value,
                    ]);
                    $this->addAddressFields($addressFieldValue);
                    if ($this->isPersistent()) {
                        $repo->insert($addressFieldValue);
                    }
                }

                $result = true;
            } else {
                // Log wrong access to property
                $this->logWrongAddressPropertyAccess($property, false);
            }
        }

        return $result;
    }

    /**
     * Update searchFakeField of profile
     *
     * @return boolean
     */
    public function update()
    {
        $result = parent::update();

        if ($this->getProfile()) {
            \XLite\Core\Database::getEM()->refresh($this->getProfile());
            $this->getProfile()->updateSearchFakeField();
            \XLite\Core\Database::getEM()->flush();
        }

        return $result;
    }

    /**
     * Update searchFakeField of profile
     *
     * @return boolean
     */
    public function delete()
    {
        // We are using id because we can't user em->refresh or em->merge
        $profileId = $this->getProfile()
            ? $this->getProfile()->getProfileId()
            : null;

        $result = parent::delete();
        if ($profileId) {
            $profile = \XLite\Core\Database::getRepo('XLite\Model\Profile')->find($profileId);

            $profile->updateSearchFakeField();
        }

        return $result;
    }

    /**
     * Update searchFakeField of profile
     *
     * @return boolean
     */
    public function create()
    {
        $result = parent::create();

        if ($this->getProfile()) {
            \XLite\Core\Database::getEM()->refresh($this->getProfile());
            $this->getProfile()->updateSearchFakeField();
        }

        return $result;
    }

    /**
     * Universal getter
     *
     * @param string $property
     *
     * @return mixed|null Returns NULL if it is impossible to get the property
     */
    public function getterProperty($property)
    {
        $result = parent::getterProperty($property);

        if ($result === null) {
            $addressField = static::getAddressFieldByServiceName($property);

            if ($addressField) {
                $addressFieldValue = $this->getFieldValue($property);

                $result = $addressFieldValue
                    ? $addressFieldValue->getValue()
                    : static::getDefaultFieldPlainValue($property);
            } else {
                // Log wrong access to property
                $this->logWrongAddressPropertyAccess($property);
            }
        }

        return $result;
    }

    /**
     * Disable default logging of access to wrong property
     *
     * @param string  $property Property name
     * @param boolean $isGetter Flag: is called property getter (true) or setter (false) OPTIONAL
     *
     * @return void
     */
    protected function logWrongPropertyAccess($property, $isGetter = true)
    {
    }

    /**
     * Log access to unknown address property
     *
     * @param string  $property Property name
     * @param boolean $isGetter Flag: is called property getter (true) or setter (false) OPTIONAL
     *
     * @return void
     */
    protected function logWrongAddressPropertyAccess($property, $isGetter = true)
    {
        parent::logWrongPropertyAccess($property, $isGetter);
    }

    /**
     * Return true if specified property exists
     *
     * @param string $name Property name
     *
     * @return boolean
     */
    public function isPropertyExists($name)
    {
        return parent::isPropertyExists($name)
            || (bool)static::getAddressFieldByServiceName($name);
    }

    /**
     * Get field value
     *
     * @param string $name Field name
     *
     * @return \XLite\Model\AddressFieldValue
     */
    public function getFieldValue($name)
    {
        $addressFieldValue = null;

        $addressField = static::getAddressFieldByServiceName($name);

        if ($addressField) {
            foreach ($this->getAddressFields() as $field) {
                if (
                    $field->getAddressField()
                    && (int)$field->getAddressField()->getId() === (int)$addressField->getId()
                ) {
                    $addressFieldValue = $field;
                    break;
                }
            }
        }

        return $addressFieldValue;
    }

    public static function createDefaultShippingAddress()
    {
        $address = new \XLite\Model\Address();

        $requiredFields = ['country', 'state', 'custom_state', 'zipcode', 'city'];

        $data = [];
        foreach ($requiredFields as $fieldName) {
            if (!isset($data[$fieldName]) && \XLite\Model\Address::getDefaultFieldValue($fieldName)) {
                $data[$fieldName] = \XLite\Model\Address::getDefaultFieldValue($fieldName);
            }
        }

        $address->map($data);

        return $address;
    }

    public function checkAddress()
    {
        /** @var \XLite\Model\AddressFieldValue $addressFieldValue */
        foreach ($this->getAddressFields() as $addressFieldValue) {
            $serviceName = $addressFieldValue->getAddressField()
                ? $addressFieldValue->getAddressField()->getServiceName()
                : '';
            $val = $addressFieldValue->getValue();
            if (!$this->checkAddressField($serviceName, $val)) {
                return false;
            }
        }

        return true;
    }

    public function restoreInvalid()
    {
        /** @var \XLite\Model\AddressFieldValue $addressFieldValue */
        foreach ($this->getAddressFields() as $addressFieldValue) {
            $serviceName = $addressFieldValue->getAddressField()
                ? $addressFieldValue->getAddressField()->getServiceName()
                : '';
            $val = $addressFieldValue->getValue();
            if (!$this->checkAddressField($serviceName, $val)) {
                $addressFieldValue->setValue(static::getDefaultFieldValue($serviceName));
            }
        }
    }

    /**
     * Get default value for the field
     *
     * @param string $fieldName Field service name
     *
     * @return mixed
     */
    public static function getDefaultFieldValue($fieldName)
    {
        $result = null;

        switch ($fieldName) {
            case 'country':
                $code = \XLite\Core\Config::getInstance()->Shipping->anonymous_country;
                $result = \XLite\Core\Database::getRepo('XLite\Model\Country')->findOneByCode($code);

                if ($result && !$result->getEnabled()) {
                    $newResult = \XLite\Core\Database::getRepo('XLite\Model\Country')->findOneBy(['enabled' => true]);
                    $result = $newResult ?: $result;
                }
                break;

            case 'country_code':
                $code = \XLite\Core\Config::getInstance()->Shipping->anonymous_country;
                $result = \XLite\Core\Database::getRepo('XLite\Model\Country')->findOneByCode($code);

                if ($result && !$result->getEnabled()) {
                    $newResult = \XLite\Core\Database::getRepo('XLite\Model\Country')->findOneBy(['enabled' => true]);
                    $result = $newResult ?: $result;
                }

                $result = $result->getCode();
                break;

            case 'state':
                $id = \XLite\Core\Config::getInstance()->Shipping->anonymous_state;
                $result = \XLite\Core\Database::getRepo('XLite\Model\State')->find($id);
                break;

            case 'custom_state':
                $result = \XLite\Core\Config::getInstance()->Shipping->anonymous_custom_state;
                break;

            case 'zipcode':
                $result = \XLite\Core\Config::getInstance()->Shipping->anonymous_zipcode;
                break;

            case 'city':
                $result = \XLite\Core\Config::getInstance()->Shipping->anonymous_city;
                break;

            default:
        }

        return $result;
    }


    /**
     * Get required fields by address type
     *
     * @param string $atype Address type code
     *
     * @return array
     */
    public function getRequiredFieldsByType($atype)
    {
        switch ($atype) {
            case static::BILLING:
                $list = \XLite\Core\Database::getRepo('XLite\Model\AddressField')->getBillingRequiredFields();
                break;

            case static::SHIPPING:
                $list = \XLite\Core\Database::getRepo('XLite\Model\AddressField')->getShippingRequiredFields();
                break;

            default:
                $list = null;
            // TODO - add throw exception
        }

        return $list;
    }

    /**
     * Returns array of address fields serviceName/value pairs
     *
     * @return array
     */
    public function serialize()
    {
        $fields = $this->getAddressFields();

        $result = [];

        if ($fields) {
            $result = array_reduce($fields->toArray(), static function ($acc, $item) {
                if ($item->getAddressField()) {
                    $acc[$item->getAddressField()->getServiceName()] = $item->getValue();
                    return $acc;
                }
            }, []);
        }

        return $result;
    }

    /**
     * Clone
     *
     * @return \XLite\Model\AEntity
     */
    public function cloneEntity()
    {
        $entity = parent::cloneEntity();

        foreach (\XLite\Core\Database::getRepo('XLite\Model\AddressField')->findAllEnabled() as $field) {
            if (
                $field->getServiceName() === 'state_id'
                && $this->getCountry()
                && $this->getCountry()->isForcedCustomState()
            ) {
                continue;
            }

            $entity->setterProperty($field->getServiceName(), $this->getterProperty($field->getServiceName()));
        }

        if ($this->getProfile()) {
            $entity->setProfile($this->getProfile());
        }

        return $entity;
    }

    /**
     * Get country
     *
     * @return \XLite\Model\Country
     */
    public function getCountry()
    {
        $result = $this->country;

        if (!$result) {
            $result = \XLite\Model\Address::getDefaultFieldValue('country');
        }

        return $result;
    }


    /**
     * Serialize address to array
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'name'         => trim($this->getFirstname() . ' ' . $this->getLastname()),
            'address'      => $this->getStreet(),
            'city'         => $this->getCity(),
            'state'        => $this->getState() ? $this->getState()->getCode() : '',
            'custom_state' => $this->getCustomState(),
            'zipcode'      => $this->getZipcode(),
            'country'      => $this->getCountry() ? $this->getCountry()->getCode() : '',
            'type'         => $this->getType() ?: \XLite\Core\Config::getInstance()->Shipping->anonymous_address_type,
        ];
    }

    /**
     * Set is_billing
     *
     * @param boolean $isBilling
     *
     * @return Address
     */
    public function setIsBilling($isBilling)
    {
        $this->is_billing = $isBilling;
        return $this;
    }

    /**
     * Get is_billing
     *
     * @return boolean
     */
    public function getIsBilling()
    {
        return $this->is_billing;
    }

    /**
     * Set is_shipping
     *
     * @param boolean $isShipping
     *
     * @return Address
     */
    public function setIsShipping($isShipping)
    {
        $this->is_shipping = $isShipping;
        return $this;
    }

    /**
     * Get is_shipping
     *
     * @return boolean
     */
    public function getIsShipping()
    {
        return $this->is_shipping;
    }

    /**
     * Set isWork
     *
     * @param boolean $isWork
     *
     * @return Address
     */
    public function setIsWork($isWork)
    {
        $this->isWork = $isWork;
        return $this;
    }

    /**
     * Get isWork
     *
     * @return boolean
     */
    public function getIsWork()
    {
        return $this->isWork;
    }

    /**
     * Get address_id
     *
     * @return integer
     */
    public function getAddressId()
    {
        return $this->address_id;
    }

    /**
     * Set address_type
     *
     * @param string $addressType
     *
     * @return Address
     */
    public function setAddressType($addressType)
    {
        $this->address_type = $addressType;
        return $this;
    }

    /**
     * Get address_type
     *
     * @return string
     */
    public function getAddressType()
    {
        return $this->address_type;
    }

    /**
     * Add addressFields
     *
     * @param \XLite\Model\AddressFieldValue $addressFields
     *
     * @return Address
     */
    public function addAddressFields(\XLite\Model\AddressFieldValue $addressFields)
    {
        $this->addressFields[] = $addressFields;
        return $this;
    }

    /**
     * Get addressFields
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAddressFields()
    {
        return $this->addressFields;
    }

    /**
     * Set profile
     *
     * @param \XLite\Model\Profile $profile
     *
     * @return Address
     */
    public function setProfile(\XLite\Model\Profile $profile = null)
    {
        $this->profile = $profile;
        return $this;
    }

    /**
     * Get profile
     *
     * @return \XLite\Model\Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }
}
