<?php

namespace Chetkov\Extractor\Strategy;

use Chetkov\Extractor\Composite\TreeElement;
use Chetkov\Extractor\Specification\ObjectCanBeExtractedSpecification;

/**
 * Class AbstractExtractionStrategy
 * @package Chetkov\Extractor\Strategy
 */
abstract class AbstractExtractionStrategy implements ExtractionStrategy
{
    protected const EXTRACTING_IS_NOT_ALLOWED = 1;

    /**
     * @var bool
     */
    protected $isNeedExtractInheritance;

    /**
     * @var TreeElement
     */
    protected $currentElementOfExtractedObjectsTree;

    /**
     * @var ObjectCanBeExtractedSpecification
     */
    private $objectCanBeExtracted;

    /**
     * AbstractExtractionStrategy constructor.
     * @param ObjectCanBeExtractedSpecification $objectCanBeExtracted
     * @param bool $isNeedExtractInheritance
     */
    public function __construct(ObjectCanBeExtractedSpecification $objectCanBeExtracted, bool $isNeedExtractInheritance = false)
    {
        $this->isNeedExtractInheritance = $isNeedExtractInheritance;
        $this->objectCanBeExtracted = $objectCanBeExtracted;
    }

    /**
     * @param $value
     * @return mixed
     */
    protected function prepareValue($value)
    {
        if ($value instanceof \stdClass) {
            $value = json_decode(json_encode($value), true);
        }

        if (is_array($value)) {
            foreach ($value as $key => $item) {
                try {
                    $value[$key] = $this->prepareValue($item);
                } catch (\Exception $e) {
                    if ($e->getCode() !== self::EXTRACTING_IS_NOT_ALLOWED) {
                        throw new $e;
                    }
                    unset($value[$key]);
                }
            }
        }

        if (is_object($value)) {
            if (!$this->objectCanBeExtracted->isSatisfiedBy($value, $this->currentElementOfExtractedObjectsTree)) {
                throw new \RuntimeException('Value is object of not extractable class', self::EXTRACTING_IS_NOT_ALLOWED);
            }

            $value = $this->extract($value);
            $this->currentElementOfExtractedObjectsTree = $this->currentElementOfExtractedObjectsTree->getParent();
        }

        return $value;
    }

    /**
     * @param $object
     * @return void
     */
    protected function setObjectToTree($object): void
    {
        if (!$this->currentElementOfExtractedObjectsTree) {
            $this->currentElementOfExtractedObjectsTree = new TreeElement($object);
        }

        if (!$this->currentElementOfExtractedObjectsTree->isCurrentElementValue($object)) {
            $existingElement = $this->currentElementOfExtractedObjectsTree->findByUniqueValue($object);
            if ($existingElement) {
                $this->currentElementOfExtractedObjectsTree = $existingElement;
            } else {
                $child = new TreeElement($object);
                $this->currentElementOfExtractedObjectsTree->addChild($child);
                $this->currentElementOfExtractedObjectsTree = $child;
            }
        }
    }
}
