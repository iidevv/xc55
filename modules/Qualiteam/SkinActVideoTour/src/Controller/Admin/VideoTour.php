<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoTour\Controller\Admin;

use Qualiteam\SkinActVideoTour\Helper\Youtube;
use Qualiteam\SkinActVideoTour\Model\VideoTours as VideoToursModel;
use Qualiteam\SkinActVideoTour\View\Model\VideoTour as VideoTourFormClass;
use XLite\Core\Auth;
use XLite\Core\Converter;
use XLite\Core\Database;
use XLite\Core\Request;
use XLite\Core\TopMessage;

/**
 * Class video tour
 */
class VideoTour extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL(): bool
    {
        return parent::checkACL()
            || Auth::getInstance()->isPermissionAllowed('manage catalog');
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle(): string
    {
        $label = $this->getVideoTour() && $this->getVideoTour()->isPersistent()
            ? static::t('SkinActVideoTour edit video tour')
            : static::t('SkinActVideoTour add video tour');

        return static::t($label);
    }

    /**
     * Get video tour model
     */
    public function getVideoTour(): VideoToursModel
    {
        $result = Database::getRepo(VideoToursModel::class)->find($this->getId());

        return $result ?: new VideoToursModel();
    }

    /**
     * Return id from request
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return (int) Request::getInstance()->id;
    }

    /**
     * Get product id
     *
     * @return int
     */
    public function getProductId(): int
    {
        return $this->getRequestTargetProductId();
    }

    /**
     * Modify model
     *
     * @return void
     * @throws \Exception
     */
    protected function doActionModify(): void
    {
        $videoTour = $this->prepareVideoTour();

        Database::getEM()->persist($videoTour);
        Database::getEM()->flush();

        $this->setReturnURL();
        $this->setHardRedirect();
    }

    /**
     * Prepare video tour
     *
     * @return VideoToursModel
     */
    protected function prepareVideoTour(): VideoToursModel
    {
        $model = $this->getVideoTour();
        $data  = $this->prepareRequestData();

        $model->setVideoUrl($data['video_url']);
        $model->setYoutubeId($data['youtube_id']);
        $model->setDescription($data['description']);
        $model->setProduct($data['product']);
        $model->setEnabled($data['enabled']);
        $model->setPosition($data['position']);

        return $model;
    }

    /**
     * Prepare request data
     *
     * @return array
     */
    protected function prepareRequestData(): array
    {
        $data = $this->getModelForm()->getRequestData();

        $data['product']    = $this->getRequestTargetProductId();
        $data['youtube_id'] = Youtube::getYoutubeVideoId($data['video_url']);

        return $data;
    }

    /**
     * Return product Id
     *
     * @return int
     */
    public function getRequestTargetProductId(): int
    {
        return (int) Request::getInstance()->target_product_id;
    }

    /**
     * Set return URL
     *
     * @param string $url Url OPTIONAL
     *
     * @return void
     */
    public function setReturnURL($url = ''): void
    {
        $url = Converter::buildURL(
            'product',
            '',
            ['product_id' => $this->getRequestTargetProductId(), 'page' => 'video_tour']
        );

        parent::setReturnURL($url);
    }

    /**
     * doActionDelete
     *
     * @return void
     * @throws \Exception
     */
    protected function doActionDelete(): void
    {
        $videoTour = $this->getVideoTour();

        Database::getEM()->remove($videoTour);
        Database::getEM()->flush();

        TopMessage::addInfo(
            static::t('SkinActVideoTour video has been deleted')
        );

        $this->setReturnURL();
        $this->setHardRedirect();
    }

    /**
     * Get model form class
     *
     * @return string
     */
    protected function getModelFormClass(): string
    {
        return VideoTourFormClass::class;
    }
}