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

namespace Qoliber\DJson;

/**
 * Interface for directive implementations
 *
 * Defines the contract for creating custom directives in DJson templates.
 */
interface DirectiveInterface
{
    /**
     * Check if this directive matches the given key
     *
     * @param string $key Directive key to check
     * @return bool True if matches
     */
    public function matches(string $key): bool;

    /**
     * Parse the directive key and extract parameters
     * Returns array with directive parameters
     *
     * @param string $key Directive key to parse
     * @return array Parsed parameters
     */
    public function parse(string $key): array;

    /**
     * Process the directive with given value and context
     *
     * @param array $params Parsed parameters from the directive key
     * @param mixed $value The value associated with the directive key
     * @param array $context The data context
     * @param \Qoliber\DJson\DJson $processor The main processor instance
     * @return mixed Processed result
     */
    public function process(array $params, mixed $value, array $context, \Qoliber\DJson\DJson $processor): mixed;
}
