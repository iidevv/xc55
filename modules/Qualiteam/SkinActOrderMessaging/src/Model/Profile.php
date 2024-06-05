<?php


namespace Qualiteam\SkinActOrderMessaging\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Order
 * @Extender\Mixin
 */
class Profile extends \XLite\Model\Profile
{
    /**
     * Conversation
     *
     * @var \XC\VendorMessages\Model\Conversation
     *
     * @ORM\OneToOne (targetEntity="XC\VendorMessages\Model\Conversation", mappedBy="author", cascade={"remove"})
     */
    protected $generalConversation;

    /**
     * @return \XC\VendorMessages\Model\Conversation
     */
    public function getGeneralConversation()
    {
        return $this->generalConversation;
    }

    /**
     * @param \XC\VendorMessages\Model\Conversation $generalConversation
     */
    public function setGeneralConversation(\XC\VendorMessages\Model\Conversation $generalConversation)
    {
        $this->generalConversation = $generalConversation;
    }
}