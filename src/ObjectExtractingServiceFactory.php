<?php

namespace Chetkov\Extractor;


class ObjectExtractingServiceFactory
{
    /**
     * @param array $config
     * @return ObjectExtractionService
     */
    public static function create(array $config): ObjectExtractionService
    {

        return new ObjectExtractionService($extractingStrategy);
    }
}
