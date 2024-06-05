<?php


namespace Qualiteam\SkinActOrderMessaging\View\ItemsList;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Conversation extends \XC\VendorMessages\View\ItemsList\Conversation
{

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        if (\XLite::isAdminZone()) {
            $list[] = 'modules/Qualiteam/SkinActOrderMessaging/messages/style.less';
            $list[] = 'modules/Qualiteam/SkinActOrderMessaging/uploader.css';

        }else {
            // customer zone
            $list[] = 'modules/Qualiteam/SkinActOrderMessaging/uploader.css';
        }

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();

        if (\XLite::isAdminZone()) {
            $list[] = 'modules/Qualiteam/SkinActOrderMessaging/messages/markReadUnread.js';
            $list[] = 'modules/Qualiteam/SkinActOrderMessaging/admin_uploader.js';

        } else {
            // customer zone
            $list[] = 'modules/Qualiteam/SkinActOrderMessaging/uploader.js';
        }

        return $list;
    }

    /**
     * Mark messages as read
     *
     * @return integer
     */
    protected function markMessagesAsRead()
    {
        return \XLite::isAdminZone() ? false : parent::markMessagesAsRead();
    }

    /**
     * @inheritdoc
     */
    protected function getPageBodyTemplate()
    {
        return 'modules/Qualiteam/SkinActOrderMessaging/messages/body.twig';

//        return \XLite::isAdminZone()
//            ? 'modules/Qualiteam/SkinActOrderMessaging/messages/body.twig'
//            : 'modules/Qualiteam/SkinActOrderMessaging/messages/body.twig';
    }
}