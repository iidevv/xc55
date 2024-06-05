<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Qualiteam\SkinActVerifiedCustomer\View\Model\Profile;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;
use XLite\Core\Request;


/**
 * @Extender\Mixin
 */
class AdminMain extends \XLite\View\Model\Profile\AdminMain
{
    public const SECTION_VERIFICATION = 'verification_information';

    protected $verificationSchema = [
        'verificationStatus' => [
            self::SCHEMA_CLASS => '\XLite\View\FormField\Select\Regular',
            self::SCHEMA_LABEL => 'SkinActVerifiedCustomer Verification status',
        ],
        'files' => [
            self::SCHEMA_CLASS => '\Qualiteam\SkinActVerifiedCustomer\View\FormField\FileUploader\FileUploader',
            self::SCHEMA_LABEL => 'SkinActVerifiedCustomer Verification attachments',
        ]

    ];

    public function __construct(array $params = [], array $sections = [])
    {
        parent::__construct($params, $sections);

        $this->verificationSchema['verificationStatus'][self::SCHEMA_OPTIONS] = [
            \Qualiteam\SkinActVerifiedCustomer\Model\VerificationInfo::STATUS_VERIFIED => static::t('SkinActVerifiedCustomer STATUS_VERIFIED'),
            \Qualiteam\SkinActVerifiedCustomer\Model\VerificationInfo::STATUS_NOT_VERIFIED => static::t('SkinActVerifiedCustomer STATUS_NOT_VERIFIED')
        ];
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActCareers/admin_main.css';
        return $list;
    }

    /**
     * Return list of the class-specific sections
     *
     * @return array
     */
    protected function getProfileMainSections()
    {
        if ($this->getModelObject() && !$this->getModelObject()->isAdmin()) {

            return parent::getProfileMainSections()
                + [
                    static::SECTION_VERIFICATION => 'SkinActVerifiedCustomer Verification information',
                ];
        }

        return parent::getProfileMainSections();
    }

    /**
     * Return fields list by the corresponding schema
     *
     * @return array
     */
    protected function getFormFieldsForSectionVerificationInformation()
    {
        return $this->getFieldsBySchema($this->verificationSchema);
    }


    public function getDefaultFieldValue($name)
    {
        $verificationInfo = $this->getModelObject()->getVerificationInfo();

        if ($name === 'verificationStatus') {
            return $verificationInfo ? $verificationInfo->getStatus() : \Qualiteam\SkinActVerifiedCustomer\Model\VerificationInfo::STATUS_NOT_VERIFIED;
        }

        if ($name === 'files') {
            return $verificationInfo ? $verificationInfo->getFiles()->toArray() : [];

        }

        return parent::getDefaultFieldValue($name);
    }

    protected function saveVerificationInfo()
    {
        $data = $this->getRequestData();

        if (isset($data['verificationStatus'])
            && in_array($data['verificationStatus'], [
                \Qualiteam\SkinActVerifiedCustomer\Model\VerificationInfo::STATUS_VERIFIED,
                \Qualiteam\SkinActVerifiedCustomer\Model\VerificationInfo::STATUS_NOT_VERIFIED], true)
        ) {

            $verificationInfo = $this->getModelObject()->getVerificationInfo();

            if (!$verificationInfo) {
                $verificationInfo = new \Qualiteam\SkinActVerifiedCustomer\Model\VerificationInfo();
                Database::getEM()->persist($verificationInfo);
                $this->getModelObject()->setVerificationInfo($verificationInfo);
                $verificationInfo->setProfile($this->getModelObject());
            }

            $verificationInfo->setStatus($data['verificationStatus']);

            $files = $data['files'];
            $files = is_array($files) ? $files : [];
            $verificationInfo->processFiles('files', $files);

        }

        Database::getEM()->flush();
    }

    protected function postprocessSuccessActionUpdate()
    {
        parent::postprocessSuccessActionUpdate();

        $this->saveVerificationInfo();
    }

    protected function postprocessSuccessActionCreate()
    {
        parent::postprocessSuccessActionCreate();

        $this->saveVerificationInfo();
    }
}