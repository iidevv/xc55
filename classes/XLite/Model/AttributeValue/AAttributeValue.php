<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\AttributeValue;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
abstract class AAttributeValue extends \XLite\Model\Base\I18n
{
    /**
     * Rate type codes
     */
    public const TYPE_ABSOLUTE = 'a';
    public const TYPE_PERCENT  = 'p';

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column (type="integer", options={"unsigned": true})
     */
    protected $id;

    /**
     * @var \XLite\Model\Attribute
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\Attribute")
     * @ORM\JoinColumn (name="attribute_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $attribute;

    /**
     * Return attribute value as string
     *
     * @return string
     */
    abstract public function asString();

    /**
     * Return diff
     *
     * @param array $oldValues Old values
     * @param array $newValues New values
     *
     * @return array
     * @todo: add test
     */
    public static function getDiff(array $oldValues, array $newValues)
    {
        $diff = [];
        if ($newValues) {
            foreach ($newValues as $attributeId => $attributeValues) {
                $changed = false;
                $changes = [
                    'deleted' => [],
                    'added'   => [],
                    'changed' => [],
                ];

                foreach ($attributeValues as $id => $value) {
                    if (!isset($oldValues[$attributeId][$id])) {
                        $changed               = true;
                        $changes['added'][$id] = $value;
                    } else {
                        $c = [];
                        foreach ($value as $k => $v) {
                            if ($v != $oldValues[$attributeId][$id][$k]) {
                                $c[$k] = $v;
                            }
                        }
                        if ($c) {
                            $changed                 = true;
                            $changes['changed'][$id] = $c;
                        }
                    }
                }

                if (!empty($oldValues[$attributeId])) {
                    foreach ($oldValues[$attributeId] as $id => $value) {
                        if (!isset($newValues[$attributeId][$id])) {
                            $changed              = true;
                            $changes['deleted'][] = $id;
                        }
                    }
                }

                if ($changed) {
                    $diff[$attributeId] = $changes;
                }
            }
        }

        return $diff;
    }

    /**
     * Clone
     *
     * @return static
     */
    public function cloneEntity()
    {
        /** @var static $newEntity */
        $newEntity = parent::cloneEntity();
        $newEntity->setAttribute($this->getAttribute());

        return $newEntity;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return (int) $this->id;
    }

    /**
     * @return \XLite\Model\Attribute
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * @param \XLite\Model\Attribute $attribute
     */
    public function setAttribute(\XLite\Model\Attribute $attribute = null)
    {
        $this->attribute = $attribute;
    }
}
