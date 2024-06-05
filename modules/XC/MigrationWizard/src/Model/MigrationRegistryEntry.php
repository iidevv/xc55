<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Migration Registry Entry
 *
 * @ORM\Entity
 * @ORM\Table  (name="migration_wizard_registry_entries",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint (name="RegistrySource", columns={"registryId", "sourceId"})
 *      },
 *      indexes={
 *          @ORM\Index (name="RegistrySourceResult", columns={"registryId", "sourceId", "resultId"})
 *      }
 * )
 */
class MigrationRegistryEntry extends \XLite\Model\AEntity
{
    /**
     * Entry unique ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $entryId;

    /**
     * Entry source ID
     *
     * @var string
     *
     * @ORM\Column (type="string", length=128, nullable=false)
     */
    protected $sourceId;

    /**
     * Entry result ID
     *
     * @var string
     *
     * @ORM\Column (type="string", length=128, nullable=false)
     */
    protected $resultId;

    /**
     * Relation to a registry entity
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToOne  (targetEntity="XC\MigrationWizard\Model\MigrationRegistry", inversedBy="entries")
     * @ORM\JoinColumn (name="registryId", referencedColumnName="registryId", onDelete="CASCADE")
     */
    protected $registry;

    /**
     * Returns unique ID
     *
     * @see \XLite\Model\AEntity->getUniqueIdentifier()
     *
     * @return integer
     */
    public function getEntryId()
    {
        return $this->entryId;
    }

    /**
     * @return string
     */
    public function getSourceId()
    {
        return $this->sourceId;
    }

    /**
     * @param string $sourceId
     */
    public function setSourceId($sourceId)
    {
        $this->sourceId = $sourceId;
    }

    /**
     * @return string
     */
    public function getResultId()
    {
        return $this->resultId;
    }

    /**
     * @param string $resultId
     */
    public function setResultId($resultId)
    {
        $this->resultId = $resultId;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getRegistry()
    {
        return $this->registry;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $registry
     */
    public function setRegistry($registry)
    {
        $this->registry = $registry;
    }
}
