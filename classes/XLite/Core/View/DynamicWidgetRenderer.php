<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\View;

use Includes\Utils\ConfigParser;
use XLite\View\AView;

class DynamicWidgetRenderer
{
    /**
     * Dynamic widgets render this special placeholder value (along with widget class and parameters) to be later replaced with the actual widget content.
     */
    public const DYNAMIC_WIDGET_PLACEHOLDER = '__dynamic_widget_placeholder__';

    /** @var WidgetParamsSerializer */
    protected $widgetParamsSerializer;

    public function __construct(WidgetParamsSerializer $widgetParamsSerializer)
    {
        $this->widgetParamsSerializer = $widgetParamsSerializer;
    }

    /**
     * Generate placeholder value with the necessary information needed to reify this widget later (class name and widget params)
     *
     * @param AView|DynamicWidgetInterface $widget
     *
     * @return string
     *
     * @throws WidgetParamsSerializationException
     */
    public function getWidgetPlaceholder(DynamicWidgetInterface $widget)
    {
        try {
            $placeholderData = serialize([
                'class'  => get_class($widget),
                'params' => $this->widgetParamsSerializer->serialize($widget->getWidgetParams()),
            ]);
            $placeholderData = $this->sign($placeholderData, ConfigParser::getOptions(['installer_details', 'shared_secret_key']));
            $placeholderData = base64_encode($placeholderData);
        } catch (WidgetParamsSerializationException $e) {
            throw new WidgetParamsSerializationException($e->getMessage() . ' (' . get_class($widget) . ')');
        }

        $placeholderDataLen = strlen($placeholderData);

        return self::DYNAMIC_WIDGET_PLACEHOLDER . $placeholderDataLen . '_' . $placeholderData;
    }

    /**
     * Replace all placeholders in the source string with rendered widget contents
     *
     * @param AView $parent
     * @param       $content
     *
     * @return string
     */
    public function reifyWidgetPlaceholders(AView $parent, $content)
    {
        $replacedContent = '';

        $placeholder = self::DYNAMIC_WIDGET_PLACEHOLDER;
        $pos         = 0;

        while (($placeholderPos = strpos($content, $placeholder, $pos)) !== false) {
            $replacedContent .= substr($content, $pos, $placeholderPos - $pos);
            $pos = $placeholderPos;

            $pos += strlen($placeholder);

            $length = '';

            while (is_numeric($content[$pos])) {
                $length .= $content[$pos++];
            }

            $length = (int)$length;

            $pos++; // skip '_'

            $serialized = substr($content, $pos, $length);
            $serialized = base64_decode($serialized);
            $serialized = $this->verify($serialized, ConfigParser::getOptions(['installer_details', 'shared_secret_key']));

            $pos += $length;

            $placeholderData = unserialize($serialized);

            $widgetParams = $this->widgetParamsSerializer->unserialize($placeholderData['params']);

            $widget = $parent->getChildWidget($placeholderData['class'], $widgetParams);

            $replacedContent .= $widget->getContent();
        }

        $replacedContent .= substr($content, $pos);

        return $replacedContent;
    }

    protected function sign(string $data, string $seed): string
    {
        $keypair = $this->getKeypair($seed);

        return sodium_crypto_sign($data, $keypair[0]);
    }

    protected function verify(string $data, string $seed): string
    {
        $keypair = $this->getKeypair($seed);

        return sodium_crypto_sign_open($data, $keypair[1]) ?: '';
    }

    protected function getKeypair(string $seed)
    {
        if (strlen($seed) > SODIUM_CRYPTO_SIGN_SEEDBYTES) {
            $seed = substr($seed, 0, SODIUM_CRYPTO_SIGN_SEEDBYTES);
        } elseif (strlen($seed) < SODIUM_CRYPTO_SIGN_SEEDBYTES) {
            $seed = str_pad($seed, SODIUM_CRYPTO_SIGN_SEEDBYTES, md5($seed));
        }

        $sign_pair = sodium_crypto_sign_seed_keypair($seed);

        return [sodium_crypto_sign_secretkey($sign_pair), sodium_crypto_sign_publickey($sign_pair)];
    }
}
