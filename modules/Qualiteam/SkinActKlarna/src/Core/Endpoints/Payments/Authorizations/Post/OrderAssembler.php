<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Core\Endpoints\Payments\Authorizations\Post;

use Qualiteam\SkinActKlarna\Core\Endpoints\Assembler;
use Qualiteam\SkinActKlarna\Core\Endpoints\AssemblerInterface;
use Qualiteam\SkinActKlarna\Core\Endpoints\ConstructorInterface;

class OrderAssembler implements AssemblerInterface
{
    protected string $path = '';

    /**
     * @param \Qualiteam\SkinActKlarna\Core\Endpoints\ConstructorInterface $constructorOrder
     * @param \Qualiteam\SkinActKlarna\Core\Endpoints\Assembler            $assembler
     */
    public function __construct(
        private ConstructorInterface $constructorOrder,
        private Assembler $assembler,
    ) {
        $this->constructorOrder->build();
    }

    /**
     * @return array
     */
    public function getBody(): array
    {
        return $this->constructorOrder->getBody();
    }

    /**
     * Assembling a data to call endpoint
     */
    public function assemble(): void
    {
        $this->assembler->setBody(
            $this->getBody()
        );

        $this->assembler->setPath(
            $this->getPath()
        );

        $this->assembler->assemble();
    }

    /**
     * @return string
     */
    protected function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $value
     *
     * @return void
     */
    public function setPath(string $value): void
    {
        $this->path = $value;
    }
}