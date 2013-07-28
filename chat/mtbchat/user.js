//var Message = require('./message');

module.exports = function User(json){
	var self = this;
	
	this.json = json;
	this.id = json.id;
	this.lang = json.lang;
	this.name = json.name;
	this.link = json.link;
	this.disconnectionTimeout = null;
	this.bufferedMessages = [];
	
	this.getJson = function(){
		return this.json;
	};
	
	this.disconnectClient = function(client, chatRoom){
		console.log('client ' + client.sessionId + ' for user ' + self.id + ' left, starting time out');
		
		if(this.disconnectionTimeout){
			// if any time out was running, delete it and start over
			clearTimeout(this.disconnectionTimeout);
		}
		
		this.disconnectionTimeout = setTimeout(function(){
			console.log('client ' + client.sessionId + ' for user ' + self.id + ' left for good after time out');
			chatRoom.notifyLeave(client, self);
		}, 15000);
	};
	
	this.isHere = function(chatRoom){
		console.log('user ' + this.id + ' is here, and has ' + this.bufferedMessages.length + ' buffered messages' );
		clearTimeout(this.disconnectionTimeout);
		this.disconnectionTimeout = null;
		chatRoom.sendBufferedMessages(this);
		this.bufferedMessages = [];
	};
};