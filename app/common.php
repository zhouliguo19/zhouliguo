<?php
// 应用公共文件
    
    
    //公共文件，用来传入xls并下载
    function downloadExcel($data, $table_head, $filename, $format)
    {
        $newExcel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();  //创建一个新的excel文档
        $objSheet = $newExcel->getActiveSheet();  //获取当前操作sheet的对象
        $objSheet->setTitle('sheet1');  //设置当前sheet的标题
        //设置宽度为true,不然太窄了
        $newExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        //获取数据库的数据。//使用模型
        for ($i = 0; $i < count($table_head); $i++) {
            $column = chr(ord('A') + $i);
            //设置宽度为true,不然太窄了
            $newExcel->getActiveSheet()->getColumnDimension($column)->setAutoSize(true);
            $objSheet->setCellValue($column.'1',$table_head[$i]);
        }
        // 填写数据
        $column = ord('A');   // 返回字符的  ASCII 码值
        foreach ($data as $row=>$col) {
            $i=0;
            foreach ($col as $k=>$v ) {
                $objSheet->setCellValue(chr($column+$i).($row+2), $v);
                $i++;
            }
        }
		ob_end_clean() ;
        // $format只能为 Xlsx 或 Xls
        if ($format == 'Xlsx') {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        } elseif ($format == 'Xls') {
            header('Content-Type: application/vnd.ms-excel');
        }

        // header("Content-Disposition: attachment;filename="
        //     . $filename . date('Y-m-d') . '.' . strtolower($format));
            
        header("Content-Disposition: attachment;filename="
            . $filename . '.' . strtolower($format));
        header('Cache-Control: max-age=0');
        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($newExcel, $format);
        //$objWriter = IOFactory::createWriter($newExcel, $format);
        $objWriter->save('php://output');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        exit;
    }
    
    
    function import()
    {
	//获取表格的大小，限制上传表格的大小5M
        if(isset($_FILES['inputExcelclass'])){
            $file_size = $_FILES['inputExcelclass']['size'];
            if ($file_size > 5 * 1024 * 1024) {
                $this->error('文件大小不能超过5M');
                exit();
            }
        }
        //限制上传表格类型
        if(isset($_FILES['inputExcelclass'])){
            $fileExtendName = substr(strrchr($_FILES['inputExcelclass']["name"], '.'), 1);
        }
        //application/vnd.ms-excel  为xls文件类型
        if ($fileExtendName != 'xls' && $fileExtendName != 'xlsx') {
            $this->error('必须为excel表格，且必须为xls格式！');
            exit();
        }
        // 有Xls和Xlsx格式两种
        if( $fileExtendName =='xlsx' )
        {
            $objReader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        }else{
       		$objReader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xls');
        }
        $objReader->setReadDataOnly(TRUE);
        $filename = $_FILES['inputExcelclass']['tmp_name'];
        $objPHPExcel = $objReader->load($filename);  //$filename可以是上传的表格，或者是指定的表格
        $sheet = $objPHPExcel->getSheet(0);   //excel中的第一张sheet
        $highestRow = $sheet->getHighestRow();       // 取得总行数
        $highestColumn = $sheet->getHighestColumn();   // 取得总列数
        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
        $lines = $highestRow -1;
        if($lines <= 0){
           $this->error('excel表格，没有数据！');
        }
        //循环读取excel表格，整合成数组。如果是不指定key的二维，就用$data[i][j]表示。
        for ($j = 2; $j <= $highestRow; $j++) {
           $data[$j - 2] = [
           		'title' => trim($objPHPExcel->getActiveSheet()->getCell("A" . $j)->getValue()),
                'content' => trim($objPHPExcel->getActiveSheet()->getCell("B" . $j)->getValue()),
                'prices' => trim($objPHPExcel->getActiveSheet()->getCell("C" . $j)->getValue()),
                'create_time' => ($objPHPExcel->getActiveSheet()->getCell("D" . $j)->getValue()-25569)*24*60*60,
                'end_time' => ($objPHPExcel->getActiveSheet()->getCell("E" . $j)->getValue()-25569)*24*60*60,
           ];
        }
         
        return $data;
    }