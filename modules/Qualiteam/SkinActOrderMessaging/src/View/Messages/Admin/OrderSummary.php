<?php


namespace Qualiteam\SkinActOrderMessaging\View\Messages\Admin;

/**
 * Write message
 **/
class OrderSummary extends \XLite\View\AView
{
    /**
     * @inheritdoc
     */
    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActOrderMessaging/order_messages/order_summary.twig';
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActOrderMessaging/order_messages/style.less';

        return $list;
    }
}