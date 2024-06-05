<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FileAttachments\Model\Product;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product attachment's translations
 *
 * @ORM\Entity
 * @ORM\Table (
 *     name="product_attachment_translations",
 *     indexes={
 *         @ORM\Index (name="ci", columns={"code","id"}),
 *         @ORM\Index (name="id", columns={"id"})
 *     }
 * )
 */
class AttachmentTranslation extends \XLite\Model\Base\Translation
{
    // {{{ Collumns

    /**
     * @var string
     *
     * @ORM\Column (type="string", length=128)
     */
    protected $title = '';

    /**
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $description = '';

    /**
     * @var \CDev\FileAttachments\Model\Product\Attachment
     *
     * @ORM\ManyToOne (targetEntity="CDev\FileAttachments\Model\Product\Attachment", inversedBy="translations")
     * @ORM\JoinColumn (name="id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $owner;

    // }}}

    /**
     * Set title
     *
     * @param string $title
     * @return AttachmentTranslation
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return AttachmentTranslation
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get label_id
     *
     * @return integer
     */
    public function getLabelId()
    {
        return $this->label_id;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return AttachmentTranslation
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
}
