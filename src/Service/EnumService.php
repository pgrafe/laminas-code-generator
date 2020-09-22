<?php


namespace CodeGenerator\Service;


use CodeGenerator\Builder\ClassBuilder;
use CodeGenerator\Model\EnumBuildModel;
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

    /**
     * @param string $path
     * @return EnumBuildModel[]
     */
    public function getEnumBuildModelList(string $path): array
    {
        $enumBuildModelList = [];
        $xmlFileService = new XmlFileService();
        $domDocumentList = $xmlFileService->getEnumDomDocumentList($path);
        foreach ($domDocumentList as $DOMDocument) {
            foreach ($DOMDocument->getElementsByTagName('enum') as $DOMNode) {
                if (!$DOMNode instanceof DOMElement) {
                    continue;
                }
                $enumBuildModel = new EnumBuildModel();
                $enumFQDN = $DOMNode->getAttribute('fqdn');
                $enumFQDNList = explode('\\', $enumFQDN);
                $enumName = array_pop($enumFQDNList);
                $enumNameSpace = implode('\\', $enumFQDNList);
                $enumType = $DOMNode->getAttribute('type');
                if ($enumName === null) {
                    return false;
                }
                array_splice($enumFQDNList, 1, 0, ['src']);
                $enumPath = implode('/', $enumFQDNList) . '/';
                $constList = $this->getConstList($DOMNode);

                $enumBuildModel->setBasePath($path);
                $enumBuildModel->setConstList($constList);
                $enumBuildModel->setName($enumName);
                $enumBuildModel->setType($enumType);
                $enumBuildModel->setPath($enumPath);
                $enumBuildModel->setNameSpace($enumNameSpace);

                $enumBuildModelList[] = $enumBuildModel;
            }
        }

        return $enumBuildModelList;
    }

    /**
     * @param EnumBuildModel[] $enumBuildModelList
     * @return bool
     */
    public function buildEnumList(array $enumBuildModelList): bool
    {
        foreach ($enumBuildModelList as $enumBuildModel) {
            $classBuilder = new ClassBuilder();
            $classBuilder->addContentLine('<?php');
            $classBuilder->addContentLine('');
            $classBuilder->addContentLine('');
            $classBuilder->addContentLine('namespace ' . $enumBuildModel->getNameSpace() . ';');
            $classBuilder->addContentLine('');
            $classBuilder->addContentLine('');
            $classBuilder->addContentLine('use InvalidArgumentException;');
            $classBuilder->addContentLine('');
            $classBuilder->addContentLine('');

            $classBuilder->addContentLine('class ' . $enumBuildModel->getName());
            $classBuilder->addContentLine('{');

            $classBuilder->addContentLine('/**');
            $classBuilder->addContentLine('* @var ' . $enumBuildModel->getType());
            $classBuilder->addContentLine('*/');
            $classBuilder->addContentLine('private ' . $enumBuildModel->getType() . ' $value;');
            foreach ($enumBuildModel->getConstList() as $_constName => $_constValue) {

                $classBuilder->addContentLine('/**');
                $classBuilder->addContentLine('* @var ' . $enumBuildModel->getType());
                $classBuilder->addContentLine('*/');
                if ($enumBuildModel->getType() === 'int') {
                    $classBuilder->addContentLine('private const ' . $_constName . ' = ' . $_constValue . ';');
                } else {
                    $classBuilder->addContentLine('private const ' . $_constName . ' = \'' . $_constValue . '\';');
                }

                $classBuilder->addContentLine('/**');
                $classBuilder->addContentLine('* @return ' . $enumBuildModel->getName());
                $classBuilder->addContentLine('*/');

                $classBuilder->addContentLine('public static function ' . $_constName . '(): ' . $enumBuildModel->getName());
                $classBuilder->addContentLine('{');
                $classBuilder->addContentLine('return new self(self::' . $_constName . ');');
                $classBuilder->addContentLine('}');
            }


            $classBuilder->addContentLine('/**');
            $classBuilder->addContentLine('* @return ' . $enumBuildModel->getType() . '[]');
            $classBuilder->addContentLine('*/');
            $classBuilder->addContentLine('public static function getConstList(): array');
            $classBuilder->addContentLine('{');
            foreach ($enumBuildModel->getConstList() as $_constName => $_constValue) {
                $classBuilder->addContentLine('$constList[\'' . $_constName . '\']  = self::' . $_constName . ';');
            }
            $classBuilder->addContentLine('return $constList;');
            $classBuilder->addContentLine('}');


            $classBuilder->addContentLine('/**');
            $classBuilder->addContentLine('* @param ' . $enumBuildModel->getType() . ' $value');
            $classBuilder->addContentLine('* @return ' . $enumBuildModel->getName());
            $classBuilder->addContentLine('*/');
            $classBuilder->addContentLine('public static function create(' . $enumBuildModel->getType() . ' $value): ' . $enumBuildModel->getName());
            $classBuilder->addContentLine('{');
            $classBuilder->addContentLine('foreach (self::getConstList() as $_const => $_value) {');
            $classBuilder->addContentLine('if ($value === $_value) {');
            $classBuilder->addContentLine('return self::$_const();');
            $classBuilder->addContentLine('}');
            $classBuilder->addContentLine('}');

            $classBuilder->addContentLine('throw new InvalidArgumentException(\'invalid enum value: "\' . $value . \'"\');');
            $classBuilder->addContentLine('}');


            $classBuilder->addContentLine('/**');
            $classBuilder->addContentLine('* @param ' . $enumBuildModel->getType() . ' $value');
            $classBuilder->addContentLine('* @return bool');
            $classBuilder->addContentLine('*/');
            $classBuilder->addContentLine('public static function isValidValue(' . $enumBuildModel->getType() . ' $value): bool');
            $classBuilder->addContentLine('{');
            $classBuilder->addContentLine('return in_array($value, self::getConstList(), true);');
            $classBuilder->addContentLine('}');


            $classBuilder->addContentLine('/**');
            $classBuilder->addContentLine('* ' . $enumBuildModel->getName() . ' constructor');
            $classBuilder->addContentLine('* @param ' . $enumBuildModel->getType() . ' $value');
            $classBuilder->addContentLine('*/');
            $classBuilder->addContentLine('private function __construct(' . $enumBuildModel->getType() . ' $value)');
            $classBuilder->addContentLine('{');
            $classBuilder->addContentLine('$this->value = $value;');
            $classBuilder->addContentLine('}');


            $classBuilder->addContentLine('/**');
            $classBuilder->addContentLine('* @param ' . $enumBuildModel->getName() . ' $' . lcfirst($enumBuildModel->getName()));
            $classBuilder->addContentLine('* @return bool');
            $classBuilder->addContentLine('*/');
            $classBuilder->addContentLine('public function equals(' . $enumBuildModel->getName() . ' $' . lcfirst($enumBuildModel->getName()) . '): bool');
            $classBuilder->addContentLine('{');
            $classBuilder->addContentLine('return $' . lcfirst($enumBuildModel->getName()) . '->getValue() === $this->getValue();');
            $classBuilder->addContentLine('}');


            $classBuilder->addContentLine('/**');
            $classBuilder->addContentLine('*');
            $classBuilder->addContentLine('* @return ' . $enumBuildModel->getType());
            $classBuilder->addContentLine('*');
            $classBuilder->addContentLine('*/');
            $classBuilder->addContentLine('public function getValue(): ' . $enumBuildModel->getType());
            $classBuilder->addContentLine('{');
            $classBuilder->addContentLine('return $this->value;');
            $classBuilder->addContentLine('}');

            $classBuilder->addContentLine('}');
            file_put_contents($enumBuildModel->getFilePath(), implode("\n", $classBuilder->getContentList()));
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