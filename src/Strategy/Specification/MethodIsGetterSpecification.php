<?php

namespace Chetkov\Extractor\Strategy\Specification;

/**
 * Class MethodIsGetterSpecification
 * @package Chetkov\Extractor\Strategy\Specification
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
