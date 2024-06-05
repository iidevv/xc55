<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Framework\Doctrine\Common\Annotations;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Annotations\Reader as ReaderInterface;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

class Reader implements ReaderInterface
{
    protected ReaderInterface $inner;

    /**
     * @var string[]
     */
    protected array $classNames;

    public function __construct(
        ReaderInterface $inner,
        array $classNames
    ) {
        $this->inner = $inner;
        $this->classNames = $classNames;
    }

    /**
     * @param ReflectionClass $class
     *
     * @return object[]
     */
    public function getClassAnnotations(ReflectionClass $class): array
    {
        return $this->inner->getClassAnnotations($class);
    }

    /**
     * @param ReflectionClass $class
     * @param class-string<T> $annotationName
     *
     * @return T|null
     *
     * @template T
     */
    public function getClassAnnotation(ReflectionClass $class, $annotationName)
    {
        if (!$this->isMergeParents($annotationName)) {
            return $this->inner->getClassAnnotation($class, $annotationName);
        }

        $summarizedAnnotation = null;
        $annotations = $this->getClassAnnotationParents($class, $annotationName);
        $annotations = array_reverse($annotations);
        foreach ($annotations as $annotation) {
            if (!$summarizedAnnotation) {
                $summarizedAnnotation = $annotation;
                continue;
            }

            $summarizedAnnotation = $this->mergeClassAnnotation($summarizedAnnotation, $annotation);
        }

        if ($summarizedAnnotation) {
            $this->postProcess($class->getName(), $summarizedAnnotation);
        }

        return $summarizedAnnotation;
    }

    private function postProcess(string $name, ApiResource $summarizedAnnotation): void
    {
        // 'get_by_number' only for XLite\Model\Order
        if ($name === 'XLite\Model\Cart') {
            unset($summarizedAnnotation->itemOperations['get_by_number']);
        }
    }

    /**
     * @param ReflectionMethod $method
     *
     * @return object[]
     */
    public function getMethodAnnotations(ReflectionMethod $method): array
    {
        return $this->inner->getMethodAnnotations($method);
    }

    /**
     * @param ReflectionMethod $method
     * @param class-string<T>  $annotationName
     *
     * @return T|null
     *
     * @template T
     */
    public function getMethodAnnotation(ReflectionMethod $method, $annotationName)
    {
        return $this->inner->getMethodAnnotation($method, $annotationName);
    }

    /**
     * @param ReflectionProperty $property
     *
     * @return object[]
     */
    public function getPropertyAnnotations(ReflectionProperty $property): array
    {
        return $this->inner->getPropertyAnnotations($property);
    }

    /**
     * @param ReflectionProperty $property
     * @param class-string<T>    $annotationName
     *
     * @return T|null
     *
     * @template T
     */
    public function getPropertyAnnotation(ReflectionProperty $property, $annotationName)
    {
        return $this->inner->getPropertyAnnotation($property, $annotationName);
    }

    protected function isMergeParents(string $annotationName): bool
    {
        return in_array($annotationName, $this->classNames, true);
    }

    protected function mergeClassAnnotation(object $previous, object $current): object
    {
        if ($previous instanceof ApiResource && $current instanceof ApiResource) {
            return $this->mergeApiResourceAnnotation($previous, $current);
        }

        throw new \InvalidArgumentException(
            sprintf('Cannot merge object "%s" and "%s"', get_class($previous), get_class($current))
        );
    }

    protected function getClassAnnotationParents(ReflectionClass $class, $annotationName): array
    {
        $annotations = [];

        do {
            $annotation = $this->inner->getClassAnnotation($class, $annotationName);
            if ($annotation) {
                $annotations[$class->getName()] = $annotation;
            }
            $class = $class->getParentClass() ?: null;
        } while ($class);

        return $annotations;
    }

    protected function mergeApiResourceAnnotation(ApiResource $previous, ApiResource $current): ApiResource
    {
        return new ApiResource(
            $current->description ?: $previous->description,
            $this->mergeArray($previous->collectionOperations, $current->collectionOperations),
            $this->mergeArray($previous->graphql, $current->graphql),
            $current->iri ?: $previous->iri,
            $this->mergeArray($previous->itemOperations, $current->itemOperations),
            $current->shortName ?: $previous->shortName,
            $this->mergeArray($previous->subresourceOperations, $current->subresourceOperations),
            $this->mergeArray($previous->attributes, $current->attributes),
        );
    }

    protected function mergeArray(&$array1, &$array2): ?array
    {
        if ($array1 === null) {
            return $array2;
        }

        if ($array2 === null) {
            return $array1;
        }

        $merged = $array1;

        foreach ($array2 as $key => $value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = $this->mergeArray($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }
}
