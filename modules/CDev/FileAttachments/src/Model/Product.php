<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FileAttachments\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;
use CDev\FileAttachments\Model\Product\Attachment;

/**
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Product
{
    /**
     * Product attachments
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="CDev\FileAttachments\Model\Product\Attachment", mappedBy="product", cascade={"all"})
     * @ORM\OrderBy   ({"orderby" = "ASC"})
     */
    protected $attachments;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     *
     * @return void
     */
    public function __construct(array $data = [])
    {
        $this->attachments = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Clone
     *
     * @return \XLite\Model\AEntity
     */
    public function cloneEntity()
    {
        $newProduct = parent::cloneEntity();

        foreach ($this->getAttachments() as $attachment) {
            $attachment->cloneEntityForProduct($newProduct);
        }

        $newProduct->update(true);

        return $newProduct;
    }

    /**
     * Add attachments
     *
     * @param \CDev\FileAttachments\Model\Product\Attachment $attachments
     * @return Product
     */
    public function addAttachments(\CDev\FileAttachments\Model\Product\Attachment $attachments)
    {
        $this->attachments[] = $attachments;
        return $this;
    }

    /**
     * Get attachments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * Return filtered attachments
     *
     * @param \XLite\Model\Profile $profile OPTIONAL
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFilteredAttachments($profile = null)
    {
        return $this->getAttachments()->filter($this->getAttachmentsFilter($profile));
    }

    /**
     * Returns image comparing closure
     *
     * @param \XLite\Model\Profile $profile OPTIONAL
     *
     * @return \Closure
     */
    protected function getAttachmentsFilter($profile = null)
    {
        /**
         * @param Attachment $element
         *
         * @return boolean
         */
        return static function ($element) use ($profile) {
            if ($element->getAccess() === Attachment::ACCESS_ANY) {
                return true;
            } elseif ($element->getAccess() === Attachment::ACCESS_REGISTERED) {
                return $profile !== null;
            }

            $membershipId = ($profile && $profile->getMembership())
                ? $profile->getMembership()->getMembershipId()
                : null;

            return (int)$element->getAccess() === $membershipId;
        };
    }
}
