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
 * Match directive - Pattern matching (switch/case)
 *
 * Syntax: @djson match variable
 *         @djson switch variable
 */
class MatchDirective implements DirectiveInterface
{
    private const DIRECTIVE_CASE = '@djson case';
    private const DIRECTIVE_DEFAULT = '@djson default';
    private const PATTERN_MATCH = '/^@djson (match|switch)\s+.+/';
    private const PATTERN_PARSE = '/^@djson (?:match|switch)\s+(.+)$/';

    /**
     * Check if directive matches the given key
     *
     * @param string $key Directive key
     * @return bool True if matches
     */
    public function matches(string $key): bool
    {
        // Supports both "match" and "switch"
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
        // Pattern: @djson match variable or @djson switch variable
        if (preg_match(self::PATTERN_PARSE, $key, $matches)) {
            return [
                'expression' => trim($matches[1])
            ];
        }

        return [];
    }

    /**
     * Process the match/switch directive
     *
     * @param array $params Parsed parameters
     * @param mixed $value Template value
     * @param array $context Data context
     * @param \Qoliber\DJson\DJson $processor Main processor
     * @return mixed Processed result from matching case
     */
    public function process(array $params, mixed $value, array $context, \Qoliber\DJson\DJson $processor): mixed
    {
        $expression = $params['expression'];

        // Evaluate the expression to get the value to match against
        $matchValue = $processor->getValue($expression, $context);

        // Process the cases
        if (!is_array($value)) {
            return null;
        }

        $defaultValue = null;
        $matchFound = false;
        $result = [];

        foreach ($value as $caseKey => $caseValue) {
            if (!is_string($caseKey)) {
                continue;
            }

            // Check for default case
            if ($caseKey === self::DIRECTIVE_DEFAULT) {
                $defaultValue = $caseValue;
                continue;
            }

            // Check for case directive
            if (str_starts_with($caseKey, self::DIRECTIVE_CASE)) {
                // Extract case value: "@djson case admin" -> "admin"
                $casePattern = substr($caseKey, strlen(self::DIRECTIVE_CASE));
                $casePattern = trim($casePattern);

                // Check if it matches
                if ($this->matchesCase($matchValue, $casePattern, $context, $processor)) {
                    $matchFound = true;
                    $processed = $processor->processNode($caseValue, $context);

                    if (is_array($processed)) {
                        $result = array_merge($result, $processed);
                    } else {
                        return $processed;
                    }

                    // Stop after first match (like PHP match)
                    break;
                }
            }
        }

        // If no match found, use default
        if (!$matchFound && $defaultValue !== null) {
            $processed = $processor->processNode($defaultValue, $context);

            if (is_array($processed)) {
                $result = array_merge($result, $processed);
            } else {
                return $processed;
            }
        }

        return empty($result) ? null : $result;
    }

    /**
     * Check if the value matches the case pattern
     *
     * @param mixed $value Value to match
     * @param string $pattern Pattern to match against
     * @param array $context Data context
     * @param \Qoliber\DJson\DJson $processor Main processor
     * @return bool True if matches
     */
    private function matchesCase(mixed $value, string $pattern, array $context, \Qoliber\DJson\DJson $processor): bool
    {
        // Remove quotes if present
        $pattern = trim($pattern);

        if ((str_starts_with($pattern, '"') && str_ends_with($pattern, '"')) ||
            (str_starts_with($pattern, "'") && str_ends_with($pattern, "'"))) {
            $pattern = substr($pattern, 1, -1);
        }

        // Direct comparison
        return $value == $pattern;
    }
}
