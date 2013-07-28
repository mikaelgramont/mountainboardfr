module.exports = function ClientList(){
	this.clients = {};
	
	this.addClient = function(client){
		if(typeof(this.clients[client.sessionId]) != 'undefined'){
			console.log("Client with sessionId " + client.sessionId + ' already registered' );
			return false;
		}
		
		console.log("Adding client with sessionId " + client.sessionId);
		this.clients[client.sessionId] = client;
		
		return true;
	};

	this.remove = function(client){
		delete this.clients[client.sessionId];
	};	
	
	this.setUserIdForClient = function(client, userId){
		if(typeof(this.clients[client.sessionId]) == 'undefined'){
			console.log('No client with session ' + client.sessionId);
		}
		
		this.clients[client.sessionId].userId = userId;
		console.log('Set userId ' + userId + ' for sessionId ' + client.sessionId);
	};
	
	this.findClientsBySessionId = function(sessionId){
		if(typeof(this.clients[sessionId]) == 'undefined'){
			console.log('findClientsBySessionId - could not find client for session ' + sessionId);
			return false;
		}
		
		return this.clients[sessionId];
	};

	this.findClientsByUserId = function(userId){
		var userClients = [];
		
		for(var sessionId in this.clients){
			if(this.clients[sessionId].userId == userId){
				userClients.push(this.clients[sessionId]);
			}
		}
		
		return userClients;
	};
	

};