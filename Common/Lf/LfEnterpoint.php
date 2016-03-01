<?php
require __DIR__ . '/Lf/LfClassLoader.php';
require __DIR__ . '/Function.php';
require __DIR__ . '/LfCommonKey.php';
require __DIR__ .'/Excel/ExcelUnits.php';
require __DIR__ .'/Excel/PHPExcel/PHPExcel.php';
LfClassLoader::AutoLoad('Lf',__DIR__);
LfConfig::$DbUserName='';
LfConfig::$DbPassword='';
LfConfig::$DbDatabase='';
