<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCareers\View;

use XCart\Extender\Mapping\ListChild;
use XLite\Core\Converter;
use XLite\Core\Database;
use XLite\Core\Request;

/**
 *
 * @ListChild (list="center", zone="customer", weight="100")
 */
class OneCareerCustomerPage extends \XLite\View\AView
{

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActCareers/one_career_page.css';
        return $list;
    }


    protected function getCareer()
    {
        static $career = null;

        if ($career === null) {

            $id = (int)Request::getInstance()->id;

            if ($id) {

                $career = Database::getRepo('\Qualiteam\SkinActCareers\Model\Job')
                    ->getOneCareerForCustomerById($id);

                if ($career) {
                    return $career;
                }
            }

        }

        return $career;
    }

    protected function getCareerFields()
    {
        static $result = null;

        if ($result === null) {

            $career = $this->getCareer();

            if (!$career) {
                $result = [];
                return $result;
            }

            // The Job Details Page should display the content of the fields: 'Job Title', 'Job Description', 'Job Compensation', Job Duties', 'Job Requirements', 'Job Employment Type', and 'Job Probation time'.

            $order = [
                // 'title' => static::t('SkinActCareers title'),
                'pageDescription' => static::t('SkinActCareers Description'),
                'compensation' => static::t('SkinActCareers compensation'),
                'duties' => static::t('SkinActCareers duties'),
                'requirements' => static::t('SkinActCareers requirements'),
                'employmentType' => static::t('SkinActCareers employmentType'),
                'probationTime' => static::t('SkinActCareers probationTime'),
                // 'publicationDate' => static::t('SkinActCareers publicationDate'),
            ];

            $fields = [];

            foreach ($order as $filedName => $label) {

                $methodName = 'get' . ucfirst($filedName);

                if (method_exists($career, $methodName)) {
                    $content = $career->{$methodName}();

                    $str = htmlentities(strip_tags($content));
                    $str = preg_replace('/(?:\s|&nbsp;)+/', '', $str, -1);

                    if ($content && $str) {
                        if ($filedName === 'publicationDate') {
                            $content = Converter::formatDate($content);
                        }
                        $fields[] = [
                            'name' => $filedName,
                            'content' => $content,
                            'label' => $label
                        ];
                    }
                }

            }

            $result = $fields;
            return $result;
        }

        return $result;
    }

    protected function isVisible()
    {
        return $this->getCareerFields() && parent::isVisible();
    }

    public static function getAllowedTargets()
    {
        return ['career'];
    }

    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActCareers/one_career_customer_page.twig';
    }

    protected function getApplyNowLocation()
    {
        return $this->buildURL('interview_questions', '', [
            'jid' => $this->getCareer() ? $this->getCareer()->getId() : 0
        ]);
    }

}