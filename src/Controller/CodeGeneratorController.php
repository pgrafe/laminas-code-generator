<?php


namespace CodeGenerator\Controller;


use CodeGenerator\Service\EnumService;
use CodeGenerator\Service\XmlFileService;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class CodeGeneratorController extends AbstractActionController
{

    /**
     * @return ViewModel
     */
    public function indexAction(): ViewModel
    {
        return new ViewModel();
    }

    public function enumAction(): ViewModel
    {
        $viewModel = new ViewModel();
        $enumService = new EnumService();
        $enumBuildModelList = $enumService->getEnumBuildModelList(__DIR__ . '/../../../../../module/');
        $enumService->buildEnumList($enumBuildModelList);

        return $viewModel;
    }

}