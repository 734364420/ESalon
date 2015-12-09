<?php

/**
 * Created by PhpStorm.
 * User: HivenKay
 * Date: 15/9/30
 * Time: 上午11:03
 */
class ExcelUnits
{
    public function __construct()
    {
        //导入phpExcel核心类
        include_once 'PHPExcel/PHPExcel.php';
    }

    /**
     * 读取excel $filename 路径文件名 $encode 返回数据的编码 默认为utf8
     */
    public function read($filename, $encode = 'utf-8')
    {
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($filename);
        $objWorksheet = $objPHPExcel->getActiveSheet();
        $highestRow = $objWorksheet->getHighestRow();
        $highestColumn = $objWorksheet->getHighestColumn();
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
        $excelData = array();
        for ($row = 1; $row <= $highestRow; $row++) {
            for ($col = 0; $col < $highestColumnIndex; $col++) {
                $excelData[$row][] = (string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
            }
        }
        return $excelData;
    }
}
