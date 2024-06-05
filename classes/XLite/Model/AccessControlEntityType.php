<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table (
 *     name="access_control_entity_types",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint (name="type", columns={"type"})
 *     },
 *     indexes={
 *         @ORM\Index (name="type", columns={"type"})
 *     }
 * )
 */
class AccessControlEntityType extends \XLite\Model\AEntity
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column (type="integer", options={"unsigned": true})
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column (type="string")
     */
    protected $type;

    /**
     * Return Id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set Id
     *
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Return type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param $type
     *
     * @return $this
     */
    public function setType($type)
    {
        if (strpos($type, '\\') !== 0) {
            $type = '\\' . $type;
        }

        $this->type = $type;
        return $this;
    }

    /**
     * Check if entity type is suitable with this type
     *
     * @param AEntity $entity
     *
     * @return mixed
     */
    public function checkType(\XLite\Model\AEntity $entity)
    {
        $type = $this->getType();

        return $entity instanceof $type;
    }

    /**
     * Check if string equal with type
     *
     * @param string $type
     *
     * @return mixed
     */
    public function checkStringType($type)
    {
        if (strpos($type, '\\') !== 0) {
            $type = '\\' . $type;
        }

        return $type === $this->getType();
    }
}
