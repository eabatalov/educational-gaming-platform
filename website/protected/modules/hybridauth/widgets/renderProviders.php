<?php

class renderProviders extends CWidget {

        const ACTION_SIGNUP = 'signup';
        const ACTION_LOGIN = 'login';
	public $config;
        public $action; //'signup' or 'login'
	private $_assetsUrl;
	
	public function init() {
		// this method is called by CController::beginWidget()
		$this->config = Yii::app()->getModule('hybridauth')->getConfig();
		$this->_assetsUrl = Yii::app()->getModule('hybridauth')->getAssetsUrl();
                if ($this->action != self::ACTION_LOGIN && $this->action != self::ACTION_SIGNUP)
                    throw new InvalidArgumentException('Action name is invalid!');
	}

	public function run() {
		
		// this method is called by CController::endWidget()
		$cs = Yii::app()->getClientScript();
		
		$cs->registerCoreScript('jquery');
		$cs->registerCoreScript('jquery.ui');
		$cs->registerCssFile($cs->getCoreScriptUrl(). '/jui/css/base/jquery-ui.css'); 
		$cs->registerScriptFile($this->_assetsUrl . '/script.js');
		$cs->registerCssFile($this->_assetsUrl . '/styles.css');
		$providers = $this->config['providers'];
		
		foreach ($providers as $key => &$provider) {
			$provider['active']=false;
                        if ($this->action == self::ACTION_SIGNUP && !$provider['showOnSignup'])
                            unset($providers[$key]);
		}
		if (!Yii::app()->user->isGuest) {
                        $hauthStorage = new PostgresHybridAuthStorage();
			foreach ($hauthStorage->getUserHAuthRecords(Yii::app()->user->id) as $hauthRecord) {
				$providers[$hauthRecord->getLoginProviderName()]['active']=true;
			}
		}
		$this->render('providers', array(
			'baseUrl'=>$this->config['baseUrl'],
			'providers' => $providers,
			'assetsUrl' =>  $this->_assetsUrl,
                        'action'    => $this->action
		));
	}
}