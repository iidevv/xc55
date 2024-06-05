<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use Doctrine\ORM\Mapping as ORM;
use Includes\Utils\Module\Manager;

/**
 * Language
 *
 * @ORM\Entity
 * @ORM\Table (name="languages",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint (name="code3", columns={"code3"}),
 *          @ORM\UniqueConstraint (name="code2", columns={"code"})
 *      },
 *      indexes={
 *          @ORM\Index (name="added", columns={"added"}),
 *          @ORM\Index (name="enabled", columns={"enabled"})
 *      }
 * )
 */
class Language extends \XLite\Model\Base\I18n
{
    /**
     * Unique id
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column (type="integer", unique=true)
     */
    protected $lng_id;

    /**
     * Language alpha-2 code (ISO 639-2)
     *
     * @var string
     *
     * @ORM\Column (type="string", options={ "fixed": true }, length=2, unique=true)
     */
    protected $code;

    /**
     * Language alpha-3 code (ISO 639-3)
     *
     * @var string
     *
     * @ORM\Column (type="string", options={ "fixed": true }, length=3, unique=true)
     */
    protected $code3 = '';

    /**
     * Right-to-left flag
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $r2l = false;

    /**
     * Language status (added/not added)
     *
     * @var integer
     *
     * @ORM\Column (type="boolean")
     */
    protected $added = false;

    /**
     * Language state (enabled/disabled)
     *
     * @var integer
     *
     * @ORM\Column (type="boolean")
     */
    protected $enabled = false;

    /**
     * Related module (Author\Name)
     *
     * @var string
     *
     * @ORM\Column (type="string", nullable=true)
     */
    protected $module;

    /**
     * Countries
     *
     * @var \XLite\Model\Country[]
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\Country", mappedBy="language")
     */
    protected $countries;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\LanguageTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * Get module
     *
     * @return string
     */
    public function getValidModule()
    {
        $module = $this->getModule();

        return ($module && Manager::getRegistry()->isModuleEnabled($module)) ? $module : null;
    }

    /**
     * Return true if current language is set as a default for customer interface
     *
     * @return boolean
     */
    public function getDefaultCustomer()
    {
        return $this->getCode() == \XLite\Core\Config::getInstance()->General->default_language;
    }

    /**
     * Return true if current language is set as a default for admin interface
     *
     * @return boolean
     */
    public function getDefaultAdmin()
    {
        return $this->getCode() == \XLite\Core\Config::getInstance()->General->default_admin_language;
    }

    /**
     * Return true if current language is set as a default for customer interface
     *
     * @return boolean
     */
    public function getDefaultAuth()
    {
        return (!\XLite\Core\Auth::getInstance()->isAdmin() && $this->getDefaultCustomer())
        || (\XLite\Core\Auth::getInstance()->isAdmin() && $this->getDefaultAdmin());
    }

    /**
     * Get flag URL
     *
     * @return string
     */
    public function getFlagURL()
    {
        return \XLite\Core\Layout::getInstance()->getResourceWebPath(
            $this->getFlagFile(),
            \XLite\Core\Layout::WEB_PATH_OUTPUT_URL,
            \XLite::INTERFACE_WEB,
            \XLite::ZONE_CUSTOMER
        );
    }

    /**
     * Get flag URL
     *
     * @return string
     */
    public function getFlagFile()
    {
        $code = $this->getLanguageToFlagCodeMapping()[$this->getCode()] ?? $this->getCode();

        $file = "images/flags_svg/{$code}.svg";
        $path = \XLite\Core\Layout::getInstance()->getResourceFullPath(
            $file,
            \XLite::INTERFACE_WEB,
            \XLite::ZONE_COMMON
        );

        return $path
            ? $file
            : 'images/flags_svg/__.svg';
    }

    /**
     * @return array
     */
    protected function getLanguageToFlagCodeMapping()
    {
        return [
            'be' => 'by',
            'ja' => 'jp',
            'ko' => 'kr',
            'hy' => 'am',
            'ar' => 'sa',
            'el' => 'gr',
            'he' => 'il',
            'kr' => 'ng',
            'sv' => 'se',
            'sl' => 'si',
            'si' => 'lk',
            'et' => 'ee',
            'ee' => 'gh',
            'aa' => 'et',
            'km' => 'kh',
            'ms' => 'my',
            'my' => 'mm',
        ];
    }

    /**
     * Remove all label translations to the language
     * Return true on success
     *
     * @return boolean
     */
    public function removeTranslations()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\LanguageLabel')->deleteTranslations($this->getCode());
    }

    /**
     * Get default language code
     *
     * @return string
     */
    protected function getSessionLanguageCode()
    {
        return $this->getCode();
    }

    /**
     * Get lng_id
     *
     * @return integer
     */
    public function getLngId()
    {
        return $this->lng_id;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Language
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
     * Set code3
     *
     * @param string $code3
     * @return Language
     */
    public function setCode3($code3)
    {
        $this->code3 = $code3;

        return $this;
    }

    /**
     * Get code3
     *
     * @return string
     */
    public function getCode3()
    {
        return $this->code3;
    }

    /**
     * Set r2l
     *
     * @param boolean $r2l
     * @return Language
     */
    public function setR2l($r2l)
    {
        $this->r2l = $r2l;

        return $this;
    }

    /**
     * Get r2l
     *
     * @return boolean
     */
    public function getR2l()
    {
        return $this->r2l;
    }

    /**
     * Set added
     *
     * @param boolean $added
     * @return Language
     */
    public function setAdded($added)
    {
        $this->added = $added;

        return $this;
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
     * Set enabled
     *
     * @param boolean $enabled
     * @return Language
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
     * Set module
     *
     * @param string $module
     * @return Language
     */
    public function setModule($module)
    {
        $this->module = $module;
        return $this;
    }

    /**
     * Get module
     *
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Set Countries
     *
     * @param \XLite\Model\Country[] $countries
     *
     * @return $this
     */
    public function setCountries($countries)
    {
        foreach ($countries as $country) {
            $country->setLanguage($this);
        }

        $this->countries = $countries;

        return $this;
    }

    /**
     * Get countries
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCountries()
    {
        return $this->countries;
    }
}
