<?php


namespace Qualiteam\SkinActOrderMessaging\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Autocomplete controller
 * @Extender\Mixin
 */
class Conversation extends \XC\VendorMessages\Model\Conversation
{
    /**
     * Author
     *
     * @var \XLite\Model\Profile
     *
     * @ORM\OneToOne (targetEntity="XLite\Model\Profile", inversedBy="generalConversation")
     * @ORM\JoinColumn (name="profile_id", referencedColumnName="profile_id", onDelete="CASCADE")
     */
    protected $author;

    /**
     * @return \XLite\Model\Profile
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param \XLite\Model\Profile $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

}