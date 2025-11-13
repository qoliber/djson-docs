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
 * Set directive - Variable assignment and calculations
 *
 * Syntax: @djson set variableName = expression
 */
class SetDirective implements DirectiveInterface
{
    private const PATTERN_MATCH = '/^@djson set\s+\w+\s*=\s*.+/';
    private const PATTERN_PARSE = '/^@djson set\s+(\w+)\s*=\s*(.+)$/';

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
        // Pattern: @djson set variableName = expression
        if (preg_match(self::PATTERN_PARSE, $key, $matches)) {
            return [
                'variable' => trim($matches[1]),
                'expression' => trim($matches[2])
            ];
        }

        return [];
    }

    /**
     * Process the set directive
     *
     * @param array $params Parsed parameters
     * @param mixed $value Template value
     * @param array $context Data context
     * @param \Qoliber\DJson\DJson $processor Main processor
     * @return mixed Processed result with new variable in context
     */
    public function process(array $params, mixed $value, array $context, \Qoliber\DJson\DJson $processor): mixed
    {
        $variableName = $params['variable'];
        $expression = $params['expression'];

        // Evaluate the expression
        $computedValue = $this->evaluateExpression($expression, $context, $processor);

        // Add computed value to context for subsequent processing
        $newContext = array_merge($context, [$variableName => $computedValue]);

        // Process the value with the new context
        return $processor->processNode($value, $newContext);
    }

    /**
     * Evaluate expression for computed values
     *
     * @param string $expression Expression to evaluate
     * @param array $context Data context
     * @param \Qoliber\DJson\DJson $processor Main processor
     * @return mixed Evaluated result
     */
    private function evaluateExpression(string $expression, array $context, \Qoliber\DJson\DJson $processor): mixed
    {
        $expression = trim($expression);

        // Check for ternary operator first: condition ? true : false
        if (str_contains($expression, '?') && str_contains($expression, ':')) {
            $parts = explode('?', $expression, 2);
            if (count($parts) === 2) {
                $condition = trim($parts[0]);
                $values = trim($parts[1]);

                $valueParts = explode(':', $values, 2);
                if (count($valueParts) === 2) {
                    $trueValue = trim($valueParts[0]);
                    $falseValue = trim($valueParts[1]);

                    // Evaluate condition using DJson's evaluateExpression
                    $conditionResult = $processor->evaluateExpression($condition, $context);

                    // Get the appropriate value based on condition
                    $selectedValue = $conditionResult ? $trueValue : $falseValue;

                    // Resolve and return the selected value
                    return $this->resolveValue($selectedValue, $context, $processor);
                }
            }
        }

        // Try operators in order: *, /, +, - (respect precedence for *, /)
        // First handle * and /
        if (str_contains($expression, '*')) {
            $parts = $this->splitByOperator($expression, '*');
            if ($parts) {
                $left = $this->resolveValue($parts[0], $context, $processor);
                $right = $this->evaluateExpression($parts[1], $context, $processor);
                if (is_numeric($left) && is_numeric($right)) {
                    return $left * $right;
                }
            }
        }

        if (str_contains($expression, '/')) {
            $parts = $this->splitByOperator($expression, '/');
            if ($parts) {
                $left = $this->resolveValue($parts[0], $context, $processor);
                $right = $this->evaluateExpression($parts[1], $context, $processor);
                if (is_numeric($left) && is_numeric($right) && $right != 0) {
                    return $left / $right;
                }
                return 0;
            }
        }

        // Then handle + and -
        if (str_contains($expression, '+')) {
            $parts = $this->splitByOperator($expression, '+');
            if ($parts) {
                $left = $this->resolveValue($parts[0], $context, $processor);
                $right = $this->evaluateExpression($parts[1], $context, $processor);

                // String concatenation or numeric addition
                if (is_numeric($left) && is_numeric($right)) {
                    return $left + $right;
                }
                return $left . $right;
            }
        }

        if (str_contains($expression, '-')) {
            $parts = $this->splitByOperator($expression, '-');
            if ($parts) {
                $left = $this->resolveValue($parts[0], $context, $processor);
                $right = $this->evaluateExpression($parts[1], $context, $processor);
                if (is_numeric($left) && is_numeric($right)) {
                    return $left - $right;
                }
            }
        }

        // No operator found, just resolve the value
        return $this->resolveValue($expression, $context, $processor);
    }

    /**
     * Split expression by operator, respecting quoted strings
     *
     * @param string $expression Expression to split
     * @param string $operator Operator to split by
     * @return array|null Array with left and right parts, or null if not found
     */
    private function splitByOperator(string $expression, string $operator): ?array
    {
        $inQuotes = false;
        $quoteChar = null;
        $length = strlen($expression);

        for ($i = 0; $i < $length; $i++) {
            $char = $expression[$i];

            // Track quote state
            if (($char === '"' || $char === "'") && ($i === 0 || $expression[$i - 1] !== '\\')) {
                if (!$inQuotes) {
                    $inQuotes = true;
                    $quoteChar = $char;
                } elseif ($char === $quoteChar) {
                    $inQuotes = false;
                    $quoteChar = null;
                }
                continue;
            }

            // Find operator outside quotes
            if (!$inQuotes && $char === $operator) {
                return [
                    trim(substr($expression, 0, $i)),
                    trim(substr($expression, $i + 1))
                ];
            }
        }

        return null;
    }

    /**
     * Resolve a value (variable or literal)
     *
     * @param string $value Value to resolve
     * @param array $context Data context
     * @param \Qoliber\DJson\DJson $processor Main processor
     * @return mixed Resolved value
     */
    private function resolveValue(string $value, array $context, \Qoliber\DJson\DJson $processor): mixed
    {
        $value = trim($value);

        // Check for quoted strings
        if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
            (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
            return substr($value, 1, -1);
        }

        // Check for numbers
        if (is_numeric($value)) {
            return str_contains($value, '.') ? (float)$value : (int)$value;
        }

        // Check for booleans
        if ($value === 'true') {
            return true;
        }
        if ($value === 'false') {
            return false;
        }

        // Check for null
        if ($value === 'null') {
            return null;
        }

        // Otherwise, treat as variable path
        return $processor->getValue($value, $context);
    }
}
