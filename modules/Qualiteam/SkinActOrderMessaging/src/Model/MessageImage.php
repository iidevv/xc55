<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActOrderMessaging\Model;


use Doctrine\ORM\Mapping as ORM;

/**
 * Content images file storage
 *
 * @ORM\Entity
 * @ORM\Table  (name="message_images")
 */
class MessageImage extends \XLite\Model\Base\Image
{

    /**
     * Relation to a product entity
     *
     * @var \XC\VendorMessages\Model\Message
     *
     * @ORM\ManyToOne  (targetEntity="XC\VendorMessages\Model\Message", inversedBy="images")
     * @ORM\JoinColumn (name="message_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $message;

    /**
     * @return \XC\VendorMessages\Model\Message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param \XC\VendorMessages\Model\Message $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

//    public function getAllowedFileSystemRoots()
//    {
//        $list[] = LC_DIR_IMAGES;
//
//        return $list;
//    }
//
//    public function getStorageName()
//    {
//        return 'message_image';
//    }
//
//    public function getStoreFileSystemRoot()
//    {
//        return LC_DIR_IMAGES . $this->getStorageName() . LC_DS;
//    }
//
//    public function getFileSystemRoot()
//    {
//        return LC_DIR_IMAGES . $this->getStorageName() . LC_DS;
//    }

    public function loadFromLocalFile($path, $basename = null, $makeUnique = false)
    {
        return parent::loadFromLocalFile($path, $basename, true);
    }
}
