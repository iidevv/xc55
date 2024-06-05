<?php


namespace Qualiteam\SkinActOrderMessaging\Model;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;
use Doctrine\ORM\Mapping as ORM;


/**
 * Message
 * @Extender\Mixin
 */
class Message extends \XC\VendorMessages\Model\Message
{

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="Qualiteam\SkinActOrderMessaging\Model\MessageImage", mappedBy="message", cascade={"all"})
     * @ORM\OrderBy   ({"id" = "ASC"})
     */
    protected $images;


    public function addImages($image)
    {
        $this->images[] = $image;

        return $this;
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages()
    {
        return $this->images;
    }

    public function __construct(array $data = [])
    {
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Mark as unread
     *
     * @param \XLite\Model\Profile $profile Profile OPTIONAL
     *
     * @return false|\XC\VendorMessages\Model\MessageRead
     */
    public function markAsUnread(\XLite\Model\Profile $profile = null)
    {
        $result = false;
        $profile = $profile ?: \XLite\Core\Auth::getInstance()->getProfile();

        $read = Database::getRepo('\XC\VendorMessages\Model\MessageRead')
            ->findOneBy(['message' => $this->getId(), 'reader' => $profile->getProfileId()]);

        if ($read) {
            Database::getEM()->remove($read);
            Database::getEM()->flush();
        }

        return $result;
    }
}