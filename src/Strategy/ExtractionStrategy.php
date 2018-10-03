<?php

namespace Chetkov\Extractor\Strategy;

/**
 * Interface ExtractionStrategy
 * @package Chetkov\Extractor\Strategy
 */
interface ExtractionStrategy
{
    /**
     * @param $object
     * @return array
     */
    public function extract($object): array;
}
