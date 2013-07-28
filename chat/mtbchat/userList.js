var User = require('./user');

module.exports = function UserList(){
	var self = this;
	
	this.users = {};
	
	this.addUser = function(user){
		if(typeof(this.users[user.id]) != 'undefined'){
			console.log("User with id " + user.id + ' already registered, has ' + this.users[user.id].bufferedMessages.length + ' buffered messages');
			return false;
		}
		
		console.log("Adding user with id " + user.id);
		this.users[user.id] = user;
		
		return true;
	};
	
	this.remove = function(id){
		delete this.users[id];
		console.log('Removed user ' + id);
	};
	
	this.getUser = function(id){
		if(typeof(this.users[id]) == 'undefined'){
			return null;
		}
		return this.users[id];
	};

	this.getUserData = function(chatRoom, http, siteHost, sitePort, client, PHPSESSID, onAdd, onSuccess, onError){
		console.log('getUserData for PHPSESSID='+PHPSESSID);
		
		var site = http.createClient(sitePort, siteHost),
			request = site.request(
				"GET", '/chat?authCheck=ra45HuiB@&PHPSESSID='+PHPSESSID, {
				'host' : siteHost
			}),
			data = '';
        request.end();
		
        request.on('response', function(response) {
            response.setEncoding('utf8');
            response.on('data', function(chunk) {
            	data += chunk;
            });
            response.on('end', function onUserInfoResponse(){
            	if(!data){
            		console.log('No data received for getUserData');
            		onError(user);
            		return;
            	}
            	
            	var json = JSON.parse(data);
            	
            	if(!json.id){
            		console.log('userId = 0 received for getUserData');
                	onError(user);
            		return;
            	}
            	
            	var added = false,
            		user = self.getUser(json.id);
            	if(user == null){
            		user = new User(json);
            		added = self.addUser(user);
            	}
            	
            	onSuccess(user);
           		
            	if(added) {
            		onAdd(user);
            	}
            	user.isHere(chatRoom);
            });
        });		
	};		

};