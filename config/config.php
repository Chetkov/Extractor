<?php

use Chetkov\Extractor\Strategy\GettersResultsExtractionStrategy;

return [
    // Общие настройки всех стратегий
    'strategy' => GettersResultsExtractionStrategy::class,  // Стратегия извлечения по умолчанию
    'extractable_classes' => [],                            // Здесь перечисляем классы, объекты которых нужно обрабатывать. Пустой массив означает, что извлечению подвергнуться экземпляры любых классов
    'number_of_nesting_levels_to_check_in_tree' => 0,       // Кол-во родительских уровней в дереве уже извлеченных объектов проверяемое при защите от зацикливания типа: $parent->getChildren() --> $child->getParent() --> $parent->getChildren() ...
    'is_need_extract_inheritance' => false,                 // Извлекать данные из родительских элементов?

    // Настройки для стратегии PropertiesExtractionStrategy
    'is_need_extract_private_properties' => false,          // Извлекать данные из приватных свойств?
    'is_need_extract_protected_properties' => false,        // Извлекать данные из защищенных свойств?
    'is_need_extract_public_properties' => true,            // Извлекать данные из публичных свойств?
    'is_need_extract_static_properties' => false,           // Извлекать данные из статических свойств?
];