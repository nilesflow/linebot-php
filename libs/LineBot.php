<?php
require_once(__DIR__ .'/Base.php');
require_once(__DIR__ .'/LineBotApi.php');

/**
 * LINE Bot 共通クラス
 */
class LineBot extends Base {
	protected $config = null;	// コンフィグ
	protected $aData = null;	// データリソース
	protected $oApi = null;		// API

	protected $aContents = array(); // コンテンツ

	/**
	 * コンストラクタ
	 * @param array $config
	 */
	public function __construct($config) {
		// データ情報、configを保持
		$this->aData = $config['LINEBOT_DATA'];
		$this->config = $config['LINEBOT_CONFIG'];

		// LINE BOT APIインスタンス
		$this->oApi = new LineBotApi($config['LINEBOT_API']);
	}

	/**
	 * ロガーインスタンス
	 * 
	 * @api
	 * @override
	 * @param object $logger ロガークラスインスタンス
	 */
	public function attachLogger($logger) {
		parent::attachLogger($logger);
		$this->oApi->attachLogger($logger);
	}


	/**
	 * Bot処理
	 * 以下の処理を行う。
	 * ・Receiving messages
	 * ・Receiving operaions
	 * 
	 * @api
	 * @param json $messages
	 */
	public function run($messages){
		// コンテンツ準備
		$this->makeResources();
		
		// メッセージ解析処理
		$results = $this->oApi->receiveMessages($messages);
		if (! $results) {
			// 失敗した場合は、処理中断。
			$this->printLog('Receiving Message faild');
			return;
		}
	
		// メッセージ受信に対する操作
		foreach ($results as $result) {
			if (! isset($result['content'])) {
				$logger->printLog('content none.');
				continue;
			}
	
			// 1件ごとに処理
			$this->receivedMessage($result['content']);
		}
	}

	/**
	 * データを配列からランダムに抽出
	 * 
	 * @internal
	 * @param array $arr データ配列
	 */
	protected function pickResource($arr) {
		$max = count($arr) - 1;
		return $arr[rand(0, $max)];
	}

	/**
	 * BOTが使用するリソースの準備
	 * 
	 * @internal
	 */
	protected function makeResources() {
		// メッセージコンテンツ生成
		$resource = $this->pickResource($this->aData['image']);
		$this->aContents['image'] = $this->oApi->makeImageContent($resource);
		
		$resource = $this->pickResource($this->aData['video']);
		$this->aContents['video'] = $this->oApi->makeVideoContent($resource);
		
		$resource = $this->pickResource($this->aData['audio']);
		$this->aContents['audio'] = $this->oApi->makeAudioContent($resource);
		
		$resources = $this->pickResource($this->aData['location']);
		$this->aContents['location'] = $this->oApi->makeLocationContent($resources);
		
		$resource = $this->pickResource($this->aData['sticker']);
		$this->aContents['sticker'] = $this->oApi->makeStickerContent($resource);
		
		$resource = $this->pickResource($this->aData['rich']);
		$this->aContents['rich'] = $this->oApi->makeRichContent($resource);
	}

	/**
	 * メッセージ受信に対する処理
	 * メッセージに応じて、Sending messages
	 * 
	 * @internal
	 * @param array $content
	 */
	protected function receivedMessage($content) {
		$from			= $content['from'];
		$messageId 		= $content['id'];
		$text			= $content['text'];
		$contentType	= $content['contentType'];

		// ユーザ情報取得
		$ret = $this->oApi->getUserProfile($from);
		$this->printLog('user profile information');
		$this->printLog($ret);

		// メッセージがPreview情報取得可能なら、取得
		if ($this->oApi->canPreviewedType($contentType)) {
			$ret = $this->saveContent($messageId, true);
			if ($ret !== false) {
				;
			}
		}
		
		// メッセージが保存可能なものであれば保存して終わり。
		if ($this->oApi->canSavedType($contentType)) {
			$ret = $this->saveContent($messageId);
			if ($ret !== false) {
				$text = "画像を保存しました。";
				$ret = $this->replyMessage($from, $text);
			}
		}

		// コンテンツタイプに応じた処理
		switch($contentType) {
			// テキスト
			case LineBotApi::CONTENTTYPE_TEXT:
				// 応答
				$this->chatMessage($from, $text);
				break;
			// スタンプ
			case LineBotApi::CONTENTTYPE_STICKER:
				// スタンプを返信
				$resource = $this->pickResource($this->aData['sticker']);
				$resource['STKID'] = rand(1, 15); // 有効範囲がわからない 
				$content = $this->oApi->makeStickerContent($resource);
				$ret = $this->oApi->sendMessage($from, $content);
				break;
			default:
				break;
		}
	}

	
	/**
	 * 送信されたコンテンツを取得し、保存
	 * コンフィグで有効な保存ディレクトリが指定されていなければ、保存しない。
	 * 
	 * @internal
	 * @param string $messageId
	 * @param bool $isPreview true:プレビュー画像
	 * @return bool|integer
	 */
	protected function saveContent($messageId, $isPreview = false) {
		// 保存先ディレクトリのチェック
		if (! isset($this->config['DIR_DATA'])) {
			return false;
		}
		if (! is_dir($this->config['DIR_DATA'])) {
			return false;
		}

		$pathSave = $this->config['DIR_DATA']."/{$messageId}";
		// コンテンツ取得
		if ($isPreview) {
			$output = $this->oApi->getMessageContentPreviews($messageId);
			$pathSave .= "-preview";
		}
		else {
			$output = $this->oApi->getMessageContent($messageId);
		}
		if (! $output) {
			return false;
		}

		// 結果を保存
		$ret = file_put_contents($pathSave, $output);
		if (!$ret) {
			$this->printLog('saving file failed.　'.$messageId);
		}
		return $ret;
	}

	/**
	 * 指定したテキストを応答
	 * 
	 * @param string $to
	 * @param string $output
	 * @return bool|array false or 取得結果
	 */
	protected function replyMessage($to, $output) {
		$resouces = array(
			'text' => $output
		);
		$content = $this->oApi->makeTextContent($resouces);
		
		// メッセージ送信
		return $this->oApi->sendMessage($to, $content);
	}
	
	/**
	 * メッセージを返信、受信したメッセージに応じて
	 *
	 * @internal
	 * @param string $to
	 * @param string $input
	 * @return bool|array false or 取得結果
	 */
	protected function chatMessage($to, $input) {
		$ret = false;

		// 受信メッセージに応じて返すメッセージを変更
		switch ($input) {
			case 'image':
			case 'video':
			case 'audio':
			case 'location':
			case 'sticker':
			case 'rich':
				// 生成済みのコンテンツを指定
				$content = $this->aContents[$input];
		
				// Sending messages
				$ret = $this->oApi->sendMessage($to, $content);
				break;
			case 'multi':
				// マルチコンテンツ生成
				$messages = array(
					$this->aContents['audio'],
					$this->aContents['video'],
					$this->aContents['audio'],
					$this->aContents['location'],
					$this->aContents['sticker'],
					$this->aContents['rich'],
				);

				// Sending multiple messages
				$ret = $this->oApi->sendMultipleMessages($to, $messages);
				break;
			default:
				break;
		}

		// 処理有無を返却
		return $ret;
	}
}