<?php


namespace CodeGenerator\Builder;


class ClassBuilder
{

    /**
     * @var array
     */
    private array $contentList = [];
    /**
     * @var string
     */
    private string $class_name;
    /**
     * @var string
     */
    private string $name_space;
    /**
     * @var array
     */
    private array $use_class_list = [];

    /**
     * @param string $contentLine
     */
    public function addContentLine(string $contentLine): void
    {
        $this->contentList[] = $contentLine;
    }

    /**
     * @return array
     */
    public function getContentList(): array
    {
        return $this->contentList;
    }

    /**
     * @param string $className
     */
    public function setClassName(string $className): void
    {
        $this->class_name = $className;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->class_name;
    }

    /**
     * @param string $nameSpace
     */
    public function setNameSpace(string $nameSpace): void
    {
        $this->name_space = $nameSpace;
    }

    /**
     * @return string
     */
    public function getNameSpace(): string
    {
        return $this->name_space;
    }

    /**
     * @param string $className
     */
    public function addUseClass(string $className): void
    {
        $this->use_class_list[$className] = $className;
        ksort($this->use_class_list);
    }

    /**
     * @return array
     */
    public function getUseClassList(): array
    {
        return $this->use_class_list;
    }

    /**
     * @return string
     */
    public function buildClass(): string
    {
        $contentList   = [];
        $contentList[] = '<?php';
        $contentList[] = '';
        $contentList[] = '';
        $contentList[] = 'namespace ' . $this->getNameSpace() . ';';
        $contentList[] = '';
        $contentList[] = '';
        foreach ($this->getUseClassList() as $_useClass) {
            $contentList[] = 'use ' . $_useClass . ';';
        }
        $contentList[] = '';
        $contentList[] = '';
        $contentList[] = 'class ' . $this->getClassName();
        $contentList[] = '{';

        $tabCount = 1;
        foreach ($this->getContentList() as $_content) {
            $_contentLine = trim($_content);

            if ($_contentLine === '/**') {
                $contentList[] = '';
            }
            if (mb_strpos($_contentLine, '*') === 0) {
                $_contentLine = ' ' . $_contentLine;
            }
            if (mb_strpos($_contentLine, '}') !== false) {
                $tabCount--;
            }
            for ($_tabCount = 0; $_tabCount < $tabCount; $_tabCount++) {
                $_contentLine = '    ' . $_contentLine;
            }
            $contentList[] = $_contentLine;
            if (mb_strpos($_contentLine, '{') !== false) {
                $tabCount++;
            }
        }
        $contentList[] = '}';

        return implode("\n", $contentList);
    }

    /**
     * @param array $commentList
     */
    public function addCommentBlock(array $commentList): void
    {
        $this->addContentLine('/**');
        foreach ($commentList as $_commentLine) {
            $this->addContentLine('* ' . trim($_commentLine));
        }
        $this->addContentLine('*/');
    }

}