<?php

namespace Chetkov\Extractor\Strategy\Specification;

use Chetkov\Extractor\Composite\TreeElement;

/**
 * Class ObjectCanBeExtractedSpecification
 * @package Chetkov\Extractor\Strategy\Specification
 */
class ObjectCanBeExtractedSpecification
{
    /**
     * @var array
     */
    private $extractableClasses;

    /**
     * @var int
     */
    private $numberOfNestingLevelsToCheckInTree;

    /**
     * ObjectCanBeExtractedSpecification constructor.
     * @param array $extractableClasses
     * @param int $numberOfNestingLevelsToCheckInTree
     */
    public function __construct(array $extractableClasses = [], int $numberOfNestingLevelsToCheckInTree = 0)
    {
        $this->extractableClasses = $extractableClasses;
        $this->numberOfNestingLevelsToCheckInTree = $numberOfNestingLevelsToCheckInTree;
    }

    /**
     * @param $object
     * @param TreeElement|null $currentElementOfExtractedObjectsTree
     * @return bool
     */
    public function isSatisfiedBy($object, TreeElement $currentElementOfExtractedObjectsTree = null): bool
    {
        $canBeExtracted = empty($this->extractableClasses);
        foreach ($this->extractableClasses as $extractableClass) {
            if ($object instanceof $extractableClass) {
                $canBeExtracted = true;
                break;
            }
        }

        if ($canBeExtracted && $currentElementOfExtractedObjectsTree) {
            $canBeExtracted = !$currentElementOfExtractedObjectsTree->isParentElementValue($object, $this->numberOfNestingLevelsToCheckInTree);
        }

        return $canBeExtracted;
    }
}
