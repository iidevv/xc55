<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActContactUsPage\View;

use XLite\Core\Config;

class CompanyShowroomInfo extends \XLite\View\AView
{

    /**
     * @inheritdoc
     */
    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActContactUsPage/contact_us/company_info.twig';
    }

    public function getAboutUsBlockContent() {
        return Config::getInstance()->Qualiteam->SkinActContactUsPage->about_us_content;
    }

    public function getShowroomsBlockContent() {
        return Config::getInstance()->Qualiteam->SkinActContactUsPage->showrooms_content;
    }

    public function getFirstShowroomGoogleMapCode() {
        return Config::getInstance()->Qualiteam->SkinActContactUsPage->showroom1_gmap_code;
    }

    public function getSecondShowroomGoogleMapCode() {
        return Config::getInstance()->Qualiteam->SkinActContactUsPage->showroom2_gmap_code;
    }
}