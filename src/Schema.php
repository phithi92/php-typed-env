<?php

declare(strict_types=1);

namespace Phithi92\TypedEnv;

final class Schema
{
    /** @var array<string, KeyRule> */
    private array $keys = [];

    /**
     * Factory builder
     */
    public static function build(): self
    {
        return new self();
    }

    // ---- Typed entry points ------------------------------------------------

    public function string(string $key): KeyRule
    {
        return $this->rule($key)->typeString();
    }

    public function bool(string $key): KeyRule
    {
        return $this->rule($key)->typeBool();
    }

    public function int(string $key): KeyRule
    {
        return $this->rule($key)->typeInt();
    }

    public function float(string $key): KeyRule
    {
        return $this->rule($key)->typeFloat();
    }

    public function duration(string $key): KeyRule
    {
        return $this->rule($key)->typeDuration();
    }

    public function json(string $key, bool $assoc = true): KeyRule
    {
        return $this->rule($key)->typeJson($assoc);
    }

    public function list(string $key, string $delimiter = ',', bool $allowEmpty = false): KeyRule
    {
        return $this->rule($key)->typeList($delimiter, $allowEmpty);
    }

    public function url(string $key): KeyRule
    {
        return $this->rule($key)->typeUrl();
    }

    public function email(string $key): KeyRule
    {
        return $this->rule($key)->typeEmail();
    }

    public function ip(string $key): KeyRule
    {
        return $this->rule($key)->typeIp();
    }

    public function uuid(string $key): KeyRule
    {
        return $this->rule($key)->typeUuidAny();
    }

    public function uuidV4(string $key): KeyRule
    {
        return $this->rule($key)->typeUuidV4();
    }

    public function size(string $key): KeyRule
    {
        return $this->rule($key)->typeSize();
    }

    public function port(string $key): KeyRule
    {
        return $this->rule($key)->typePort();
    }

    public function date(string $key, string $format = 'Y-m-d'): KeyRule
    {
        return $this->rule($key)->typeDate($format);
    }

    public function dateTime(string $key, string $format = 'c', bool $immutable = true): KeyRule
    {
        return $this->rule($key)->typeDateTime($format, $immutable);
    }

    public function path(string $key, bool $resolveRealpath = false): KeyRule
    {
        return $this->rule($key)->typePath($resolveRealpath);
    }

    public function chmod(string $key): KeyRule
    {
        return $this->rule($key)->typeChmod();
    }

    public function hex(string $key, ?int $length = null): KeyRule
    {
        return $this->rule($key)->typeHex($length);
    }

    public function base64(string $key): KeyRule
    {
        return $this->rule($key)->typeBase64();
    }

    public function numericString(string $key): KeyRule
    {
        return $this->rule($key)->typeNumericString();
    }

    public function regex(string $key, string $pattern): KeyRule
    {
        return $this->rule($key)->typeRegex($pattern);
    }

    public function locale(string $key): KeyRule
    {
        return $this->rule($key)->typeLocale();
    }

    public function color(string $key): KeyRule
    {
        return $this->rule($key)->typeColor();
    }

    public function array(string $key, string $delimiter = ','): KeyRule
    {
        return $this->rule($key)->typeArray($delimiter);
    }

    public function urlPath(string $key): KeyRule
    {
        return $this->rule($key)->typeUrlPath();
    }

    public function version(string $key): KeyRule
    {
        return $this->rule($key)->typeVersion();
    }

    // ---- Meta --------------------------------------------------------------

    /**
     * @internal used by EnvKit
     *
     * @return array<string, KeyRule>
     */
    public function all(): array
    {
        return $this->keys;
    }

    // ---- Internal ----------------------------------------------------------

    /** Get or create a rule for a key */
    private function rule(string $key): KeyRule
    {
        return $this->keys[$key] ??= new KeyRule($key);
    }
}
