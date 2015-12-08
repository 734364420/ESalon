<?php
/**
 * Created by PhpStorm.
 * User: HivenKay
 * Date: 15/9/30
 * Time: 上午10:55
 */
class LfExcelReader {
    static function ReadExcel($FilePath) {
        $ExcelUnit = new ExcelUnits();
        $data = $ExcelUnit->read($FilePath);
        return $data;
    }
}
