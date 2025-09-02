<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv\Schema;

use IteratorAggregate;
use Phithi92\TypedEnv\Types;
use Traversable;

/**
 * Represents the schema definition for environment variables.
 *
 * This class allows defining strongly typed rules for environment variables.
 * It implements {@see IteratorAggregate} so the schema can be iterated over
 * directly, returning key-rule pairs.
 *
 * @implements IteratorAggregate<string, KeyRule>
 */
final class Schema implements IteratorAggregate
{
    // Default delimiter for list values (comma-separated)
    private const LIST_DELIMITER = ',';

    // Default format for DateTime values (ISO 8601)
    private const DATETIME_FORMAT = 'c';

    // Default format for Date values (YYYY-MM-DD)
    private const DATE_FORMAT = 'Y-m-d';

    /** @var array<string, KeyRule> Collection of environment variable key rules */
    private array $keys = [];

    /**
     * Factory builder to create a new Schema instance.
     */
    public static function build(): self
    {
        return new self();
    }

    // ---- Typed entry points ------------------------------------------------

    /**
     * Define a string key rule.
     *
     * @param string $key Environment variable name
     */
    public function string(string $key): Types\StringKeyRule
    {
        return $this->keys[$key] = new Types\StringKeyRule($key);
    }

    /**
     * Define a boolean key rule.
     */
    public function bool(string $key): Types\BoolKeyRule
    {
        return $this->keys[$key] = new Types\BoolKeyRule($key);
    }

    /**
     * Define an integer key rule.
     */
    public function int(string $key): Types\IntKeyRule
    {
        return $this->keys[$key] = new Types\IntKeyRule($key);
    }

    /**
     * Define a float key rule.
     */
    public function float(string $key): Types\FloatKeyRule
    {
        return $this->keys[$key] = new Types\FloatKeyRule($key);
    }

    /**
     * Define a duration key rule.
     *
     * @param bool $returnInterval Whether to return a DateInterval instead of seconds
     * @param string $roundingMode How to round duration values ('floor', 'ceil', etc.)
     */
    public function duration(
        string $key,
        bool $returnInterval = false,
        string $roundingMode = 'floor'
    ): Types\DurationKeyRule {
        return $this->keys[$key] = new Types\DurationKeyRule($key, $returnInterval, $roundingMode);
    }

    /**
     * Define a JSON key rule.
     *
     * @param bool $assoc Whether to return associative arrays
     */
    public function json(string $key, bool $assoc = true): Types\JsonKeyRule
    {
        return $this->keys[$key] = new Types\JsonKeyRule($key, $assoc);
    }

    /**
     * Define a list key rule.
     */
    public function list(
        string $key,
        string $delimiter = ',',
        bool $allowEmpty = false
    ): Types\ListKeyRule {
        return $this->keys[$key] = new Types\ListKeyRule($key, $delimiter, $allowEmpty);
    }

    /**
     * Define a URL key rule.
     */
    public function url(string $key): Types\UrlKeyRule
    {
        return $this->keys[$key] = new Types\UrlKeyRule($key);
    }

    /**
     * Define an email key rule.
     */
    public function email(string $key): Types\EmailKeyRule
    {
        return $this->keys[$key] = new Types\EmailKeyRule($key);
    }

    /**
     * Define an IP address key rule.
     */
    public function ip(string $key): Types\IpKeyRule
    {
        return $this->keys[$key] = new Types\IpKeyRule($key);
    }

    /**
     * Define a UUID key rule.
     *
     * @param bool $uuidv4 Whether to enforce UUID v4 format
     */
    public function uuid(string $key, bool $uuidv4 = false): Types\UuidKeyRule
    {
        return $this->keys[$key] = new Types\UuidKeyRule($key, $uuidv4);
    }

    /**
     * Define a size key rule.
     */
    public function size(string $key): Types\SizeKeyRule
    {
        return $this->keys[$key] = new Types\SizeKeyRule($key);
    }

    /**
     * Define a port key rule.
     */
    public function port(string $key): Types\PortKeyRule
    {
        return $this->keys[$key] = new Types\PortKeyRule($key);
    }

    /**
     * Define a date key rule.
     */
    public function date(string $key, string $format = self::DATE_FORMAT): Types\DateKeyRule
    {
        return $this->keys[$key] = new Types\DateKeyRule($key, $format);
    }

    /**
     * Define a date-time key rule.
     *
     * @param bool $immutable Return DateTimeImmutable if true
     */
    public function dateTime(
        string $key,
        string $format = self::DATETIME_FORMAT,
        bool $immutable = true
    ): Types\DateTimeKeyRule {
        return $this->keys[$key] = new Types\DateTimeKeyRule($key, $format, $immutable);
    }

    /**
     * Define a path key rule.
     */
    public function path(string $key, bool $resolveRealpath = false): Types\PathKeyRule
    {
        return $this->keys[$key] = new Types\PathKeyRule($key, $resolveRealpath);
    }

    /**
     * Define a chmod permission key rule.
     */
    public function chmod(string $key): Types\ChmodKeyRule
    {
        return $this->keys[$key] = new Types\ChmodKeyRule($key);
    }

    /**
     * Define a hex value key rule.
     */
    public function hex(string $key): Types\HexKeyRule
    {
        return $this->keys[$key] = new Types\HexKeyRule($key);
    }

    /**
     * Define a base64 value key rule.
     */
    public function base64(string $key): Types\Base64KeyRule
    {
        return $this->keys[$key] = new Types\Base64KeyRule($key);
    }

    /**
     * Define a numeric string key rule.
     */
    public function numericString(string $key): Types\NumericStringKeyRule
    {
        return $this->keys[$key] = new Types\NumericStringKeyRule($key);
    }

    /**
     * Define a regex pattern key rule.
     */
    public function regex(string $key, string $pattern): KeyRule
    {
        return $this->keys[$key] = new Types\RegexKeyRule($key, $pattern);
    }

    /**
     * Define a locale key rule.
     */
    public function locale(string $key): Types\LocaleKeyRule
    {
        return $this->keys[$key] = new Types\LocaleKeyRule($key);
    }

    /**
     * Define a color key rule.
     */
    public function color(string $key): Types\ColorKeyRule
    {
        return $this->keys[$key] = new Types\ColorKeyRule($key);
    }

    /**
     * Define an array key rule.
     */
    public function array(string $key, string $delimiter = self::LIST_DELIMITER): Types\ArrayKeyRule
    {
        return $this->keys[$key] = new Types\ArrayKeyRule($key, $delimiter);
    }

    /**
     * Define a URL path key rule.
     */
    public function urlPath(string $key): Types\UrlKeyRule
    {
        return $this->keys[$key] = new Types\UrlKeyRule($key);
    }

    /**
     * Define a version key rule.
     */
    public function version(string $key): Types\VersionKeyRule
    {
        return $this->keys[$key] = new Types\VersionKeyRule($key);
    }

    /**
     * Get all defined key rules.
     *
     * @internal Used by EnvKit internally.
     *
     * @return array<string, KeyRule>
     */
    public function all(): array
    {
        return $this->keys;
    }

    // ---- Internal ----------------------------------------------------------

    /**
     * Provides an iterator over all defined key-rule pairs.
     *
     * Uses `yield` for lazy iteration, making the Schema object directly iterable.
     *
     * @return Traversable<string, KeyRule> Iterator of key names and their corresponding KeyRule objects
     */
    public function getIterator(): Traversable
    {
        foreach ($this->keys as $key => $rule) {
            yield $key => $rule;
        }
    }
}
