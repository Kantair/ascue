<?php
$date_ot_cal=date('Y-m-d',strtotime('-1 day'));
$date_do_cal=date('Y-m-d');

if (isset($_POST["submit"])){  
        $date_ot_cal=$_POST['date_ot']; 
	$date_do_cal=$_POST['date_do'];
}  
?>

<?php //подключение к базе данных и описывание параметров
require_once($_SERVER['DOCUMENT_ROOT'].'/tpl/dbdata.php');

$server = $dbHost;
$link = mssql_connect($server, $dbUser, $dbPass);

$selected = mssql_select_db($dbName, $link) or die("Couldn’t open database databasename");

$item='1';
$id_object='4417';

$date_ot_sql=$date_ot_cal.' 00:00:00.000';
$date_do_sql=$date_do_cal.' 00:00:00.000';
?>



<link href="<?= Root('i/css/datetimepicker/bootstrap-combined.min.css')?>" rel="stylesheet">
    <link rel="stylesheet" type="text/css" media="screen" href="<?= Root('i/css/datetimepicker/bootstrap-datetimepicker.min.css')?>">
<link rel="stylesheet" type="text/css" href="<?= Root('i/css/tabl.css')?>" />


<div class="row">
<div class="col-md-6">
<b>
	<?php //получение имени точки учета
	$result = mssql_query("select OBJDESCRIPTION from OBJECT where OBJCODE='$id_object' and OBJTYPE=99");
	while($row = mssql_fetch_array($result)){
	$name=$row['OBJDESCRIPTION'];
	$name = iconv('Windows-1251', 'UTF-8', $name);//перевод кодировки
	echo $name;
	}
	?>
</b><br>
Счетчик: <i> 
	<?php //получение имени счетчика
	$result = mssql_query("select NAME from TYPESDEV where ID = (select TDEVICE from DEVICES where CODE='$id_object')");
	while($row = mssql_fetch_array($result)){
	$counter=$row['NAME'];
	$counter = strstr($counter, " ");//вывод строки после первого пробела
	$counter = iconv('Windows-1251', 'UTF-8', $counter);//перевод кодировки
	echo $counter;
	}
	?>
</i><br>
Заводской номер: <i>
	<?php
	$result = mssql_query("select CUSTOMDATA from DEVICES where CODE='$id_object'");
	while($row = mssql_fetch_array($result)){
	$serial=$row['CUSTOMDATA'];
	$poz = strlen($serial)-strpos($serial, "NUM=")-4;//вычисление позиции
	$serial=substr($serial,-$poz)-";";//удаление последнего символа
	echo $serial;
	}
	?>
</i>
</div>

<div class="col-md-6">
<form action="" method="post">  
<div style="height:75px;">
<b>Начало:</b>
 <div id="datetimepicker" class="input-append date">
        <input type="text" name="date_ot" value="<?=$date_ot_cal?>" readonly></input>
      <span class="add-on">
        <i data-date-icon="icon-calendar"><img width='25' height='25' src="<?= Root('i/image/calendar.png')?>"></i>
      </span>
    </div>
<b>Окончание:</b>
 <div id="datetimepicker2" class="input-append date">
      <input type="text" name="date_do" value="<?=$date_do_cal?>" readonly></input>
      <span class="add-on">
        <i data-date-icon="icon-calendar"><img width='25' height='25' src="<?= Root('i/image/calendar.png')?>"></i>
      </span>
    </div>
</div>

<br>
<input type="submit" name="submit" class="btn btn-primary" value="Применить"><br> 
</form>
</div>
</div>
    <script type="text/javascript" src="<?= Root('i/js/datetimepicker/bootstrap-datetimepicker.min.js')?>">
    </script>
<script type="text/javascript" src="<?= Root('i/js/datetimepicker/bootstrap-datetimepicker.ru.js')?>">
    </script>

    <script type="text/javascript">

$.fn.datetimepicker.defaults = {
  pickDate: true,            // disables the date picker
  pickTime: false            // disables de time picker
};
      $('#datetimepicker').datetimepicker({
        format: 'yyyy-MM-dd',
	language: 'ru'
      });
      $('#datetimepicker2').datetimepicker({
        format: 'yyyy-MM-dd',
	language: 'ru'
      });

    </script>

<?php
//опрос по одному каналу счетчика Нестле ввод1 для графика
$result = mssql_query("SELECT STR(value0,8,3) as VALUE0, timestamp = DATEDIFF(s, '19700101', DATA_DATE) FROM DATA where ITEM='$item' and OBJECT='$id_object' and PARNUMBER=12 and (DATA_DATE>'$date_ot_sql' and DATA_DATE<='$date_do_sql') order by DATA_DATE");

$result1 = mssql_query("select timestamp = DATEDIFF(s, '19700101', DATA_DATE), STR(VALUE0,8,3) as VALUE0 from DATA where ITEM='$item' and OBJECT='$id_object' and PARNUMBER=218 and (DATA_DATE>'$date_ot_sql' and DATA_DATE<='$date_do_sql') order by DATA_DATE");

$val='';
$prog='';
$time='';
$time1='';
while($row1 = mssql_fetch_array($result1)){
 if ($prog) {$prog.=', ';$time1.=', ';}
    $prog.=$row1['VALUE0'];
    $time1.=$row1['timestamp'].'000';
}
$time1 = explode(",",$time1);//преобразование в массив
$prog='['.$prog.']'; 
$arr1 = explode(",",$time1[0]);

while($row = mssql_fetch_array($result)){
if ($val) {$val.=', ';$time.=', ';}
    $val.=$row['VALUE0'];
    $time.=$row['timestamp'].'000';
}
$val = explode(",",$val);//преобразование в массив
$time = explode(",",$time);//преобразование в массив
 end($val);
$par1='';
for ($i=0;$i<key($val);$i++) {
    $par1.=($val[$i]+$val[$i+1])/2;
    $par1.=', ';
$i++;
}
$par1='['.$par1.']'; 
$arr = explode(",",$time[1]);

?>

		<script type="text/javascript">
$(function () {
	var d1 = <?=$par1?>;
	var d2 = <?=$arr[0]?>;
	var d3 = <?=$prog?>;
	var d4 = <?=$arr1[0]?>;
        $('#grcontainer').highcharts({
            chart: {
                zoomType: 'x'
            },
            title: {
                text: 'Энергия за час'
            },
            xAxis: {
		title: {		
			text: 'Время'
		},
                type: 'datetime',
                minRange: 3600000 // one hour
            },
            yAxis: {
                title: {
                    text: 'кВт*ч'
                }
            },
            legend: {
                enabled: false
            },
            plotOptions: {
                area: {
                    fillColor: {
                        linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1},
                        stops: [
                            [0, Highcharts.getOptions().colors[0]],
                            [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                        ]
                    },
                    marker: {
                        radius: 2
                    },
                    lineWidth: 1,
                    states: {
                        hover: {
                            lineWidth: 1
                        }
                    },
                    threshold: null
                }
            },
    
            series: [{
                type: 'column', //area, bar, column, line, pie
                name: 'кВт*ч',
                pointInterval: 3600000,
                pointStart: d2,
                data: d1
            }, {
                type: 'column', //area, bar, column, line, pie
                name: 'прогноз',
                pointInterval: 3600000,
                pointStart: d4,
                data: d3
            }]
        });
    });
		</script>


<div id="grcontainer" style="min-width: 310px; height: 400px; margin: 0 auto"></div>



﻿<?php 
//опрос по одному каналу счетчика Нестле ввод1
$result = mssql_query("select Convert(Varchar(50),DATA_DATE, 120) as DATA_DATE, STR(VALUE0,8,3) as VALUE0 from DATA where ITEM='$item' and OBJECT='$id_object' and PARNUMBER=12 and (DATA_DATE>'$date_ot_sql' and DATA_DATE<='$date_do_sql') order by DATA_DATE");

$val='';
$time='';

while($row = mssql_fetch_array($result)){
if ($val) {$val.=', ';$time.=', ';}
    $val.=$row['VALUE0'];
    $time.=$row['DATA_DATE'];
}

$val = explode(",",$val);//преобразование в массив
$time = explode(",",$time);//преобразование в массив
end($val);

echo "<div class='col-md-3'><table class='s-table'  border=1 cellpadding=7>\n";
echo "<tr><th>Время</th><th>кВт</th></tr>";
for ($i=0;$i<key($val)/2;$i++) {
$par=($val[$i]+$val[$i+1])/2;
echo "\t<tr border=1>\n";
echo "\t\t<td border=1>" . $time[$i+1] . "</td>\n";
echo "\t\t<td border=1>" . $par . "</td>\n";
echo "\t</tr>\n";
$i++;}
echo "</table>\n";  
echo "<br><br><br>"; 
echo "</div>\n"; 


echo "<div class='col-md-3'><table class='s-table'  border=1 cellpadding=7>\n";
echo "<tr><th>Время</th><th>кВт</th></tr>";
for ($i=$i;$i<key($val);$i++) {
$par=($val[$i]+$val[$i+1])/2;
echo "\t<tr border=1>\n";
echo "\t\t<td border=1>" . $time[$i+1] . "</td>\n";
echo "\t\t<td border=1>" . $par . "</td>\n";
echo "\t</tr>\n";
$i++;}

$result1 = mssql_query("select SUM(VALUE0)/2 as summa from DATA where ITEM='$item' and OBJECT='$id_object' and PARNUMBER=12 and (DATA_DATE>='$date_ot_sql' and DATA_DATE<'$date_do_sql')");
$row1 = mssql_fetch_array($result1);
echo "\t<tr border=1>\n";
echo "\t\t<td border=1>Итого, кВт*ч</td>\n";
echo "\t\t<td border=1>" . $row1["summa"] . "</td>\n";
echo "\t</tr>\n";
echo "</table>\n";  
echo "<br><br><br>"; 
echo "</div>\n";
?>

<?php
echo "<div class='col-md-3'><table class='s-table'  border=1 cellpadding=7>\n";
echo "<tr><th>Энергия</th><th>кВт*ч</th></tr>";
$y=date('Y');
$m=date('n');
$d=date('j');
$result = mssql_query("select SUM(VALUE0)/2 as summa from DATA where ITEM='$item' and OBJECT='$id_object' and PARNUMBER=12 and Year(DATA_DATE)=$y and Month(DATA_DATE)=$m and Day(DATA_DATE)=$d");
$row = mssql_fetch_array($result);
echo "\t<tr border=1>\n";
echo "\t\t<td border=1>За текущий день</td>\n";
echo "\t\t<td border=1>" . $row["summa"] . "</td>\n";
echo "\t</tr>\n";
$result = mssql_query("select SUM(VALUE0)/2 as summa from DATA where ITEM='$item' and OBJECT='$id_object' and PARNUMBER=12 and Year(DATA_DATE)=$y and Month(DATA_DATE)=$m and Day(DATA_DATE)=($d-1)");
$row = mssql_fetch_array($result);
echo "\t<tr border=1>\n";
echo "\t\t<td border=1>За предыдущий день</td>\n";
echo "\t\t<td border=1>" . $row["summa"] . "</td>\n";
echo "\t</tr>\n";
$result = mssql_query("select SUM(VALUE0)/2 as summa from DATA where ITEM='$item' and OBJECT='$id_object' and PARNUMBER=12 and Year(DATA_DATE)=$y and Month(DATA_DATE)=$m");
$row = mssql_fetch_array($result);
echo "\t<tr border=1>\n";
echo "\t\t<td border=1>За текущий месяц</td>\n";
echo "\t\t<td border=1>" . $row["summa"] . "</td>\n";
echo "\t</tr>\n";
$result = mssql_query("select SUM(VALUE0)/2 as summa from DATA where ITEM='$item' and OBJECT='$id_object' and PARNUMBER=12 and Year(DATA_DATE)=$y and Month(DATA_DATE)=($m-1)");
$row = mssql_fetch_array($result);
echo "\t<tr border=1>\n";
echo "\t\t<td border=1>За предыдущий месяц</td>\n";
echo "\t\t<td border=1>" . $row["summa"] . "</td>\n";
echo "\t</tr>\n";
echo "</table>\n";  
echo "<br><br><br>"; 
echo "</div>\n";
?>


<form action="" method="post">  
<?php
$date_exp_ot=$date_ot_sql;
$date_exp_do=$date_do_sql;
setcookie("date_exp_ot", $date_exp_ot, time() + 60); 
setcookie("date_exp_do", $date_exp_do, time() + 60); 
setcookie("item", $item, time() + 60); 
setcookie("id_object", $id_object, time() + 60); 
?>
<input type="submit" name="export" class="btn btn-primary" value="Вывести отчет" onclick="window.open('?q=export/exportprog')"><br> 
</form>






