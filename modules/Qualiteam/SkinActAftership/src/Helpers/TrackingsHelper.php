<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Helpers;

use Qualiteam\SkinActAftership\Traits\AftershipTrait;
use XCart\Container;
use XLite\Core\Request;
use XLite\Core\Translation;

/**
 * Tracking helper
 */
class TrackingsHelper
{
    use AftershipTrait;

    /**
     * @var array
     */
    public static array $error = [];

    /**
     * Get title
     *
     * @return string
     */
    public static function getTitle(): string
    {
        return Translation::lbl('SkinActAftership order tracking');
    }

    /**
     * Get tracking info
     *
     * @return array
     */
    public static function getTrackings(): array
    {
        $self     = new TrackingsHelper();
        $response = $self->getTrackingData(__FUNCTION__);

        if ($self->hasResponseData($response)
            && isset($response['data']['tracking'])
        ) {

            if (!empty($response['data']['tracking']['expected_delivery'])) {
                $delivery_date                                     = strtotime($response['data']['tracking']['expected_delivery']);
                $response['data']['tracking']['expected_delivery'] = ($delivery_date)
                    ? date('j M (D)', $delivery_date)
                    : '';
            }
            foreach ($response['data']['tracking']['checkpoints'] as $k => $v) {
                $v['checkpoint_timestamp']                       = strtotime($v['checkpoint_time']);
                $response['data']['tracking']['checkpoints'][$k] = $v;
            }
            $response['data']['tracking']['checkpoints'] = array_reverse($response['data']['tracking']['checkpoints']);

            return $response['data']['tracking'];
        }

        self::$error = $response;

        return [];
    }

    /**
     * @param string $id
     *
     * @return array
     */
    protected function getTrackingData(string $id): array
    {
        $slug           = $this->getSlug();
        $trackingNumber = $this->getTrackingNumber();
        $containerName  = $this->getAftershipPrefix() . ucfirst($id);

        $tracking = Container::getContainer()->get($containerName);
        $tracking->setTrackingNumber($trackingNumber);
        $tracking->setSlug($slug);

        return $tracking->getData();
    }

    /**
     * Get slug
     *
     * @return string|null
     */
    public static function getSlug(): ?string
    {
        return Request::getInstance()->slug ?? '';
    }

    /**
     * Get tracking number
     *
     * @return string|null
     */
    public static function getTrackingNumber(): ?string
    {
        return Request::getInstance()->trackNumber ?? '';
    }

    /**
     * Is response has any data
     *
     * @param array $response
     *
     * @return bool
     */
    protected function hasResponseData(array $response): bool
    {
        return !empty($response)
            && isset($response['data']);
    }

    /**
     * @return array
     */
    public static function getError(): array
    {
        return json_decode(self::$error['message'], true) ?? [];
    }

    /**
     * Get info for carriers
     *
     * @return array
     */
    public static function postCouriersDetect(): array
    {
        $self     = new TrackingsHelper();
        $response = $self->getTrackingData(__FUNCTION__);

        return $self->hasResponseData($response)
        && isset($response['data']['couriers'])
            ? array_shift($response['data']['couriers']) ?? []
            : [];
    }

    /**
     * Create a new aftership tracking number
     *
     * @param string $trackingNumber
     * @param string $slug
     *
     * @return array
     */
    public static function addAftershipTracking(string $trackingNumber, string $slug = ''): array
    {
        $aftership = Container::getContainer()->get('aftershipPostTrackings');
        $aftership->setTrackingNumber($trackingNumber);

        if (!empty($slug)) {
            $aftership->setSlug($slug);
        }

        return $aftership->getData();
    }

    /**
     * If aftership has result
     *
     * @param array $result
     *
     * @return bool
     */
    public static function hasAftershipResult(array $result): bool
    {
        return $result
            && $result['data']
            && $result['data']['tracking']
            && $result['data']['tracking']['id'];
    }
}