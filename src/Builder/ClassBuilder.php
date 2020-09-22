<?php


namespace CodeGenerator\Builder;


class ClassBuilder
{

    /**
     * @var array
     */
    private array $contentList = [];

    /**
     * @var int
     */
    private int $tabCount = 0;

    /**
     * @param string $contentLine
     */
    public function addContentLine(string $contentLine): void
    {
        if ($contentLine === '/**') {
            $this->contentList[] = '';
        }
        if (mb_strpos($contentLine, '*') === 0) {
            $contentLine = ' ' . $contentLine;
        }
        if (mb_strpos($contentLine, '}') !== false) {
            $this->tabCount--;
        }
        for ($_tabCount = 0; $_tabCount < $this->tabCount; $_tabCount++) {
            $contentLine = "\t" . $contentLine;
        }
        $this->contentList[] = $contentLine;
        if (mb_strpos($contentLine, '{') !== false) {
            $this->tabCount++;
        }
    }

    /**
     * @return array
     */
    public function getContentList(): array
    {
        return $this->contentList;
    }

}