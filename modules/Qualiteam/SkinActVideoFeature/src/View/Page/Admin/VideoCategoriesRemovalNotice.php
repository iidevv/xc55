<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\View\Page\Admin;

use Qualiteam\SkinActVideoFeature\View\ItemsList\Model\VideoCategories as VideoCategoriesItemsListModel;

class VideoCategoriesRemovalNotice extends \XLite\View\AView
{
    /**
     * @inheritdoc
     */
    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActVideoFeature/page/video_category/removal_notice_popup.twig';
    }

    /**
     * @inheritdoc
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActVideoFeature/page/video_category/removal_notice_popup.less';

        return $list;
    }

    /**
     * Return link to video list
     *
     * @return string
     */
    protected function getNoCategoryVideosLink()
    {
        return $this->buildURL('educational_videos', '', [
            'categoryId' => 'no_category'
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function finalizeTemplateDisplay($template, array $profilerData)
    {
        parent::finalizeTemplateDisplay($template, $profilerData);

        \XLite\Core\Session::getInstance()->{VideoCategoriesItemsListModel::IS_DISPLAY_REMOVAL_NOTICE} = false;
    }
}