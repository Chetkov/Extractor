<?php

namespace Chetkov\Extractor\Strategy;

use Chetkov\Extractor\Strategy\Specification\ObjectCanBeExtractedSpecification;

/**
 * Class PropertiesExtractionStrategy
 * @package Chetkov\Extractor\Strategy
 */
class PropertiesExtractionStrategy extends AbstractExtractionStrategy
{
    /**
     * @var array
     */
    private $extractableAccessModifiers = [];

    /**
     * PropertiesExtractionStrategy constructor.
     * @param ObjectCanBeExtractedSpecification $objectCanBeExtracted
     * @param bool $isNeedExtractPrivateProperties
     * @param bool $isNeedExtractProtectedProperties
     * @param bool $isNeedExtractPublicProperties
     * @param bool $isNeedExtractStaticProperties
     * @param bool $isNeedExtractInheritedProperties
     */
    public function __construct(
        ObjectCanBeExtractedSpecification $objectCanBeExtracted,
        bool $isNeedExtractPrivateProperties = false,
        bool $isNeedExtractProtectedProperties = false,
        bool $isNeedExtractPublicProperties = true,
        bool $isNeedExtractStaticProperties = false,
        bool $isNeedExtractInheritedProperties = false
    ) {
        parent::__construct($objectCanBeExtracted, $isNeedExtractInheritedProperties);

        if ($isNeedExtractPrivateProperties) {
            $this->extractableAccessModifiers[] = \ReflectionProperty::IS_PRIVATE;
        }

        if ($isNeedExtractProtectedProperties) {
            $this->extractableAccessModifiers[] = \ReflectionProperty::IS_PROTECTED;
        }

        if ($isNeedExtractPublicProperties) {
            $this->extractableAccessModifiers[] = \ReflectionProperty::IS_PUBLIC;
        }

        if ($isNeedExtractStaticProperties) {
            $this->extractableAccessModifiers[] = \ReflectionProperty::IS_STATIC;
        }
    }

    /**
     * @param $object
     * @return array
     * @throws \ReflectionException
     */
    public function extract($object): array
    {
        $result = [];

        $reflectionClass = new \ReflectionClass($object);
        foreach ($this->extractableAccessModifiers as $accessModifier) {
            $result[] = $this->extractProperties($reflectionClass, $reflectionClass->getProperties($accessModifier), $object);
        }

        return array_merge(...$result);
    }

    /**
     * @param \ReflectionClass $reflectionClass
     * @param \ReflectionProperty[] $properties
     * @param $object
     * @return array
     */
    private function extractProperties(\ReflectionClass $reflectionClass, array $properties, $object): array
    {
        $result = [];

        foreach ($properties as $property) {
            $this->setObjectToTree($object);

            if (!$this->isNeedExtractInheritance) {
                $isPropertyInherited = $property->getDeclaringClass()->getName() !== $reflectionClass->getName();
                if ($isPropertyInherited) {
                    continue;
                }
            }

            $isPublicProperty = $property->isPublic();
            if (!$isPublicProperty) {
                $property->setAccessible(true);
            }

            try {
                $value = $property->getValue($object);
                $result[$property->getName()] = $this->prepareValue($value);
            } catch (\Exception $e) {
                if ($e->getCode() !== self::EXTRACTING_IS_NOT_ALLOWED) {
                    throw $e;
                }
            }

            if (!$isPublicProperty) {
                $property->setAccessible(false);
            }
        }

        return $result;
    }
}
