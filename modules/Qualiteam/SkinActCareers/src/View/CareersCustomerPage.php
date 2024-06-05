<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCareers\View;

use XCart\Extender\Mapping\ListChild;
use XLite\Core\Converter;
use XLite\Core\Database;

/**
 *
 * @ListChild (list="center", zone="customer", weight="100")
 */
class CareersCustomerPage extends \XLite\View\AView
{
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActCareers/careers_page.css';
        return $list;
    }

    public static function getAllowedTargets()
    {
        return ['careers'];
    }

    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActCareers/careers_customer_page.twig';
    }

    protected function getCareers()
    {
        static $careers = null;

        if ($careers === null) {

            $careers = Database::getRepo('\Qualiteam\SkinActCareers\Model\Job')
                ->getCareersForCustomer();
        }

        return $careers;
    }

    protected function getCareerDate($career)
    {
        return Converter::formatDate($career->getPublicationDate());
    }

    protected function getCareerLocation($career)
    {
        return $this->buildURL('career', '', ['id' => $career->getId()]);
    }
}