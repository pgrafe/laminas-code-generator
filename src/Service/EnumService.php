<?php


namespace CodeGenerator\Service;


use CodeGenerator\Builder\ClassBuilder;
use CodeGenerator\Model\EnumBuildModel;
use DOMElement;

class EnumService
{
    /**
     * @param string $path
     * @return EnumBuildModel[]
     */
    public function getEnumBuildModelList(string $path): array
    {
        $enumBuildModelList = [];
        $xmlFileService     = new XmlFileService();
        $domDocumentList    = $xmlFileService->getEnumDomDocumentList($path);
        if (count($domDocumentList) === 0) {
            $enumBuildModel = new EnumBuildModel();
            $enumBuildModel->addMessage('could not find any XML file beneath: ' . $path);
            $enumBuildModelList[] = $enumBuildModel;

            return $enumBuildModelList;
        }
        foreach ($domDocumentList as $DOMDocument) {
            foreach ($DOMDocument->getElementsByTagName('enum') as $DOMNode) {
                $enumBuildModel = new EnumBuildModel();
                if (!$DOMNode instanceof DOMElement) {
                    $enumBuildModel->addMessage('could not find valid DOMElement');
                    $enumBuildModelList[] = $enumBuildModel;

                    continue;
                }
                $enumFQDN      = $DOMNode->getAttribute('fqdn');
                $enumFQDNList  = explode('\\', $enumFQDN);
                $enumName      = array_pop($enumFQDNList);
                $enumNameSpace = implode('\\', $enumFQDNList);
                $enumType      = $DOMNode->getAttribute('type');
                if ($enumName === null) {
                    $enumBuildModel->addMessage('could not find valid DOMElement');
                    $enumBuildModelList[] = $enumBuildModel;

                    continue;
                }
                array_splice($enumFQDNList, 1, 0, ['src']);
                $enumPath  = implode('/', $enumFQDNList) . '/';
                $constList = $this->getConstList($DOMNode);

                $enumBuildModel->setBasePath($path);
                $enumBuildModel->setConstList($constList);
                $enumBuildModel->setName($enumName);
                $enumBuildModel->setType($enumType);
                $enumBuildModel->setPath($enumPath);
                $enumBuildModel->setNameSpace($enumNameSpace);
                $enumBuildModel->setStatus(true);

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
            if (!$enumBuildModel->getStatus()) {
                continue;
            }
            $classBuilder = new ClassBuilder();
            $classBuilder->setNameSpace($enumBuildModel->getNameSpace());
            $classBuilder->setClassName($enumBuildModel->getName());
            $classBuilder->addUseClass('InvalidArgumentException');

            $classBuilder->addCommentBlock(['@var ' . $enumBuildModel->getType()]);
            $classBuilder->addContentLine('private ' . $enumBuildModel->getType() . ' $value;');
            foreach ($enumBuildModel->getConstList() as $_constName => $_constValue) {
                $classBuilder->addCommentBlock(['@var ' . $enumBuildModel->getType()]);
                if ($enumBuildModel->getType() === 'int') {
                    $classBuilder->addContentLine('private const ' . $_constName . ' = ' . $_constValue . ';');
                } else {
                    $classBuilder->addContentLine('private const ' . $_constName . ' = \'' . $_constValue . '\';');
                }
                $classBuilder->addCommentBlock(['@return ' . $enumBuildModel->getName()]);
                $classBuilder->addContentLine('public static function ' . $_constName . '(): ' . $enumBuildModel->getName());
                $classBuilder->addContentLine('{');
                $classBuilder->addContentLine('return new self(self::' . $_constName . ');');
                $classBuilder->addContentLine('}');
            }
            $classBuilder->addCommentBlock(['@return ' . $enumBuildModel->getType() . '[]']);
            $classBuilder->addContentLine('public static function getConstList(): array');
            $classBuilder->addContentLine('{');
            foreach ($enumBuildModel->getConstList() as $_constName => $_constValue) {
                $classBuilder->addContentLine('$constList[\'' . $_constName . '\'] = self::' . $_constName . ';');
            }
            $classBuilder->addContentLine('');
            $classBuilder->addContentLine('return $constList;');
            $classBuilder->addContentLine('}');

            $classBuilder->addCommentBlock(
                [
                    '@param ' . $enumBuildModel->getType() . ' $value',
                    '@return ' . $enumBuildModel->getName(),
                ]
            );
            $classBuilder->addContentLine('public static function create(' . $enumBuildModel->getType() . ' $value): ' . $enumBuildModel->getName());
            $classBuilder->addContentLine('{');
            $classBuilder->addContentLine('foreach (self::getConstList() as $_const => $_value) {');
            $classBuilder->addContentLine('if ($value === $_value) {');
            $classBuilder->addContentLine('return self::$_const();');
            $classBuilder->addContentLine('}');
            $classBuilder->addContentLine('}');

            $classBuilder->addContentLine('throw new InvalidArgumentException(\'invalid enum value: "\' . $value . \'"\');');
            $classBuilder->addContentLine('}');

            $classBuilder->addCommentBlock(
                [
                    '@param ' . $enumBuildModel->getType() . ' $value',
                    '@return bool',
                ]
            );
            $classBuilder->addContentLine('public static function isValidValue(' . $enumBuildModel->getType() . ' $value): bool');
            $classBuilder->addContentLine('{');
            $classBuilder->addContentLine('return in_array($value, self::getConstList(), true);');
            $classBuilder->addContentLine('}');

            $classBuilder->addCommentBlock(
                [
                    $enumBuildModel->getName() . ' constructor',
                    '@param ' . $enumBuildModel->getType() . ' $value',
                ]
            );
            $classBuilder->addContentLine('private function __construct(' . $enumBuildModel->getType() . ' $value)');
            $classBuilder->addContentLine('{');
            $classBuilder->addContentLine('$this->value = $value;');
            $classBuilder->addContentLine('}');

            $classBuilder->addCommentBlock(
                [
                    '@param ' . $enumBuildModel->getName() . ' $' . lcfirst($enumBuildModel->getName()),
                    '@return bool',
                ]
            );
            $classBuilder->addContentLine('public function equals(' . $enumBuildModel->getName() . ' $' . lcfirst($enumBuildModel->getName()) . '): bool');
            $classBuilder->addContentLine('{');
            $classBuilder->addContentLine('return $' . lcfirst($enumBuildModel->getName()) . '->getValue() === $this->getValue();');
            $classBuilder->addContentLine('}');

            $classBuilder->addCommentBlock(['@return ' . $enumBuildModel->getType()]);
            $classBuilder->addContentLine('public function getValue(): ' . $enumBuildModel->getType());
            $classBuilder->addContentLine('{');
            $classBuilder->addContentLine('return $this->value;');
            $classBuilder->addContentLine('}');

            file_put_contents($enumBuildModel->getFilePath(), $classBuilder->buildClass());
        }

        return true;
    }

    /**
     * @param DOMElement $DOMNode
     * @return array
     */
    private function getConstList(DOMElement $DOMNode): array
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