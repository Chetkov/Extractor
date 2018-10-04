<?php

namespace Chetkov\Extractor;

use Chetkov\Extractor\Strategy\Specification\MethodIsGetterSpecification;
use Chetkov\Extractor\Strategy\Specification\ObjectCanBeExtractedSpecification;
use Chetkov\Extractor\Strategy\ExtractionStrategy;
use Chetkov\Extractor\Strategy\GettersResultsExtractionStrategy;
use Chetkov\Extractor\Strategy\PropertiesExtractionStrategy;

/**
 * Class ObjectExtractionServiceFactory
 * @package Chetkov\Extractor
 */
class ObjectExtractionServiceFactory
{
    /**
     * @param array $config
     * @return ObjectExtractionService
     * @throws \ReflectionException
     */
    public function create(array $config): ObjectExtractionService
    {
        $this->validateConfig($config);
        return new ObjectExtractionService($this->createStrategy($config));
    }

    /**
     * @param array $config
     * @throws \ReflectionException
     */
    private function validateConfig(array $config): void
    {
        if (!isset($config['strategy'])) {
            throw new \RuntimeException('Не определена стратегия извлечения');
        }

        if (!class_exists($config['strategy'])) {
            throw new \RuntimeException("Стратегия [{$config['strategy']}] не существует");
        }

        $reflectionStrategy = new \ReflectionClass($config['strategy']);
        if (!$reflectionStrategy->implementsInterface(ExtractionStrategy::class)) {
            throw new \RuntimeException("Стратегия [{$config['strategy']}] должна реализовывать интерфейс [Chetkov\Extractor\Strategy\ExtractionStrategy]");
        }
    }

    /**
     * @param array $config
     * @return ExtractionStrategy
     */
    private function createStrategy(array $config): ExtractionStrategy
    {
        $objectCanBeExtractedSpecification = new ObjectCanBeExtractedSpecification(
            $config['extractable_classes'] ?? [],
            $config['number_of_nesting_levels_to_check_in_tree'] ?? 0
        );

        switch ($config['strategy']) {
            case PropertiesExtractionStrategy::class:
                $strategy = new PropertiesExtractionStrategy(
                    $objectCanBeExtractedSpecification,
                    $config['is_need_extract_private_properties'] ?? false,
                    $config['is_need_extract_protected_properties'] ?? false,
                    $config['is_need_extract_public_properties'] ?? true,
                    $config['is_need_extract_static_properties'] ?? false,
                    $config['is_need_extract_inheritance'] ?? false
                );
                break;
            case GettersResultsExtractionStrategy::class:
                $strategy = new GettersResultsExtractionStrategy(
                    $objectCanBeExtractedSpecification,
                    new MethodIsGetterSpecification(),
                    $config['is_need_extract_inheritance'] ?? false
                );
                break;
            default:
                throw new \RuntimeException("Передана не поддерживаемая в данный момент стратегия извлечения [{$config['strategy']}]");
        }

        return $strategy;
    }
}
