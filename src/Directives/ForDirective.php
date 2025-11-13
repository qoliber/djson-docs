<?php

/**
 * DJson - Dynamic JSON Templating Library
 *
 * @package   Qoliber\DJson
 * @author    Jakub Winkler <jwinkler@qoliber.com>
 * @copyright 2024 Qoliber
 * @license   MIT
 * @link      https://github.com/qoliber/djson
 */

declare(strict_types=1);

namespace Qoliber\DJson\Directives;

use Qoliber\DJson\DirectiveInterface;
use Qoliber\DJson\DJson;

/**
 * For directive - Loops over collections
 *
 * Syntax: @djson for items as item
 */
class ForDirective implements DirectiveInterface
{
    private const PATTERN_MATCH = '/^@djson for\s+.+\s+as\s+\w+/';
    private const PATTERN_PARSE = '/^@djson for\s+(.+?)\s+as\s+(\w+)$/';

    /**
     * Check if directive matches the given key
     *
     * @param string $key Directive key
     * @return bool True if matches
     */
    public function matches(string $key): bool
    {
        return preg_match(self::PATTERN_MATCH, $key) === 1;
    }

    /**
     * Parse directive parameters
     *
     * @param string $key Directive key
     * @return array Parsed parameters
     */
    public function parse(string $key): array
    {
        // Pattern: @djson for items as item
        if (preg_match(self::PATTERN_PARSE, $key, $matches)) {
            return [
                'collection' => trim($matches[1]),
                'variable' => trim($matches[2])
            ];
        }

        return [];
    }

    /**
     * Process the for loop directive
     *
     * @param array $params Parsed parameters
     * @param mixed $value Template value
     * @param array $context Data context
     * @param \Qoliber\DJson\DJson $processor Main processor
     * @return mixed Processed result
     */
    public function process(array $params, mixed $value, array $context, \Qoliber\DJson\DJson $processor): mixed
    {
        $collectionPath = $params['collection'];
        $variableName = $params['variable'];

        // Get the collection to iterate over
        $items = $processor->getValue($collectionPath, $context);

        if (!is_array($items)) {
            return [];
        }

        $result = [];
        foreach ($items as $index => $item) {
            // Create new context with loop variable
            $loopContext = array_merge($context, [
                $variableName => $item,
                '_index' => $index,
                '_key' => $index,
                '_first' => $index === array_key_first($items),
                '_last' => $index === array_key_last($items)
            ]);

            $processedItem = $processor->processNode($value, $loopContext);
            if ($processedItem !== null) {
                $result[] = $processedItem;
            }
        }

        return $result;
    }
}
