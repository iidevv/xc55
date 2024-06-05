<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// vim: set ts=4 sw=4 sts=4 et:

namespace Qualiteam\SkinActMagicImages\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Columns
 *
 * @ORM\Entity
 * @ORM\Table (name="magic360_columns",
 *      indexes={
 *          @ORM\Index (name="magic_swatches_set_id", columns={"magic_swatches_set_id"})
 *      }
 * )
 */
class Columns extends \XLite\Model\AEntity
{
    /**
     * Magic swatches set id
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $magic_swatches_set_id;

    /**
     * Columns
     *
     * @var integer
     *
     * @ORM\Column         (type="smallint", options={ "unsigned": true })
     */
    protected $columns = 0;

    /**
     * Get product id
     *
     * @return integer
     */
    public function getMagicSwatchesSetId()
    {
        return $this->magic_swatches_set_id;
    }

    /**
     * Set magic swatches set id
     *
     * @param integer $magic_swatches_set_id
     *
     * @return Columns
     */
    public function setMagicSwatchesSetId($magic_swatches_set_id)
    {
        $this->magic_swatches_set_id = $magic_swatches_set_id;

        return $this;
    }

    /**
     * Get columns
     *
     * @return integer
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Set columns
     *
     * @param integer $columns
     *
     * @return Columns
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;

        return $this;
    }
}
