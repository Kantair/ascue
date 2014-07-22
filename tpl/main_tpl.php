            
<!DOCTYPE html>
<html lang="<?= LANG?>" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?= $g_config['charset']?>" />
        <title><?= $title?></title>
        <?php if (!empty($description)):?><meta name="description" content="<?= $description?>" /><?php endif?>
        <?php if (!empty($keyWords)):?><meta name="keywords" content="<?= $keyWords?>" /><?php endif?>

        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?= Root('i/image/touch_icon/favicon_144x144.png')?>" />
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?= Root('i/image/touch_icon/favicon_114x114.png')?>" />
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?= Root('i/image/touch_icon/favicon_72x72.png')?>" />
        <link rel="apple-touch-icon-precomposed" href="<?= Root('i/image/touch_icon/favicon_57x57.png')?>" />

        <link rel="icon" href="<?= Root('favicon.ico')?>" type="image/x-icon" />
        <link rel="shortcut icon" href="<?= Root('favicon.ico')?>" type="image/x-icon" />

        <meta http-equiv="cleartype" content="on">

        <link rel="stylesheet" type="text/css" href="<?= Root('i/css/normalize.css')?>" />
        <link rel="stylesheet" type="text/css" href="<?= Root('i/css/dev/funcs.css')?>" />
        <!-- extraPacker -->
        <?php IncludeCom('dev/jquery')?>
        <?php IncludeCom("dev/bootstrap3")?>

	<script src="<?= Root('i/js/highcharts.js')?>"></script>
	<script src="<?= Root('i/js/exporting.js')?>"></script>
        <link rel="stylesheet" type="text/css" href="<?= Root('i/css/styles.css')?>" />

    </head>
    <body>
        <div class="header">
            <div class="container">
                <a class="logo" href="<?= SiteRoot()?>">
                <img width="61" hight="61" src="<?= Root('i/image/logo.png')?>" />
                </a>
                <a class="phone" href="http://voce.ru/">
                    ГЭП "Вологдаоблкоммунэнерго"
                </a>
            </div>
        </div>
        <nav class="navbar navbar-default navbar-inverse" role="navigation">
            <div class="container">
                <ul class="nav navbar-nav">
                    <li><a href="<?= SiteRoot()?>">Главная</a></li>
                    <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">Мощность <b class="caret"></b></a>
			<ul class="dropdown-menu">
            			<li><a href="<?= SiteRoot("spower")?>">Мощность за 30 минут</a></li>
            			<li><a href="<?= SiteRoot("calculation")?>">Расчетная величина</a></li>
				<li class="divider"></li>            			
				<li><a href="<?= SiteRoot("shour")?>">Часовые значения</a></li>
            			<li><a href="<?= SiteRoot("prognoz")?>">Прогноз</a></li>
            			<li class="divider"></li>
            			<li><a href="#">Cсылка</a></li>
			</ul>
		    </li>
                    <li><a href="<?= SiteRoot("energy")?>">Энергия</a></li>
                    <li><a href="<?= SiteRoot("contacts")?>">Контакты</a></li>
                </ul>
            </div>
        </nav>

        <div class="container">
            <?= $content?>
        </div>

<div class="navbar-fixed-bottom row-fluid">
      <div class="navbar-inner">
 		<div class="container" align="center">
    			<strong>&copy Ткачев Андрей, ГЭП Вологдаоблкоммунэнерго, 2014</strong>
	        </div>
	</div>
</div>

    </body>
</html>        
