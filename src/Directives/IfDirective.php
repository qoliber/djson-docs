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
 * If directive - Conditional inclusion
 *
 * Syntax: @djson if condition
 */
class IfDirective implements DirectiveInterface
{
    private const PATTERN_MATCH = '/^@djson if\s+.+/';
    private const PATTERN_WITH_KEY = '/^@djson if\s+(.+?)\s+as\s+(\w+)$/';
    private const PATTERN_SIMPLE = '/^@djson if\s+(.+)$/';

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
        // Pattern: @djson if condition
        // or: @djson if condition as keyName
        if (preg_match(self::PATTERN_WITH_KEY, $key, $matches)) {
            return [
                'condition' => trim($matches[1]),
                'key' => trim($matches[2])
            ];
        }

        if (preg_match(self::PATTERN_SIMPLE, $key, $matches)) {
            return [
                'condition' => trim($matches[1]),
                'key' => null
            ];
        }

        return [];
    }

    /**
     * Process the if directive
     *
     * @param array $params Parsed parameters
     * @param mixed $value Template value
     * @param array $context Data context
     * @param \Qoliber\DJson\DJson $processor Main processor
     * @return mixed Processed result or null if condition fails
     */
    public function process(array $params, mixed $value, array $context, \Qoliber\DJson\DJson $processor): mixed
    {
        $condition = $params['condition'];
        $key = $params['key'] ?? null;

        if ($processor->evaluateExpression($condition, $context)) {
            // If key is specified and value is simple, return as key-value pair
            if ($key !== null && !is_array($value)) {
                return [$key => $processor->processNode($value, $context)];
            }

            // Otherwise process and merge the content
            return $processor->processNode($value, $context);
        }

        return null;
    }
}
