<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Base;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
abstract class Translation extends \XLite\Model\AEntity
{
    /**
     * Default code
     */
    public const DEFAULT_LANGUAGE = 'en';

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column (type="integer")
     */
    protected $label_id;

    /**
     * Label language code
     *
     * @var string
     *
     * @ORM\Column (type="string", options={ "fixed": true }, length=2)
     */
    protected $code = self::DEFAULT_LANGUAGE;

    /**
     * @var \XLite\Model\AEntity
     */
    protected $owner;

    /**
     * Return list of class properties which are not translated
     *
     * @return array
     */
    public static function getInternalProperties()
    {
        return ['label_id', 'code'];
    }

    /**
     * Return the owner object
     *
     * @return \XLite\Model\AEntity
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set Owner
     *
     * @param \XLite\Model\AEntity $owner
     *
     * @return $this
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Check cache after entity persist or remove
     *
     * @return void
     */
    public function checkCache()
    {
        parent::checkCache();

        // Check translation owner cache
        if ($this->getOwner()) {
            $this->getOwner()->checkCache();
        }
    }
}
