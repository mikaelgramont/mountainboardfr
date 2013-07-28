module.exports = function Message(user, type, text){
	var self = this,
		date = new Date();

	this.time = date.getTime();
	this.from = {
		id: user.id,
		lang: user.lang,
		name: user.name,
		link: user.link,
	};
	this.type = type;
	this.text = text;
};