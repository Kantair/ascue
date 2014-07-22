<?php IncludeCom('PHPexcel/Classes/PHPExcel')?>
<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/tpl/dbdata.php');
$server = $dbHost;
$link = mssql_connect($server, $dbUser, $dbPass);

$selected = mssql_select_db($dbName, $link) or die("Couldn’t open database databasename");

function get_otchet() {

$date_ot_sql=$_COOKIE['date_exp_ot'];
$date_do_sql=$_COOKIE['date_exp_do'];
$item1=$_COOKIE['item1'];
$item2=$_COOKIE['item2'];
$id_object=$_COOKIE['id_object'];

$result = mssql_query("select (a.PAR1-b.PAR2) as VALUE0,Convert(Varchar(50),a.DATA_DATE, 120) as DATA_DATE from (SELECT VALUE0 as PAR1,DATA_DATE FROM DATA where ITEM='$item1' and OBJECT='$id_object' and PARNUMBER=12) a, (SELECT VALUE0 as PAR2,DATA_DATE FROM DATA where ITEM='$item2' and OBJECT='$id_object' and PARNUMBER=12) b where a.DATA_DATE=b.DATA_DATE and (a.DATA_DATE>='$date_ot_sql' and a.DATA_DATE<'$date_do_sql') order by a.DATA_DATE, b.DATA_DATE");

	$row = array();
	for($i = 0;$i < mssql_num_rows($result);$i++) {
		$row[] = mssql_fetch_assoc($result);
	}
	
	return $row;	

}


$otchet_list = get_otchet();

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
$active_sheet->setTitle("Отчет по мощности");	
//Шапа и футер 
$active_sheet->getHeaderFooter()->setOddHeader("&CОтчет по мощности");	
$active_sheet->getHeaderFooter()->setOddFooter("&CОтчет создан с помощью web-интерфейса ГЭП 'ВОКЭ'");
//Настройки шрифта
$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);

$active_sheet->getColumnDimension('A')->setWidth(15);
$active_sheet->getColumnDimension('B')->setWidth(18);
$active_sheet->getColumnDimension('C')->setWidth(15);
$active_sheet->getColumnDimension('D')->setWidth(15);

$active_sheet->mergeCells('A1:D1');
$active_sheet->getRowDimension('1')->setRowHeight(20);
$active_sheet->setCellValue('A1','Отчет по мощности');


//получение имени точки учета
$id_object=$_COOKIE['id_object'];
	$result = mssql_query("select OBJDESCRIPTION from OBJECT where OBJCODE='$id_object' and OBJTYPE=99");
	$row = mssql_fetch_array($result);
	$name = iconv('Windows-1251', 'UTF-8', $row['OBJDESCRIPTION']);//перевод кодировки

$active_sheet->mergeCells('A2:D2');
$active_sheet->setCellValue('A2',$name.' Расчетная точка');

$active_sheet->mergeCells('A3:C3');
$active_sheet->setCellValue('A3','Дата создания отчета');

//Записываем данные в ячейку
$date = date('d-m-Y');
$active_sheet->setCellValue('D3',$date);
//Устанавливает формат данных в ячейке - дата
$active_sheet->getStyle('D3')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX14);


//Создаем шапку таблички данных
$active_sheet->setCellValue('B6','Время');
$active_sheet->setCellValue('C6','кВт');


//В цикле проходимся по элементам массива и выводим все в соответствующие ячейки
$row_start = 7;
$i = 0;
foreach($otchet_list as $item) {
	$row_next = $row_start + $i;
	
	$active_sheet->setCellValue('B'.$row_next,$item['DATA_DATE']);
	$active_sheet->setCellValue('C'.$row_next,$item['VALUE0']);
	
	$i++;
}
$active_sheet->setCellValue('B'.($i+7), 'Итого (кВт*ч):');
$active_sheet->setCellValue('C'.($i+7), '=SUM(C7:C'.($i+6).')/2');

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
$active_sheet->getStyle('B6:C'.($i+6))->applyFromArray($style_wrap);

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

$active_sheet->getStyle('A1:D1')->applyFromArray($style_header);

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

$active_sheet->getStyle('A2:D2')->applyFromArray($style_header2);

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
$active_sheet->getStyle('A3:C3')->applyFromArray($style_tdate);

//Стили для даты
$style_date = array(
	//рамки
	'borders' => array(
		'left' => array(
			'style'=>PHPExcel_Style_Border::BORDER_NONE
		)
	
	),
);
$active_sheet->getStyle('D3')->applyFromArray($style_date);

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
$active_sheet->getStyle('B6:C6')->applyFromArray($style_hprice);
//стили для данных в таблице прайс-листа
$style_price = array(
	'alignment' => array(
		'horizontal' => PHPExcel_STYLE_ALIGNMENT::HORIZONTAL_CENTER,
	)
	

);
$active_sheet->getStyle('B7:C'.($i+6))->applyFromArray($style_price);




header("Content-Type:application/vnd.ms-excel");
header("Content-Disposition:attachment;filename='Отчет от $date.xls'");

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');

exit();


?>
