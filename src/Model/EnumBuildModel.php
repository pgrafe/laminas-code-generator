<?php


namespace CodeGenerator\Model;


class EnumBuildModel
{

    /**
     * @var string
     */
    private string $base_path = '';

    /**
     * @var string
     */
    private string $name = '';

    /**
     * @var string
     */
    private string $type = '';

    /**
     * @var string
     */
    private string $path = '';

    /**
     * @var string
     */
    private string $nameSpace = '';

    /**
     * @var array
     */
    private array $const_list = [];

    /**
     * @return string
     */
    public function getBasePath(): string
    {
        return $this->base_path;
    }

    /**
     * @param string $base_path
     */
    public function setBasePath(string $base_path): void
    {
        $this->base_path = $base_path;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->enum_name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->enum_name = $name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->enum_type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->enum_type = $type;
    }

    /**
     * @return array
     */
    public function getConstList(): array
    {
        return $this->cons_list;
    }

    /**
     * @param array $const_list
     */
    public function setConstList(array $const_list): void
    {
        $this->cons_list = $const_list;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getNameSpace(): string
    {
        return $this->nameSpace;
    }

    /**
     * @param string $nameSpace
     */
    public function setNameSpace(string $nameSpace): void
    {
        $this->nameSpace = $nameSpace;
    }

    /**
     * @return string
     */
    public function getFilePath():string
    {
        return $this->getBasePath() . $this->getPath() . $this->getName() . '.php';
    }

}