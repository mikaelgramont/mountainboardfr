<?php
class Lib_View_Helper_Chat extends Zend_View_Helper_Abstract
{
	public function chat()
	{
		if(!CHAT_ENABLED){
			//return;
		}

		$cookie = isset($_COOKIE['PHPSESSID']) ? $_COOKIE['PHPSESSID'] : null;
		if(empty($cookie)){
			Globals::getLogger()->chat("No php session cookie found, aborting chat");
			return;
		}

		$chatOpen = 0;
		$loader = $this->view->asset()->image('ajax-loader.gif');
		$content = <<<HTML
	<div id="activityBar">
		<div id="chat">
			<div id="chatMenu">
				<a id="chatToggle" class="chatInit" href="javascript:void(0)">Chat</a>
				<a id="chatExit" href="javascript:void(0)" title="Exit"></a>
				<a id="chatOpen" href="javascript:void(0)" title="Open"></a>
			</div>
			<div id="chatContainer">
				<div id="chatLoader">
					<img src="$loader" alt="" width="16" height="16" /> Loading...
				</div>
				<div id="chatContent">
					<ul id="chatConversation"></ul>
					<div id="chatInputs">
						<form action="." id="chatForm" name="chatForm" method="POST">
							<input type="text"  value="" id="chatMessage" />
							<input type="submit" value="go" id="chatSubmit" />
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
HTML;

		$path = SCRIPTS_PATH;
		$domain = CHAT_DOMAIN_FULL;
		$port = CHAT_PORT;
		$joinNotification = $this->view->translate('chatJoinNotification');
		$leaveNotification = $this->view->translate('chatLeaveNotification');

		$initialState = $chatOpen ? 'true' : 'false';
		$js = <<<JS
	Lib.Chat.init({path:'$path', host:'$domain', port: '$port', join: '$joinNotification', leave: '$leaveNotification'}, $initialState);

JS;
		$this->view->jQuery()->addOnLoad($js)
							 ->addJavascriptFile($this->view->asset()->script('yepnope.js'))
							 ->addJavascriptFile($this->view->asset()->script('libChat.js'))
							 ->addJavascriptFile($this->view->asset()->script('jquery.cookie.js'));

		return $content;
	}
}
