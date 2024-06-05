<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller;

class TitleFromController
{
    /**
     * @var string
     */
    private $target;

    /**
     * @param string $target
     */
    public function __construct(string $target)
    {
        $this->target = $target;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $controller = $this->getControllerForTarget();

        if (in_array("getTitle", get_class_methods($controller))) {
            return (string) $controller->getTitle();
        }

        $class = get_class($controller);

        throw new \RuntimeException("TitleFromController: controller can't return title: {$this->target}, $class");
    }

    /**
     * @return AController
     */
    protected function getControllerForTarget(): AController
    {
        $class = \XLite\Core\Converter::getControllerClass($this->target);

        if (!$class) {
            throw new \RuntimeException("TitleFromController: controller not found, unknown target: {$this->target}");
        }

        return new $class();
    }
}
