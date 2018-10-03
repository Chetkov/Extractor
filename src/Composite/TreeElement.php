<?php

namespace Chetkov\Extractor\Composite;

/**
 * Class TreeElement
 * @package Chetkov\Extractor\Composite
 */
class TreeElement
{
    /**
     * Уникальное значение отличающее элемент от других
     * Может быть чем угодно
     * @var mixed
     */
    private $uniqueValue;

    /**
     * @var static|null
     */
    private $parent;

    /**
     * @var static[]
     */
    private $children = [];

    /**
     * TreeElement constructor.
     * @param $uniqueValue
     */
    public function __construct($uniqueValue)
    {
        $this->uniqueValue = $uniqueValue;
    }

    /**
     * @return static|null
     */
    public function getParent(): ?self
    {
        return $this->parent;
    }

    /**
     * @param TreeElement $parent
     * @return static
     */
    private function setParent(TreeElement $parent): self
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @param $uniqueValue
     * @param int $numberOfNestingLevelsToCheck
     * @return bool
     */
    public function isParentElementValue($uniqueValue, int $numberOfNestingLevelsToCheck = 1): bool
    {
        $parent = $this->getParent();
        if (!$parent) {
            return false;
        }

        if ($parent->isCurrentElementValue($uniqueValue)) {
            return true;
        }

        return $numberOfNestingLevelsToCheck
            ? $parent->isParentElementValue($uniqueValue, $numberOfNestingLevelsToCheck - 1)
            : false;
    }

    /**
     * @param $uniqueValue
     * @return bool
     */
    public function isCurrentElementValue($uniqueValue): bool
    {
        return $this->getUniqueValue() === $uniqueValue;
    }

    /**
     * @return mixed
     */
    private function getUniqueValue()
    {
        return $this->uniqueValue;
    }

    /**
     * @return static[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param TreeElement $child
     * @return static
     */
    public function addChild(TreeElement $child): self
    {
        $child->setParent($this);
        $this->children[] = $child;
        return $this;
    }

    /**
     * @return static
     */
    public function getRootElement(): self
    {
        $rootElement = $this;
        if ($this->getParent()) {
            $rootElement = $this->getParent()->getRootElement();
        }
        return $rootElement;
    }

    /**
     * @param $uniqueValue
     * @return static|null
     */
    public function findByUniqueValue($uniqueValue): ?self
    {
        return $this->getRootElement()->findChildBy('uniqueValue', $uniqueValue);
    }

    /**
     * @param string $field
     * @param $value
     * @return static|null
     */
    public function findBy(string $field, $value): ?self
    {
        return $this->getRootElement()->findChildBy($field, $value);
    }

    /**
     * @param string $field
     * @param $value
     * @return static|null
     */
    public function findChildBy(string $field, $value): ?self
    {
        $getterName = 'get' . ucfirst(strtolower($field));
        if (method_exists($this, $getterName) && $this->$getterName() === $value) {
            return $this;
        }

        foreach ($this->getChildren() as $child) {
            $result = $child->findChildBy($field, $value);
            if ($result) {
                return $result;
            }
        }

        return null;
    }
}
