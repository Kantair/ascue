
<?php //подключение к базе данных и описывание параметров
/*
$server = '192.168.199.8';
$link = mssql_connect($server, 'sa', 'tot@lcontro1');

$selected = mssql_select_db("Piramida2000", $link) or die("Couldn’t open database databasename");

$result = mssql_query("SELECT NAME, FOLDERID, CODE from DEVICES order by FOLDERID");
echo "<ul>";
while ($row=mssql_fetch_array($result)){
echo "<li>";
echo $row["NAME"];
echo "</li>";
}
echo "</ul>";*/
?>


<script>
var show;
function hidetxt(type){
 param=document.getElementById(type);
 if(param.style.display == "none") {
 if(show) show.style.display = "none";
 param.style.display = "block";
 show = param;
 }else param.style.display = "none"
}
</script>

<div>
<a onclick="hidetxt('div1'); return false;" href="#" rel="nofollow">Ссылка 1</a>
<div style="display:none;" id="div1">
<li><a href="#">1</a></li><li>2</li><li>3</li>
</div>
</div>
<div>
<a onclick="hidetxt('div2'); return false;" href="#" rel="nofollow">Ссылка 2</a>
<div style="display:none;" id="div2">
Много много много текста 2
</div>
</div>
<div>
<a onclick="hidetxt('div3'); return false;" href="#" rel="nofollow">Ссылка 3</a>
<div style="display:none;" id="div3">
Много много много текста 3
</div>
</div>
