<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Base;

use Doctrine\ORM\Mapping as ORM;

/**
 * Abstract address model
 *
 * @ORM\MappedSuperclass
 */
abstract class PersonalAddress extends \XLite\Model\Base\Address
{
    /**
     * Get full name
     *
     * @return string
     */
    public function getName()
    {
        return trim($this->getFirstname() . ' ' . $this->getLastname());
    }

    /**
     * Set full name
     *
     * @param string $value Full name
     *
     * @return string
     */
    public function setName($value)
    {
        $parts = array_map('trim', explode(' ', trim($value), 2));

        $this->setFirstname($parts[0]);
        $this->setLastname($parts[1] ?? '');
    }
}
