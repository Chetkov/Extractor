<?php

namespace Chetkov\Extractor\Specification;

/**
 * Class MethodIsGetterSpecification
 * @package Chetkov\Extractor\Specification
 */
class MethodIsGetterSpecification
{
    private const GETTER_PREFIX = 'get';

    /**
     * @param \ReflectionMethod $method
     * @return bool
     */
    public function isSatisfiedBy(\ReflectionMethod $method): bool
    {
        return $method->isPublic()
            && stripos($method->getName(), self::GETTER_PREFIX) === 0
            && $method->getNumberOfParameters() === 0;
    }
}
