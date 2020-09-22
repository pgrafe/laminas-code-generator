<?php


namespace CodeGenerator\Service;


use DOMDocument;
use DOMElement;

class EnumService
{
    /**
     * @param DOMDocument $DOMDocument
     * @return int
     */
    public function getEnumDefinitionCount(DOMDocument $DOMDocument): int
    {
        return $DOMDocument->getElementsByTagName('enum')->length;
    }

    public function buildEnum(DOMDocument $DOMDocument, $path): bool
    {
        foreach ($DOMDocument->getElementsByTagName('enum') as $DOMNode) {
            if (!$DOMNode instanceof DOMElement) {
                continue;
            }
            $enumFQDN      = $DOMNode->getAttribute('fqdn');
            $enumFQDNList  = explode('\\', $enumFQDN);
            $enumName      = array_pop($enumFQDNList);
            $enumNameSpace = implode('\\', $enumFQDNList);
            $enumType      = $DOMNode->getAttribute('type');
            if ($enumName === null) {
                return false;
            }
            array_splice($enumFQDNList, 1, 0, ['src']);
            $enumPath = $path . implode('/', $enumFQDNList) . '/';
            if (!file_exists($enumPath)) {
                return false;
            }

            $constList = $this->getConstList($DOMNode);

            $enumContentList   = [];
            $enumContentList[] = '<?php';
            $enumContentList[] = '';
            $enumContentList[] = '';
            $enumContentList[] = 'namespace ' . $enumNameSpace . ';';
            $enumContentList[] = 'use InvalidArgumentException;';

            $enumContentList[] = 'class ' . $enumName;
            $enumContentList[] = '{';
            $enumContentList[] = '  /**';
            $enumContentList[] = '  * @var ' . $enumType;
            $enumContentList[] = '  */';
            $enumContentList[] = '  private $value;';

            foreach ($constList as $_constName => $_constValue) {
                $enumContentList[] = '  /**';
                $enumContentList[] = '  * @var ' . $enumType;
                $enumContentList[] = '  */';
                if ($enumType === 'int') {
                    $enumContentList[] = '  private const ' . $_constName . ' = ' . $_constValue . ';';
                } else {
                    $enumContentList[] = '  private const ' . $_constName . ' = \'' . $_constValue . '\';';
                }


                $enumContentList[] = '  /**';
                $enumContentList[] = '   * @return ' . $enumName;
                $enumContentList[] = '   */';
                $enumContentList[] = '  public static function ' . $_constName . '(): ' . $enumName;
                $enumContentList[] = '  {';
                $enumContentList[] = '      return new self(self::' . $_constName . ');';
                $enumContentList[] = '  }';

            }

            $enumContentList[] = '  /**';
            $enumContentList[] = '   * @return ' . $enumType . '[]';
            $enumContentList[] = '   */';
            $enumContentList[] = '  public static function getConstList(): array';
            $enumContentList[] = '  {';
            foreach ($constList as $_constName => $_constValue) {
                $enumContentList[] = '  $constList[\'' . $_constName . '\']  = self::' . $_constName . ';';
            }
            $enumContentList[] = '  return $constList;';
            $enumContentList[] = '  }';

            $enumContentList[] = '  /**';
            $enumContentList[] = '  * @param ' . $enumType . ' $value';
            $enumContentList[] = '  * @return ' . $enumName;
            $enumContentList[] = '  */';
            $enumContentList[] = '  public static function create(' . $enumType . ' $value): ' . $enumName;
            $enumContentList[] = '  {';
            $enumContentList[] = '  foreach (self::getConstList() as $_const => $_value) {';
            $enumContentList[] = '  if ($value === $_value) {';
            $enumContentList[] = '  return self::$_const();';
            $enumContentList[] = '  }';
            $enumContentList[] = '  }';

            $enumContentList[] = '  throw new InvalidArgumentException(\'invalid enum value: "\' . $value . \'"\');';
            $enumContentList[] = '  }';

            $enumContentList[] = '  /**';
            $enumContentList[] = '  * @param ' . $enumType . ' $value';
            $enumContentList[] = '  * @return bool';
            $enumContentList[] = '  */';
            $enumContentList[] = '  public static function isValidValue(' . $enumType . ' $value): bool';
            $enumContentList[] = '  {';
            $enumContentList[] = '  return in_array($value, self::getConstList(), true);';
            $enumContentList[] = '  }';

            $enumContentList[] = '  /**';
            $enumContentList[] = '  * ' . $enumName . ' constructor';
            $enumContentList[] = '  * @param ' . $enumType . ' $value';
            $enumContentList[] = '  */';
            $enumContentList[] = '  private function __construct(' . $enumType . ' $value)';
            $enumContentList[] = '  {';
            $enumContentList[] = '      $this->value = $value;';
            $enumContentList[] = '  }';

            $enumContentList[] = '  /**';
            $enumContentList[] = '  * @param ' . $enumName . ' $' . lcfirst($enumName);
            $enumContentList[] = '  * @return bool';
            $enumContentList[] = '  */';
            $enumContentList[] = '  public function equals(' . $enumName . ' $' . lcfirst($enumName) . '): bool';
            $enumContentList[] = '  {';
            $enumContentList[] = '      return $' . lcfirst($enumName) . '->getValue() === $this->getValue();';
            $enumContentList[] = '  }';

            $enumContentList[] = '  /**';
            $enumContentList[] = '  *';
            $enumContentList[] = '  * @return ' . $enumType;
            $enumContentList[] = '  *';
            $enumContentList[] = '  */';
            $enumContentList[] = '  public function getValue(): ' . $enumType;
            $enumContentList[] = '  {';
            $enumContentList[] = '      return $this->value;';
            $enumContentList[] = '  }';

            $enumContentList[] = '}';
            file_put_contents($enumPath . $enumName . '.php', implode("\n", $enumContentList));
        }

        return true;
    }

    private function getConstList(DOMElement $DOMNode)
    {
        $constList = [];
        foreach ($DOMNode->getElementsByTagName('const') as $_DOMNode) {
            if (!$_DOMNode instanceof DOMElement) {
                continue;
            }
            $constList[$_DOMNode->getAttribute('name')] = $_DOMNode->getAttribute('value');
        }

        return $constList;
    }

}