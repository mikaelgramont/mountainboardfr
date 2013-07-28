var http = require('http'), // HTTP server
  io = require('socket.io'), // Socket.io
  fs = require('fs'), // File System
  mtbchat = require('./mtbchat');

server = http.createServer(function(req, res){});
server.listen(8080, 'www.redesign-zend.fr');
//console.log(req);
var socket = io.listen(server);
var users = [];
var runningTimeouts = [];

var	systemUser = {
	notifyJoin: function(client, name, link){
		var payload = {from: name, message: 'joined the chat.', link: link, type: 'join'};
	    client.send(payload);
	    client.broadcast(payload);
	},

	notifyLeave: function(client, name, link){
		var payload = {from: name, message: 'left the chat.', link: link, type: 'leave'};
	    client.send(payload);
	    client.broadcast(payload);
	}
};

socket.on('connection', function(client){
	client.on('message', function(msg){
		
		console.log('message client');
		console.log('client', client);

		if(typeof(msg.sessionId) != 'undefined'){
			// we're getting a PHP session id, let's use it to get user data
			console.log("Looking for user info for session '" + msg.sessionId + "'");
			var options = {
				host: 'www.redesign-zend.fr',
				port: 80,
				path: '/chat?authCheck=ra45HuiB@&PHPSESSID=' + msg.sessionId
			};

			var site = http.createClient(options.port, options.host);
			var request = site.request("GET", options.path, {'host' : options.host})
            request.end();

            request.on('response', function(response) {
                    response.setEncoding('utf8');
                    console.log('STATUS: ' + response.statusCode);
                    // todo: buffer the data here, and act on the end event
                    response.on('data', function(chunk) {
                    	var json = JSON.parse(chunk);
                    	console.log(json, "DATA");
                    	users[client.sessionId] = json;
                        client.send(json);
                        
                        systemUser.notifyJoin(client, json.name, json.link);
                    });
            });			
		} else {
			var payload = {
				from: users[client.sessionId].name,
				message: msg.text,
				link: users[client.sessionId].link,
				type: 'msg'
			};
			console.log(client.sessionId + " broadcasting ", payload);
			client.broadcast(payload);
		}
	});
    
	// when the server gets a disconnect, during a connection, broadcast the disconnection
	client.on('disconnect', function(){
		console.log('disconnect client');
		console.log(client);
		var userId = typeof(users[client.sessionId].id) ==  'undefined' ? null : users[client.sessionId].id;
		
		if(userId){
			runningTimeouts[client.sessionId] = setTimeout(function(){
				systemUser.notifyLeave(client, users[client.sessionId].name, users[client.sessionId].link);
				console.log(userId + ' left for good after time out');
			}, 15000);
			console.log(userId + ' left, starting time out');
		}
	});
});