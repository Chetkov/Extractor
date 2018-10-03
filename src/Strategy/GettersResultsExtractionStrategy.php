<?php

namespace Chetkov\Extractor\Strategy;

use Chetkov\Extractor\Specification\MethodIsGetterSpecification;

/**
 * Class GettersResultsExtractionStrategy
 * @package Chetkov\Extractor\Strategy
 */
class GettersResultsExtractionStrategy extends AbstractExtractionStrategy
{
    /**
     * @var MethodIsGetterSpecification
     */
    private $methodIsGetterSpecification;

    /**
     * GettersResultsExtractionStrategy constructor.
     * @param array $extractableClasses
     * @param MethodIsGetterSpecification $methodIsGetter
     * @param bool $isNeedExtractInheritedMethods
     */
    public function __construct(
        array $extractableClasses,
        MethodIsGetterSpecification $methodIsGetter,
        bool $isNeedExtractInheritedMethods = false
    ) {
        parent::__construct($extractableClasses, $isNeedExtractInheritedMethods);
        $this->methodIsGetterSpecification = $methodIsGetter;
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

            if (!$this->methodIsGetterSpecification->isSatisfiedBy($method)) {
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
