<?php
/**
 * LINE BOT API エンドポイント
 */
require_once('config.php');
require_once('libs/IoTLineBot.php');
require_once('libs/Logger.php');

global $CONFIG;

// ログ設定
$logger = new Logger($CONFIG['LOGGER']);
$logger->printLog("callback start.");

// メッセージ受信処理
$jsonMessages = file_get_contents('php://input');
if ($jsonMessages === false) {
	$this->printLog('file_get_contents failed.');
	return;
}

// Bot処理
$oBot = new IotLineBot($CONFIG);
$oBot->attachLogger($logger);
$oBot->run($jsonMessages);

$logger->printLog("callback end.");
