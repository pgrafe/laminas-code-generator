<?php


namespace CodeGenerator\Service;


use DOMDocument;
use FilesystemIterator;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class XmlFileService
{

    /**
     * @param string $path
     * @return array
     */
    public function getEnumXmlFileList(string $path): array
    {
        $directory = new RecursiveDirectoryIterator($path, FilesystemIterator::FOLLOW_SYMLINKS);
        $filter    = new RecursiveCallbackFilterIterator(
            $directory, function ($current) {
            // Skip hidden files and directories.
            if ($current->getFilename()[0] === '.') {
                return false;
            }
            if ($current->isDir()) {
                return true;
            }

            // Only consume files of interest.
            return strpos($current->getFilename(), 'enum-list.xml') === 0;
        }
        );
        $iterator  = new RecursiveIteratorIterator($filter);
        $fileList  = [];
        foreach ($iterator as $info) {
            $fileList[] = $info->getPathname();
        }

        return $fileList;

    }

    /**
     * @param string $path
     * @return DOMDocument[]
     */
    public function getEnumDomDocumentList(string $path): array
    {
        $domDocumentList = [];
        foreach ($this->getEnumXmlFileList($path) as $fileName) {
            $doc = new DOMDocument();
            $doc->load($fileName);
            $domDocumentList[] = $doc;
        }

        return $domDocumentList;
    }

}