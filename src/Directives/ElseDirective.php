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
 * Else directive - Else clause for conditionals
 *
 * Syntax: @djson else
 */
class ElseDirective implements DirectiveInterface
{
    private const DIRECTIVE_ELSE = '@djson else';

    /**
     * Check if directive matches the given key
     *
     * @param string $key Directive key
     * @return bool True if matches
     */
    public function matches(string $key): bool
    {
        return $key === self::DIRECTIVE_ELSE;
    }

    /**
     * Parse directive parameters
     *
     * @param string $key Directive key
     * @return array Parsed parameters (empty for else)
     */
    public function parse(string $key): array
    {
        return [];
    }

    /**
     * Process the else directive
     *
     * @param array $params Parsed parameters
     * @param mixed $value Template value
     * @param array $context Data context
     * @param \Qoliber\DJson\DJson $processor Main processor
     * @return mixed Processed result
     */
    public function process(array $params, mixed $value, array $context, \Qoliber\DJson\DJson $processor): mixed
    {
        // The actual logic is handled in DJson::processNode
        // This directive only processes if the previous if/unless was false
        return $processor->processNode($value, $context);
    }
}
