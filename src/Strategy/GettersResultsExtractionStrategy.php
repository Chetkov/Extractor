<?php

namespace Chetkov\Extractor\Strategy;

use Chetkov\Extractor\Strategy\Specification\MethodIsGetterSpecification;
use Chetkov\Extractor\Strategy\Specification\ObjectCanBeExtractedSpecification;

/**
 * Class GettersResultsExtractionStrategy
 * @package Chetkov\Extractor\Strategy
 */
class GettersResultsExtractionStrategy extends AbstractExtractionStrategy
{
    /**
     * @var MethodIsGetterSpecification
     */
    private $methodIsGetter;

    /**
     * GettersResultsExtractionStrategy constructor.
     * @param ObjectCanBeExtractedSpecification $objectCanBeExtracted
     * @param MethodIsGetterSpecification $methodIsGetter
     * @param bool $isNeedExtractInheritedMethods
     */
    public function __construct(
        ObjectCanBeExtractedSpecification $objectCanBeExtracted,
        MethodIsGetterSpecification $methodIsGetter,
        bool $isNeedExtractInheritedMethods = false
    ) {
        parent::__construct($objectCanBeExtracted, $isNeedExtractInheritedMethods);
        $this->methodIsGetter = $methodIsGetter;
    }

    /**
     * @param $object
     * @return array
     */
    public function extract($object): array
    {
        $result = [];

        $reflectionClass = new \ReflectionClass($object);
        $methods = $reflectionClass->getMethods();
        foreach ($methods as $method) {
            $this->setObjectToTree($object);

            if (!$this->isNeedExtractInheritance) {
                $isMethodInherited = $reflectionClass->getName() !== $method->getDeclaringClass()->getName();
                if ($isMethodInherited) {
                    continue;
                }
            }

            if (!$this->methodIsGetter->isSatisfiedBy($method)) {
                continue;
            }

            try {
                $value = $object->{$method->getName()}();
                $name = substr($method->getName(), 3);
                $result[$name] = $this->prepareValue($value);
            } catch (\Exception $e) {
                if ($e->getCode() !== self::EXTRACTING_IS_NOT_ALLOWED) {
                    throw new $e;
                }
            }
        }

        return $result;
    }
}
