<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Core\Endpoints;

use Includes\Utils\Converter;

class Constructor
{
    /**
     * @var array
     */
    public array $body = [];

    /**
     * Get api body
     *
     * @return array
     */
    public function getBody(): array
    {
        return $this->body;
    }

    /**
     * Set api body
     *
     * @param array $value
     *
     * @return void
     */
    public function setBody(array $value): void
    {
        $this->body = $value;
    }

    /**
     * @param string                 $param
     * @param string|int|float|array $value
     *
     * @return void
     */
    public function addParam(string $param, string|int|float|array $value): void
    {
        $this->addParams([
            $param => $value,
        ]);
    }

    /**
     * Add multiple tracking params
     *
     * @param array $param
     *
     * @return void
     */
    public function addParams(array $param): void
    {
        $this->setBody(
            array_merge($this->getBody(), $param)
        );
    }

    /**
     * Build a set methods
     *
     * @param object $class
     *
     * @return void
     */
    public function build(object $class): void
    {
        $methods = get_class_methods($class);

        foreach ($methods as $method) {

            if (str_contains($method, 'set')) {
                $class->{$method}();
            }
        }
    }
}