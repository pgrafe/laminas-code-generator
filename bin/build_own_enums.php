<?php

include '../src/Service/EnumService.php';
include '../src/Service/XmlFileService.php';
include '../src/Model/EnumBuildModel.php';
include '../src/Builder/ClassBuilder.php';

use CodeGenerator\Service\EnumService;

$enumService                = new EnumService();
$enumBuildModelList = $enumService->getEnumBuildModelList(__DIR__ . '/../');
foreach ($enumBuildModelList as $buildModel) {
    $ownPath = str_replace('CodeGenerator/', '', $buildModel->getPath());
    $buildModel->setPath($ownPath);
}
$enumService->buildEnumList($enumBuildModelList);
