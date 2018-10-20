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
     * @param object|array $object
     * @return array
     */
    public function extract($object): array
    {
        if (null === $object) {
            return [];
        }

        if ($object instanceof \stdClass) {
            $object = json_decode(json_encode($object), true);
        }

        if ($object instanceof \ArrayAccess) {
            foreach ($object as $key => $value) {
                $object[$key] = $this->extract($value);
            }
        }

        if (is_object($object)) {
            $object = $this->extractionStrategy->extract($object);
        }

        return $object;
    }
}
