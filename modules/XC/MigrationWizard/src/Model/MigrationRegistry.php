<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Migration Registry
 *
 * @ORM\Entity
 * @ORM\Table  (name="migration_wizard_registry",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint (name="Registry", columns={"name"})
 *      }
 * )
 */
class MigrationRegistry extends \XLite\Model\AEntity
{
    /**
     * Registry unique ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $registryId;

    /**
     * Regisrty name
     *
     * @var string
     *
     * @ORM\Column (type="string", length=128)
     */
    protected $name;

    /**
     * Relation to a registry entries
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany (targetEntity="XC\MigrationWizard\Model\MigrationRegistryEntry", mappedBy="registry", cascade={"all"})
     */
    protected $entries;

    /**
     * Returns unique ID
     *
     * @see \XLite\Model\AEntity->getUniqueIdentifier()
     *
     * @return integer
     */
    public function getRegistryId()
    {
        return $this->registryId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $entries
     */
    public function setEntries($entries)
    {
        $this->entries = $entries;
    }
}
