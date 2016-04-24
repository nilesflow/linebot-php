<?php
/**
 * セットアップ
 */
require_once('config.php');

global $CONFIG;

// ディレクトリ生成
$path = $CONFIG['LOGGER']['PATH'];
if (! is_dir($path)) {
	$ret = mkdir($path, 0777, TRUE);
	if (!$ret) {
		echo 'mkdir failed. path:'.$path."\n";
	}
	chmod($path, 0777);
}

// ディレクトリ生成
$path = $CONFIG['LINEBOT_CONFIG']['PATH_DATA'];
if (! is_dir($path)) {
	$ret = mkdir($path, 0777, TRUE);
	if (!$ret) {
		echo 'mkdir failed. path:'.$path."\n";
	}
	chmod($path, 0777);
}

// コンフィグ生成
$path = 'config.php';
if (! file_exists($path)) {
	$ret = copy('config-default.php', 'config.php');
	if (!$ret) {
		echo 'creating config.php failed. path:'.$path."\n";
	}
}
else {
	echo 'config.php already has created.';
}