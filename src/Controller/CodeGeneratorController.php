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
        $xmlFileService = new XmlFileService();
        $enumService = new EnumService();
        $domDocumentList = $xmlFileService->getEnumDomDocumentList(__DIR__ . '/../../../');

        $enumDefinitionCount = 0;
        foreach ($domDocumentList as $DOMDocument) {
            $enumDefinitionCount += $enumService->getEnumDefinitionCount($DOMDocument);
            $enumService->buildEnum($DOMDocument, __DIR__ . '/../../../');
        }


        $viewModel->setVariable('domDocumentCount', count($domDocumentList));
        $viewModel->setVariable('enumDefinitionCount', $enumDefinitionCount);
        return $viewModel;
    }

}