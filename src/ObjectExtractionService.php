<?php

namespace Chetkov\Extractor;

use Chetkov\Extractor\Strategy\ExtractionStrategy;

/**
 * Class ObjectExtractionService
 * @package Chetkov\Extractor
 */
class ObjectExtractionService
{
    /**
     * @var ExtractionStrategy
     */
    private $extractionStrategy;

    /**
     * ObjectExtractionService constructor.
     * @param ExtractionStrategy $extractionStrategy
     */
    public function __construct(ExtractionStrategy $extractionStrategy)
    {
        $this->extractionStrategy = $extractionStrategy;
    }

    /**
     * @param $object
     * @return array
     */
    public function extract($object): array
    {
        return $this->extractionStrategy->extract($object);
    }
}
