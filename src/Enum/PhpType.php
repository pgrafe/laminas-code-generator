<?php


namespace CodeGenerator\Enum;


use InvalidArgumentException;


/**
 * Und auch hier noch ein Kommentar
 */
class PhpType
{

    /**
     * @var string
     */
    private string $value;

    /**
     * @var string
     */
    private const ARRAY = 'array';

    /**
     * @return PhpType
     */
    public static function ARRAY(): PhpType
    {
        return new self(self::ARRAY);
    }

    /**
     * @var string
     */
    private const INT = 'int';

    /**
     * @return PhpType
     */
    public static function INT(): PhpType
    {
        return new self(self::INT);
    }

    /**
     * @var string
     */
    private const FLOAT = 'float';

    /**
     * @return PhpType
     */
    public static function FLOAT(): PhpType
    {
        return new self(self::FLOAT);
    }

    /**
     * @var string
     */
    private const DATETIME = 'DateTime';

    /**
     * @return PhpType
     */
    public static function DATETIME(): PhpType
    {
        return new self(self::DATETIME);
    }

    /**
     * @var string
     */
    private const BOOL = 'bool';

    /**
     * @return PhpType
     */
    public static function BOOL(): PhpType
    {
        return new self(self::BOOL);
    }

    /**
     * @var string
     */
    private const STRING = 'string';

    /**
     * @return PhpType
     */
    public static function STRING(): PhpType
    {
        return new self(self::STRING);
    }

    /**
     * @return string[]
     */
    public static function getConstList(): array
    {
        $constList['ARRAY'] = self::ARRAY;
        $constList['INT'] = self::INT;
        $constList['FLOAT'] = self::FLOAT;
        $constList['DATETIME'] = self::DATETIME;
        $constList['BOOL'] = self::BOOL;
        $constList['STRING'] = self::STRING;
        
        return $constList;
    }

    /**
     * @param string $value
     * @return PhpType
     */
    public static function create(string $value): PhpType
    {
        foreach (self::getConstList() as $_const => $_value) {
            if ($value === $_value) {
                return self::$_const();
            }
        }
        throw new InvalidArgumentException('invalid enum value: "' . $value . '"');
    }

    /**
     * @param string $value
     * @return bool
     */
    public static function isValidValue(string $value): bool
    {
        return in_array($value, self::getConstList(), true);
    }

    /**
     * PhpType constructor
     * @param string $value
     */
    private function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @param PhpType $phpType
     * @return bool
     */
    public function equals(PhpType $phpType): bool
    {
        return $phpType->getValue() === $this->getValue();
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}