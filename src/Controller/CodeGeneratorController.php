<?php


namespace CodeGenerator\Controller;


use CodeGenerator\Service\DoctrineService;
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

    /**
     * @return ViewModel
     */
    public function enumAction(): ViewModel
    {

        $viewModel          = new ViewModel();
        $xmlFileService     = new XmlFileService();
        $moduleFolder       = $xmlFileService->findModuleFolder(__DIR__ . '/../../../../../');
        $enumService        = new EnumService();
        $enumBuildModelList = $enumService->getEnumBuildModelList($moduleFolder);
        $enumService->buildEnumList($enumBuildModelList);

        $viewModel->setVariable('enumBuildModelList', $enumBuildModelList);

        return $viewModel;
    }

    /**
     * @return ViewModel
     */
    public function doctrineEntityAction(): ViewModel
    {
        $viewModel          = new ViewModel();
        $xmlFileService     = new XmlFileService();
        $moduleFolder       = $xmlFileService->findModuleFolder(__DIR__ . '/../../../../../');
        $doctrineService        = new DoctrineService();
        $doctrineBuildModelList = $doctrineService->getDoctrineBuildModelList($moduleFolder);
        $doctrineService->buildDoctrineList($doctrineBuildModelList);

        $viewModel->setVariable('doctrineBuildModelList', $doctrineBuildModelList);

        return $viewModel;
    }

}