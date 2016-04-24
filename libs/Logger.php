<?php

/**
 * ログ出力制御
 */
class Logger {
	protected $filename = null;

	/**
	 * コンストラクタ
	 * 
	 * @api
	 * @param string $filename 出力ファイル名
	 */
	public function __construct($config) {
		if (!isset($config['VALID'])) {
			return;
		}
		if (!$config['VALID']) {
			return;
		}
		$this->isValid = true;
		
		$this->filename = $config['DIR'].'/'.$config['FILENAME'];
	}

	/**
	 * ログ出力
	 * 
	 * @api
	 * @param mixed $mixed 出力対象
	 */
	public function printLog($mixed) {
		if (!$this->isValid) {
			return;
		}
		if (is_array($mixed) || is_object($mixed)) {
			$mixed = print_r($mixed, true);
		}
		$mixed .= "\n";
		file_put_contents($this->filename, $mixed, FILE_APPEND);
	}
}
