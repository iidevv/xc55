<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Base;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use XLite\Core\Database;

/**
 * Translation-owner abstract class
 *
 * @ORM\MappedSuperclass
 */
abstract class I18n extends \XLite\Model\AEntity
{
    /**
     * Current entity language
     *
     * @var string
     */
    protected $editLanguage;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $translations;

    /**
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    // {{{ Base Getters / setters

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $name
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setName($name)
    {
        return $this->setTranslationField(__FUNCTION__, $name);
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $code
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setCode($code)
    {
        return $this->setTranslationField(__FUNCTION__, $code);
    }

    /**
     * @return integer
     */
    public function getLabelId()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    // }}}

    /**
     * @param string $method
     *
     * @return string
     */
    protected function getTranslationField($method)
    {
        return $this->getSoftTranslation()->$method();
    }

    /**
     * @param string $method
     * @param string $value
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setTranslationField($method, $value)
    {
        $translation = $this->getTranslation();

        if (!$this->hasTranslation($translation->getCode())) {
            $this->addTranslations($translation);
        }

        return $translation->$method($value);
    }

    /**
     * Set current entity language
     *
     * @param string $code Code to set
     *
     * @return self
     */
    public function setEditLanguage($code)
    {
        $this->editLanguage = $code;

        return $this;
    }

    /**
     * Return all translations
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * Add translation to the list
     *
     * @param \XLite\Model\Base\Translation $translation Translation to add
     *
     * @return void
     */
    public function addTranslations(\XLite\Model\Base\Translation $translation)
    {
        $this->translations[] = $translation;
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
        $result = $this->getHardTranslation($code);

        if (!isset($result) && !$allowEmptyResult) {
            $class = \Doctrine\Common\Util\ClassUtils::getClass($this) . 'Translation';

            $result = new $class();
            $result->setOwner($this);
            $result->setCode($this->getTranslationCode($code));
        }

        return $result;
    }

    /**
     * Search for translation
     *
     * @param string $code Language code OPTIONAL
     *
     * @return \XLite\Model\Base\Translation
     */
    public function getHardTranslation($code = null)
    {
        $result = \Includes\Utils\ArrayManager::searchInObjectsArray(
            $this->getTranslations()->toArray(),
            'getCode',
            $this->getTranslationCode($code)
        );

        return $result;
    }

    /**
     * Get translation in safe mode
     *
     * @param string $code Language code OPTIONAL
     *
     * @return \XLite\Model\Base\Translation
     */
    public function getSoftTranslation($code = null)
    {
        $result = null;

        // Select by languages query (current language -> default language -> hardcoded default language)
        $query = \XLite\Core\Translation::getLanguageQuery($this->getTranslationCode($code));
        foreach ($query as $code) {
            $result = $this->getTranslation($code, true);
            if (isset($result)) {
                break;
            }
        }

        // Get first translation
        if (!isset($result)) {
            $result = $this->getTranslations()->first() ?: null;
        }

        // Get empty dump translation with specified code
        if (!isset($result)) {
            $result = $this->getTranslation(array_shift($query));
        }

        return $result;
    }

    /**
     * Check for translation
     *
     * @param string $code Language code OPTIONAL
     *
     * @return boolean
     */
    public function hasTranslation($code = null)
    {
        return (bool) $this->getHardTranslation($code);
    }

    /**
     * Get translation codes
     *
     * @return array
     */
    public function getTranslationCodes()
    {
        return \Includes\Utils\ArrayManager::getObjectsArrayFieldValues($this->getTranslations()->toArray(), 'getCode');
    }

    /**
     * Detach self
     *
     * @return void
     */
    public function detach()
    {
        parent::detach();

        foreach ($this->getTranslations() as $translation) {
            $translation->detach();
        }
    }

    /**
     * Clone
     *
     * @return static
     */
    public function cloneEntity()
    {
        /** @var static $entity */
        $entity = parent::cloneEntity();

        foreach ($entity->getSoftTranslation()->getRepository()->findBy(['owner' => $this]) as $translation) {
            $newTranslation = $translation->cloneEntity();
            $newTranslation->setOwner($entity);
            $entity->addTranslations($newTranslation);
            Database::getEM()->persist($newTranslation);
        }

        return $entity;
    }

    /**
     * Return current translation code
     *
     * @param string $code Language code OPTIONAL
     *
     * @return string
     */
    protected function getTranslationCode($code = null)
    {
        if (!isset($code)) {
            if ($this->editLanguage) {
                $code = $this->editLanguage;
            } elseif (\XLite\API\Language::getInstance()->getLanguageCode()) {
                $code = \XLite\API\Language::getInstance()->getLanguageCode();
            } elseif (\XLite\Logic\Export\Generator::getLanguageCode()) {
                $code = \XLite\Logic\Export\Generator::getLanguageCode();
            } elseif (\XLite\Logic\Import\Importer::getLanguageCode()) {
                $code = \XLite\Logic\Import\Importer::getLanguageCode();
            } elseif (\XLite\Core\Translation::getTmpTranslationCode()) {
                $code = \XLite\Core\Translation::getTmpTranslationCode();
            } else {
                $code = $this->getSessionLanguageCode();
            }
        }

        return $code;
    }

    /**
     * Get default language code
     *
     * @return string
     */
    protected function getSessionLanguageCode()
    {
        $lng = \XLite\Core\Session::getInstance()->getLanguage();
        return $lng ? $lng->getCode() : 'en';
    }

    /**
     *
     */
    public function explicitlyLoadTranslations()
    {
        // We need to make this manually on serialization because after deserialization
        // initialize() call will not work as expected.
        // Probably because of custom metadata implementation
        if ($this->translations instanceof PersistentCollection) {
            // Force load this lazy piece of data
            $this->translations->initialize();
        }
    }
}
