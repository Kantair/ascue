<?php IncludeCom('PHPexcel/Classes/PHPExcel')?>
<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/tpl/dbdata.php');
$server = $dbHost;
$link = mssql_connect($server, $dbUser, $dbPass);

$selected = mssql_select_db($dbName, $link) or die("Couldn’t open database databasename");

$date_ot_sql=$_COOKIE['date_exp_ot'];
$date_do_sql=$_COOKIE['date_exp_do'];
$item=$_COOKIE['item'];
$id_object=$_COOKIE['id_object'];

$result = mssql_query("select Convert(Varchar(50),DATA_DATE, 120) as DATA_DATE, VALUE0 as VALUE0 from DATA where ITEM='$item' and OBJECT='$id_object' and PARNUMBER=12 and (DATA_DATE>'$date_ot_sql' and DATA_DATE<='$date_do_sql') order by DATA_DATE");
$result1 = mssql_query("select Convert(Varchar(50),DATA_DATE, 120) as DATA_DATE, VALUE0 as VALUE0 from DATA where ITEM='$item' and OBJECT='$id_object' and PARNUMBER=218 and (DATA_DATE>'$date_ot_sql' and DATA_DATE<='$date_do_sql') order by DATA_DATE");

$val='';
$time='';
$prog='';

while($row1 = mssql_fetch_array($result1)){
    $prog.=$row1['VALUE0']+0;
    $prog.=',';
}

$prog = explode(",",$prog);//преобразование в массив

while($row = mssql_fetch_array($result)){
if ($val) {$val.=', ';$time.=', ';}
    $val.=$row['VALUE0'];
    $time.=$row['DATA_DATE'];
}

$val = explode(",",$val);//преобразование в массив
$time = explode(",",$time);//преобразование в массив
end($val);

$par1='';$par2='';
for ($i=0;$i<key($val);$i++) {
    $par1.=($val[$i]+$val[$i+1])/2;
    $par1.=',';
    $par2.=$time[$i+1];
    $par2.=',';
$i++;
}
$par1 = explode(",",$par1);//преобразование в массив
$par2 = explode(",",$par2);//преобразование в массив


$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0); //С помощью метода setActiveSheetIndex(0) – указываем индекс (номер) активного листа. Нумерация листов начинается с нуля. Далее с помощью метода getActiveSheet() – получаем объект этого активного листа, то есть другими словами получаем доступ к нему для работы. И сохраняем этот объект в переменную $active_sheet.
$active_sheet = $objPHPExcel->getActiveSheet();




//Ориентация страницы и  размер листа
$active_sheet->getPageSetup()
		->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
$active_sheet->getPageSetup()
			->SetPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
//Поля документа		
$active_sheet->getPageMargins()->setTop(1);
$active_sheet->getPageMargins()->setRight(0.75);
$active_sheet->getPageMargins()->setLeft(0.75);
$active_sheet->getPageMargins()->setBottom(1);
//Название листа
$active_sheet->setTitle("Отчет по энергии за час");	
//Шапа и футер 
$active_sheet->getHeaderFooter()->setOddHeader("&CОтчет по энергии за час");	
$active_sheet->getHeaderFooter()->setOddFooter("&CОтчет создан с помощью web-интерфейса ГЭП 'ВОКЭ'");
//Настройки шрифта
$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);

$active_sheet->getColumnDimension('A')->setWidth(15);
$active_sheet->getColumnDimension('B')->setWidth(20);
$active_sheet->getColumnDimension('C')->setWidth(15);
$active_sheet->getColumnDimension('D')->setWidth(15);
$active_sheet->getColumnDimension('E')->setWidth(15);
$active_sheet->getColumnDimension('F')->setWidth(15);

$active_sheet->mergeCells('A1:F1');
$active_sheet->getRowDimension('1')->setRowHeight(20);
$active_sheet->setCellValue('A1','Отчет по энергии за час');

//получение имени точки учета

	$result = mssql_query("select OBJDESCRIPTION from OBJECT where OBJCODE='$id_object' and OBJTYPE=99");
	$row = mssql_fetch_array($result);
	$name = iconv('Windows-1251', 'UTF-8', $row['OBJDESCRIPTION']);//перевод кодировки

$active_sheet->mergeCells('A2:F2');
$active_sheet->setCellValue('A2',$name);

$active_sheet->mergeCells('A3:E3');
$active_sheet->setCellValue('A3','Дата создания отчета');

//Записываем данные в ячейку
$date = date('d-m-Y');
$active_sheet->setCellValue('F3',$date);
//Устанавливает формат данных в ячейке - дата
$active_sheet->getStyle('F3')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX14);


//Создаем шапку таблички данных
$active_sheet->setCellValue('B6','Время');
$active_sheet->setCellValue('C6','кВт*ч');
$active_sheet->setCellValue('D6','Прогноз');
$active_sheet->setCellValue('E6','Отклонение');

//В цикле проходимся по элементам массива и выводим все в соответствующие ячейки
$row_start = 7;
$i = 0;
end($par1);
for ($i;$i<key($par1);$i++) {
$row_next = $row_start + $i;
	$active_sheet->setCellValue('B'.$row_next,$par2[$i]);
	$active_sheet->setCellValue('C'.$row_next,$par1[$i]);
	$active_sheet->setCellValue('D'.$row_next,$prog[$i]);
	$active_sheet->setCellValue('E'.$row_next,$par1[$i]-$prog[$i]);
}

$active_sheet->setCellValue('B'.($i+7), 'Итого (кВт*ч):');
$active_sheet->setCellValue('C'.($i+7), '=SUM(C7:C'.($i+6).')');
$active_sheet->setCellValue('D'.($i+7), '=SUM(D7:D'.($i+6).')');
$active_sheet->setCellValue('E'.($i+7), '=SUM(E7:E'.($i+6).')');

//массив стилей
$style_wrap = array(
	//рамки
	'borders'=>array(
		//внешняя рамка
		'outline' => array(
			'style'=>PHPExcel_Style_Border::BORDER_THICK
		),
		//внутренняя
		'allborders'=>array(
			'style'=>PHPExcel_Style_Border::BORDER_THIN,
			'color' => array(
				'rgb'=>'696969'
			)
		)
	)
);
//применяем массив стилей к ячейкам 
$active_sheet->getStyle('B6:E'.($i+6))->applyFromArray($style_wrap);

//Стили для верхней надписи строка 1
$style_header = array(
	//Шрифт
	'font'=>array(
		'bold' => true,
		'name' => 'Times New Roman',
		'size' => 20
	),
//Выравнивание
	'alignment' => array(
		'horizontal' => PHPExcel_STYLE_ALIGNMENT::HORIZONTAL_CENTER,
		'vertical' => PHPExcel_STYLE_ALIGNMENT::VERTICAL_CENTER,
	)
);

$active_sheet->getStyle('A1:F1')->applyFromArray($style_header);

//Стили для верхней надписи строка 2
$style_header2 = array(
	//Шрифт
	'font'=>array(
		'name' => 'Times New Roman',
		'size' => 12
	),
//Выравнивание
	'alignment' => array(
		'horizontal' => PHPExcel_STYLE_ALIGNMENT::HORIZONTAL_CENTER,
		'vertical' => PHPExcel_STYLE_ALIGNMENT::VERTICAL_CENTER,
	)
);

$active_sheet->getStyle('A2:F2')->applyFromArray($style_header2);

//Стили для текта возле даты
$style_tdate = array(
//выравнивание
	'alignment' => array(
		'horizontal' => PHPExcel_STYLE_ALIGNMENT::HORIZONTAL_RIGHT,
	),
//рамки
	'borders' => array(
		'right' => array(
		'style'=>PHPExcel_Style_Border::BORDER_NONE
		)
	)
);
$active_sheet->getStyle('A3:E3')->applyFromArray($style_tdate);

//Стили для даты
$style_date = array(
	//рамки
	'borders' => array(
		'left' => array(
			'style'=>PHPExcel_Style_Border::BORDER_NONE
		)
	
	),
);
$active_sheet->getStyle('F3')->applyFromArray($style_date);

//Стили для шапочки прайс-листа
$style_hprice = array(
	//выравнивание
	'alignment' => array(
		'horizontal' => PHPExcel_STYLE_ALIGNMENT::HORIZONTAL_CENTER,
	),
//заполнение цветом
	'fill' => array(
		'type' => PHPExcel_STYLE_FILL::FILL_SOLID,
		'color'=>array(
			'rgb' => 'CFCFCF'
		)
	),
//Шрифт
	'font'=>array(
		'bold' => true,
		'italic' => true,
		'name' => 'Times New Roman',
		'size' => 10
	),
	


);
$active_sheet->getStyle('B6:E6')->applyFromArray($style_hprice);
//стили для данных в таблице прайс-листа
$style_price = array(
	'alignment' => array(
		'horizontal' => PHPExcel_STYLE_ALIGNMENT::HORIZONTAL_CENTER,
	)
	

);
$active_sheet->getStyle('B7:E'.($i+6))->applyFromArray($style_price);




header("Content-Type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename='Отчет от $date.xls'");

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');

exit();


?>
