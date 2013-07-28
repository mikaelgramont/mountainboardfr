var Lib = Lib || { };
Lib.Chat = {
	states: ['closed', 'loadingDeps', 'loadingContent', 'ready', 'paused', 'failure'],
		
	currentState: null,
	
	defaultOptions: {
		path: '/',
		protocol: 'http',
		host: 'www.mountainboard.fr',
		port: '8080',
		socketIo: 'socket.io.js',
		join: 'has joined the chatroom.',
		leave: 'has left the chatroom.',
	},
	
	options: {},
	
	depsLoaded: false,
	
	initialContentLoaded: false,
	
	messageBuffer: [],
	
	socket: null,
	
	useLocalStorage: false,
	
	debug: function(msg, msg2){
		if (!this.options.debug){
			return;
		}
		if(typeof(console) != 'undefined') {
			if(typeof(msg2) == 'undefined') {
				console.log(msg);
			} else {
				console.log(msg, msg2);
			}
		}
	},
	
	init: function(userOptions, initiallyOpen) {
        
		// taken from Modernizr v1.8pre
		try {
        	this.useLocalStorage = !!localStorage.getItem;
        } catch(e) {
        	this.useLocalStorage = false;
        }
		 
		this.debug('session:', $.cookie('PHPSESSID'));
		this.options = $.extend(this.defaultOptions, userOptions);
		
		if(this.useLocalStorage){
			initiallyOpen = localStorage.getItem('chatStatus') == 'open';
		}
		
		if(initiallyOpen){
			this.moveToStateLoadingDeps();
		} else {
			this.moveToStateClosed();
		}
		
		$('#chatMessage').attr('autocomplete', 'off');
		this.setupHandlers();
	},
	
	setupHandlers: function() {
		var me = this;
		$('#chatOpen').click(function(){
			me.loadChat();
			return false;
		});
		
		$('#chatExit').click(function(){
			me.moveToStateClosed();
			return false;
		});
		
		$('#chatToggle').click(function(){
			switch($(this).attr('class')){
				case 'chatMin':
					me.moveToStatePaused();
					break;
				case 'chatMax':
					me.moveToStateReady();
					break;
				default:
					me.loadChat();
					break;
			}
		});
		
		$('#chatForm').submit(function(){
			me.sendMessage();
			return false;
		});
		
		$('#chatSubmit').click(function(){
			me.sendMessage();
			return false;
		});
	},
	
	setupConnection: function(){
		WEB_SOCKET_SWF_LOCATION = 'http://www.redesign-zend.fr/js/WebSocketMain.swf';
		
		var self = this,
			socket = this.socket = new io.Socket(null, {rememberTransport: true, port: self.options.port, transports: ['websocket', 'flashsocket', 'htmlfile', 'xhr-multipart', 'xhr-polling',

				'jsonp-polling']});
		
		self.debug('-setupConnection');
        
		socket.on('connect', function(){
        	self.moveToStateLoadingContent();
        });
   
        socket.on('message', function(message){
        	self.debug('message', message);
        	if(message.type == 'userInfo'){
        		// called after user data was received
        		self.moveToStateReady(message.name);
        	} else if (message.type == 'userInfoError') {
        		// called after user data failed
        		self.debug('* connection failed *');
        		self.moveToStateConnectionFailure();
        	} else {
        		self.saveMessageToBuffer(message);
        	}
        });
   
        socket.on('disconnect', function(client){ 
        	// on disconnect, broadcast it to the server
        	// client.broadcast({ announcement: client.sessionId + '
			// disconnected' });
        });
        
        this.connect();
	},
	
	connect: function() {
		this.debug('-connect');
		this.socket.connect();
	},
	
	saveMessageToBuffer: function(message){
		this.messageBuffer.push(message);
    	if(this.messageBuffer.length > 15) {
    		this.messageBuffer.shift();
    	}
    	
    	this.debug('messageBuffer after write', this.messageBuffer);
    	localStorage.setItem('chatMessages', JSON.stringify(this.messageBuffer));
    	this.debug('chatMessages after write', JSON.parse(localStorage.getItem('chatMessages')));
    	
    	this.appendMessage(message);
    	
	},

    sendMessage: function (message){
		if(!message){
  	  		var msg = $("input#chatMessage").val(); 
  	  		$("input#chatMessage").val('');
		} else {
			var msg = message; 
		}
		
		if(msg.length > 0) {
			this.socket.send({text: msg});
		}
		
		$('#chatMessage').focus();
	},
	
	appendMessage: function(message){
		var content,
			date = new Date(message.time);
		switch(message.type){
			case 'joined':
				content = this.options.join;
				break;
			case 'left':
				content = this.options.leave;
				break;
			default:
				content = message.text;
				break;
		}
		
		
		var dateString = date.toLocaleDateString() + ' @ ' + date.toLocaleTimeString();
		$('#chatConversation').append('<li class="' + message.type + '">' + ' <a target="_blank" title="' + dateString + '" class="from" href="' + message.from.link + '">' + message.from.name + '</a><span class="msg">' + content + '</span></li>');
		var c = document.getElementById("chatConversation");
		c.scrollTop = c.scrollHeight;
	},
	
	clearMessages: function(){
		$('#chatConversation').empty();
	},
	
	loadChat: function(){
		this.debug('-loadChat');
		if(!this.depsLoaded){
			this.moveToStateLoadingDeps();
		} else {
			this.connect();
		}					
	},
	
	moveToStateClosed: function() {
		this.debug('-moveToStateClosed');
		if(this.socket != null){
			this.socket.disconnect();
		}
		
		this.currentState = this.states['closed'];
		this.drawClosed();
		this.clearMessages();
		
		localStorage.setItem('chatStatus', 'closed');
	},
	
	moveToStateLoadingDeps: function(){
		this.debug('-moveToStateLoadingDeps');
		var me = this;
		this.currentState = this.states['loadingDeps'];
		this.drawLoading();
		
		yepnope([{
			load: [this.options.protocol + '://' + this.options.host + '/' + this.options.path + this.options.socketIo],
			callback: function(){
				me.depsLoaded = true;
				me.setupConnection();
			}
		}]);
	},
		
	moveToStateLoadingContent: function() {
		this.debug('-moveToStateLoadingContent');
		this.drawLoading();
		this.currentState = this.states['loadingContent'];
		this.clearMessages(); // clearing any previous disconnection message
		
		
    	var storedMessages = JSON.parse(localStorage.getItem('chatMessages'));
    	if(storedMessages === null){
    		storedMessages = [];
    	}
    	this.debug('useLocalStorage: ', this.useLocalStorage);
    	this.debug('found storedMessages: ', storedMessages.length);
    	for(var i = 0, l = storedMessages.length; i < l; i++){
    		var message = storedMessages[i];
    		this.appendMessage(message);
    	}
		
		// before we move to the ready state, let's query for user data
		this.socket.send({sessionId: $.cookie('PHPSESSID')});
	},
	
	moveToStateReady: function (name){
		this.debug('-moveToStateReady - ' + name);
		this.drawReady();
		this.currentState = this.states['ready'];
		$('#chatMessage').focus();
		
		localStorage.setItem('chatStatus', 'open');
	},
	
	moveToStatePaused: function (){
		this.debug('-moveToStatePaused');
		this.drawPaused();
		this.currentState = this.states['paused'];
	},
	
	moveToStateConnectionFailure: function (){
		this.debug('-moveToStateConnectionFailure');
		this.drawFailure();
		this.currentState = this.states['failure'];
	},
	
	drawOpen: function(){
		$('#chatContainer').attr('class', 'open');
		
		$('#chatOpen').hide();
		$('#chatExit').show();
		$('#chatToggle').attr('class', 'chatMin');
	},
	
	drawLoading: function(){
		$('#chatContainer').attr('class', 'open loading');
		$('#chatOpen').hide();
		$('#chatExit').show();
	},
	
	drawReady: function(){
		$('#chatContainer').attr('class', 'open ready');
		$('#chatOpen').hide();
		$('#chatExit').show();
		$('#chatToggle').attr('class', 'chatMin');
	},
	
	drawPaused: function(){
		$('#chatContainer').attr('class', 'closed');
		$('#chatOpen').hide();
		$('#chatExit').show();
		$('#chatToggle').attr('class', 'chatMax');
	},
	
	drawFailure: function(){
		this.debug('TODO: draw connection failure');
		$('#chatContainer').attr('class', 'open');
		$('#chatOpen').hide();
		$('#chatExit').show();
	},
	
	drawClosed: function(){
		$('#chatContainer').attr('class', 'closed');
		$('#chatOpen').show();
		$('#chatExit').hide();
		$('#chatToggle').attr('class', '');
	}
};