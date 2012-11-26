<!DOCTYPE html>
<?php /* @var $e Exception */ ?>
<?php /* @var $app \KumbiaPHP\Kernel\AppContext */ ?>
<?php

function MakePrettyException(Exception $e)
{
$trace = $e->getTrace();

$result = 'Exception: "';
$result .= $e->getMessage();
$result .= '" @ ';
if ($trace[0]['class'] != '') {
$result .= $trace[0]['class'];
$result .= '->';
}
$result .= $trace[0]['function'];
$result .= '();<br />';

return $result;
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Ha Ocurrido una Excepción</title>
<link href="<?php echo $app->getBaseUrl() ?>css/exception.css" rel="stylesheet" type="text/css" media="screen" />
<link href="<?php echo $app->getBaseUrl() ?>css/structure.css" rel="stylesheet" type="text/css" media="screen" />
</head>
<body>
<div class="exception">
<div class="error flash round">
<p><strong>
<?php echo basename(get_class($e)) ?>: <?php echo "{$e->getMessage()} ({$e->getCode() })" ?>
</strong></p>
<p>En el archivo <em><?php echo htmlspecialchars($e->getFile(), ENT_NOQUOTES, 'UTF-8') ?></em> en la línea: <em><?php echo $e->getLine() ?></em></p>
</div>
<div class="exception_information">
<h2>Rastro</h2>
<?php foreach ($e->getTrace() as $trace): ?>
<?php if (isset($trace ['file']) && !(strpos($trace ['file'], $app->getAppPath()) === false) && !(strpos($trace ['file'], 'index.php'))): ?>
<p><strong><?php echo htmlspecialchars($trace['file'], ENT_NOQUOTES, 'UTF-8') . "(" . $trace ['line'] . ")" ?></strong></p>
<p>La excepción se ha generado en el archivo <span class="italic"><?php echo $trace['file'] ?></span> en la línea: <span class="italic"><?php echo $trace['line'] ?></span>:</p>

<ul class="exception_trace">
<?php
$lines = file($trace ['file']);
$start = ($trace ['line'] - 4) < 0 ? 0 : $trace ['line'] - 4;
$end = ($trace ['line'] + 2) > count($lines) - 1 ? count($lines) - 1 : $trace ['line'] + 2;
?>

<?php for ($i = $start; $i <= $end; $i++): ?>
<li <?php if ($i == $trace ['line'] - 1): ?> class="exception_highlight" <?php endif; ?>">
<strong><?php echo ($i + 1) ?></strong> <?php echo htmlspecialchars($lines [$i], ENT_NOQUOTES, 'UTF-8') ?>

</li>
<?php endfor; ?>
</ul>

<br />
<?php endif; ?>
<?php endforeach; ?>

<table><thead><tr>
<th>#</th><th>Fichero (línea)</th><th>Función</th>
</tr></thead>
<tbody>
<?php
$lines = explode("\n", $e->getTraceAsString());
$files = get_included_files();
foreach ($lines as $line) {
$line = explode(" ", $line);
?>
<tr><td><?php echo $line[0] ?></td><td><?php echo $line[1] ?></td><td><?php if (isset($line[2])) echo $line[2] ?></td></tr>
<?php } ?>
</tbody>
</table>
</div>

<div class="exception_information">
<h2>Información Adicional</h2>
<strong>En producción:</strong> <?php echo $app->InProduction() ? 'Sí' : 'No' ?><br />
<strong>Ubicación actual:</strong> <?php echo $app->getCurrentUrl() ?><br />
<strong>Server: </strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?><br />
<strong>IP Server: </strong> <?php echo $_SERVER['SERVER_ADDR'] ?><br />
<strong>IP Visitante: </strong> <?php echo $_SERVER['REMOTE_ADDR'] ?><br />
</div>

<table><thead><tr>
<th><h3><?php echo round((microtime(1)-START_TIME),4),' seg.'?></h3>Tiempo</th>
<th><h3><?php echo number_format(memory_get_usage() / 1048576, 2), ' MB'; ?></h3>Memoria Usada</th>
<th><h3><?php echo count($files) ?> ficheros</h3>Includes</th>
</tr>
<tr>
<th><h3><?php echo ini_get('max_execution_time'),' seg.'?></h3>Tiempo Máximo PHP</th>
<th><h3><?php echo ini_get('memory_limit'); ?></h3>Memoria PHP</th>
<th><h3><?php echo PHP_VERSION ?></h3>Versión PHP</th>
</tr>
</thead>
</table>
</div>
</body>
</html>
