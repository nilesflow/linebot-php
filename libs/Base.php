<?php

/**
 * 基底クラス
 * 
 * ログ出力を管理
 */
class Base {
	protected $logger = null;

	/**
	 * ロガーインスタンス
	 * @param object $logger ロガークラスインスタンス
	 */
	public function attachLogger($logger) {
		$this->logger = $logger;
	}

	/**
	 * ログ出力
	 * @param mixed $mixed 出力メッセージ
	 */
	public function printLog($mixed) {
		if (is_null($this->logger)) {
			return;
		}
		$this->logger->printLog($mixed);
	}
}
