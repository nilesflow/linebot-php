<?php
require_once(__DIR__ .'/LineBot.php');

/**
 * Iotの現在状態を通知するBot
 */
class IotLineBot extends LineBot {
	protected $aDataIot = null;	// データリソース
	protected $aContentsIot = array();
	
	/**
	 * コンストラクタ
	 * 
	 * @param array $config
	 */
	public function __construct($config) {
		parent::__construct($config);

		// データ情報、configを保持
		$this->aDataIot = $config['IOTLINEBOT_DATA'];
	}

	/**
	 * BOTが使用するリソースの準備
	 *
	 * @override
	 */
	protected function makeResources() {
		parent::makeResources();

		// メッセージコンテンツ生成
		$resource = $this->pickResource($this->aDataIot['image']);
		$this->aContentsIot['image'] = $this->oApi->makeImageContent($resource);
	}

	/**
	 * メッセージを返信、受信したメッセージに応じて
	 * 
	 * @override
	 */
	protected function chatMessage($to, $input) {
		// 親クラスの処理を優先
		$ret = parent::chatMessage($to, $input);
		if ($ret !== false) {
			// 親で処理してたら終了
			return $ret;
		}

		switch ($input) {
			case "計測値":
				$now = date('Y/m/d h:i:s');
				$tem = rand(10, 30);
				$hum = rand(30, 50);

				$text = "最新計測日時：$now\n温度：{$tem}℃\n湿度：$hum%";
				$ret = $this->replyMessage($to, $text);
				break;
			case "最新画像":
				$content = $this->aContentsIot['image'];
				$ret = $this->oApi->sendMessage($to, $content);
				break;
			case "位置":
				$content = $this->aContents['location'];
				$ret = $this->oApi->sendMessage($to, $content);
				break;
			default:
				// 上記以外はusage送信
				$text = "知りたいことはなんですか？\n現在の状態をお伝えします。次の中から聞いてください。\n計測値\n最新画像\n位置";
				$ret = $this->replyMessage($to, $text);
				break;
		}
		
		// メッセージ送信
		return $ret;
	}
}
