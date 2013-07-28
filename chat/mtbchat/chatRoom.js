var http = require('http')
, io = require('socket.io')
, ClientList = require('./clientList')
, UserList = require('./userList')
, User = require('./user')
, Message = require('./message');

	clientList = new ClientList(),
	userList = new UserList(),
	options = {
		server: null,
		socket: null,
		host: '0.0.0.0',
		port: 8080,
		sitePort: 80,
		siteHost: ''
	};

module.exports = {
	init: function init(userOptions){
		console.log('ChatRoom - init');
		for(var i in userOptions){
			options[i] = userOptions[i];
		}
		
		this.setupConnections();
	},
	
	setupConnections: function(){
		server = http.createServer(function(req, res){});
		console.log(options.host + ':' + options.port);
		server.listen(options.port, '0.0.0.0');
		socket = io.listen(server);
		
		var self = this;
		
		socket.on('connection', function onClientConnection(client){
			self.handleConnect(client);
			
			client.on('message', function(msg){
				self.handleMessage(client, msg);
			});
		    
			client.on('disconnect', function(){
				self.handleDisconnect(client);
			});
		});
	},
	
	handleMessage: function(client, msg){
		var self = this;
		if(typeof(msg.sessionId) != 'undefined'){
			// we're getting a PHP session id, let's use it to get user data
			userList.getUserData(this, http, options.siteHost, options.sitePort, client, msg.sessionId,
				function onAdd(user){
					self.notifyJoin(client, user);
				},
				function onSuccess(user){
					// give the client its user info
					client.send({type: 'userInfo', name: user.name});
					
					self.printConnectionsSummary();
					clientList.setUserIdForClient(client, user.id);
				},
				function onError(){
            		client.send({
            			type: 'userInfoError'
            		});					
				}
			);
			return;
		}
		
		console.log('Seeking userId for sessionId ' + client.sessionId);
		var userId = clientList.clients[client.sessionId].userId,
			user = userList.getUser(userId);
		
		if(!user){
			console.log('Could not find user info for message handling - session: ' + client.sessionId);
			return;
		}
		
		this.sendMessage(client, user, 'msg', msg.text);
		
		user.isHere(this, client);
	},
	
	handleConnect: function(client){
		console.log('connection');
		clientList.addClient(client);
	},
	
	handleDisconnect: function(client){
		var self = this,
			user,
			clients,
			currentClient = clientList.findClientsBySessionId(client.sessionId);
		if(typeof(currentClient.userId) == 'undefined') {
			console.log('Could not find userId for client ' + client.sessionId);
			return;
		}

		user = userList.getUser(currentClient.userId);
		if(!user){
			console.log('Could not find user info for disconnection - session: ' + client.sessionId);
			return;
		}
		
		clients = clientList.findClientsByUserId(user.id);
		
		console.log('handleDisconnect - found ' + clients.length + ' clients for user '+user.id);
		
		clientList.remove(client);
		user.disconnectClient(client, this);
	},
	
	sendMessage: function(client, user, type, text){
		if(typeof(text) == 'undefined') {
			text = '';
		}
		var message = new Message(user, type, text); 
		
	    console.log('sending message of type "' + message.type + '"');
	    
		//client.send(message);
		
		/**
		 * for each user in the list, send the message to all its clients if
		 * the user is there.
		 * if the user isn't there, buffer the message in the user object
		 */
		for(var currentUserKey in userList.users){
			var currentUser = userList.users[currentUserKey];
			if(currentUser.disconnectionTimeout){
				// user is absent
				console.log(' -- pushing buffered message to user "' +currentUser.id + '"');
				currentUser.bufferedMessages.push(message);
				console.log('-- user ' + currentUser.id + ' has ' + currentUser.bufferedMessages.length + ' buffered messages' );
			} else {
				var clients = clientList.findClientsByUserId(currentUser.id);
				for(currentClientKey in clients){
					var currentClient = clients[currentClientKey];
					console.log(' -- sending message to client "' + currentClient.sessionId + '" for user "' + currentUser.id + '"');
					currentClient.send(message);
				}
			}
		}
	},
	
	sendBufferedMessages: function(user){
		var clients = clientList.findClientsByUserId(user.id);
			
		console.log("sending " + user.bufferedMessages.length + " buffered messages to " + clients.length + " clients for user " + user.id);
		for(var i = 0, l = user.bufferedMessages.length; i < l; i++){
			for(j = 0; j < clients.length; j++){
				clients[j].send(user.bufferedMessages[i]);	
			}
		}
	},
	
	notifyJoin: function(client, user){
		this.sendMessage(client, user, 'joined');
	},
	
	notifyLeave: function(client, user){
		// remove user from user list if it has no more clients attached to it
		var clients = clientList.findClientsByUserId(user.id);
		if(clients.length == 0){
			this.sendMessage(client, user, 'left');
			console.log('No clients left for user ' + user.id + ' - removing it');
			userList.remove(user.id);
		}
		
		chatRoom.printConnectionsSummary();
	},
	
	printConnectionsSummary: function(){
		var clients = Object.keys(clientList.clients).length,
			users = Object.keys(userList.users).length;
		
		console.log('--------------------------------------------------------------------------------');
		console.log(clients + ' clients - ' + users + ' users');
		console.log('--------------------------------------------------------------------------------');
	}
};
