<?php
include_once __DIR__ . "/Exporter.inc";
$x = new Exporter();
$export_type = $_POST['export_type'];
$db_values = $_POST['db_values'];
$formName = $_POST['formName'];
$x->setExportHeader($export_type, $formName);
echo $db_values;
