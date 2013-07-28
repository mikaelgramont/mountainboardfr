/**
 * Helper functions to upload facebook photos
 */
var Lib = Lib || { };
Lib.FacebookUpload = {
	token: null,
	userId: null,
	photos: [],
	defaultOptions: {
		'pageTitle': 'post a photo from Facebook',
		'loading': 'loading...',
		'albumSingular': 'album',
		'albumPlural': 'albums',
		'pickAFacebookAlbum': 'pick an album',
		'pickAnotherOne': 'pick another one',
		'photoSingular': 'photo',
		'photoPlural': 'photos',
		'imgPath': '/images/default/'
	},
	
	options: {},
	
	ucfirst: function(str){
	    str += '';
	    var f = str.charAt(0).toUpperCase();
	    return f + str.substr(1);
	},
	
	start: function(userOptions){
		this.token = this.getAccessToken();
		if(this.token == null){
			return;
		}

		this.options = jQuery.extend(this.defaultOptions, userOptions);
		
		this.loadUser();
	},

	getAccessToken: function(){
		var token = null; 	
		if(window.location.hash.length > 0) {
			token = window.location.hash.substring(1);
		}
		return token;
	},
	
	replaceFileInput: function(){
		$('#mediaHint').remove();
		$('#media').parent().prepend('\
			<div id="facebookPhotoExplorer">\
				<div class="loading">\
					<img src="/' + this.options.imgPath + 'ajax-loader.gif" alt="" />\
					' + this.ucfirst(this.options.loading) + '\
				</div>\
				<div class="albums">\
					<p>' + this.ucfirst(this.options.pickAFacebookAlbum) + '</p>\
					<ul/>\
				</div>\
				<div class="photos">\
					<span class="albumTitle"></span>\
					<a href="#" class="back">(' + this.options.pickAnotherOne + ')</a>\
					<ul/>\
				</div>\
			</div>\
			'
		).end().replaceWith('\
			<input type="text" id="media" name="media" readonly="readonly">\
			<input type="hidden" id="useRemoteFile" name="useRemoteFile" value="1">'
		);
	},
	
	setUpClickHandlers: function(){
		var me = this;
		$('#facebookPhotoExplorer div.photos').click(function(e){
			$('#media').val(e.target.parentNode.rel);
			$(this).find('li').removeClass('selected');
			$(e.target.parentNode.parentNode).addClass('selected');
			return false;
		});
		$('#facebookPhotoExplorer div.albums').click(function(e){
			var parts = e.target.rel.split('|');
			me.getAlbumPhotos(e.target.text, parts[0], parts[1]);
			return false;
		});
		$('#facebookPhotoExplorer div.photos a.back').click(function(){
			$('#facebookPhotoExplorer div.albums').show();
			$('#facebookPhotoExplorer div.photos').hide();
			$('#facebookPhotoExplorer div.photos ul').empty();
			return false;
		});
	},
	
	loadScript: function(url, callback){
		var s = document.createElement('script');
		s.type = 'text/javascript';
		if(typeof(callback) != 'undefined'){
			url += '&callback=' + callback;
		}
		s.src = url;
		document.getElementsByTagName('head')[0].appendChild(s);
		
		$('#facebookPhotoExplorer div.albums, #facebookPhotoExplorer div.photos').hide();
		$('#facebookPhotoExplorer div.loading').show();
	},
	
	loadUser:function(){
		this.loadScript('https://graph.facebook.com/me?'+this.token, 'Lib.FacebookUpload.proceed');
	},
	
	proceed: function(response){
		if(typeof(response.id) == 'undefined'){
			alert('unauthorized');
			return;
		}
		
		this.userId = response.id;
		
		$('div.addPhotoFacebook').remove();
		
		this.changeTitle();
		
		this.replaceFileInput();

		this.setUpClickHandlers();
		
		this.getAlbums();
		
	},
	
	changeTitle: function(){
		$('h1.albumTitle').text(this.ucfirst(this.options.pageTitle));
	},
	
	getAlbums: function(response){
		this.loadScript('https://graph.facebook.com/'+this.userId+'/albums/?'+this.token, 'Lib.FacebookUpload.listAlbums');
	},
	
	listAlbums: function(response){
		this.albums = response.data;
		
		var frag = document.createDocumentFragment();
		for(var i = 0, len = this.albums.length; i < len; i++){
			if(typeof(this.albums[i].count) == 'undefined'){
				continue;
			}
			
			var li = document.createElement('li'),
				a = document.createElement('a'),
				s = document.createElement('span');
			s.className = 'count';
			s.appendChild(document.createTextNode(' (' + this.albums[i].count + ' ' + ((this.albums[i].count > 1) ? this.options.photoPlural:this.options.photoSingular) + ')'));
			a.appendChild(document.createTextNode(this.albums[i].name));
			
			a.href = '#';
			a.rel = this.albums[i].id + '|' + this.albums[i].count + '|' + this.albums[i].link;
			a.className = "album dataLink";
			li.appendChild(a);
			li.appendChild(s);
			frag.appendChild(li);
		}

		$('#facebookPhotoExplorer div.loading').hide();
		$('#facebookPhotoExplorer div.albums ul').empty().get(0).appendChild(frag);
		$('#facebookPhotoExplorer div.albums').show();
	},
	
	getAlbumPhotos: function(title, albumId, count){
		$('#facebookPhotoExplorer div.photos span.albumTitle').text(this.ucfirst(this.options.albumSingular) + ': ' + title);
		this.loadScript('https://graph.facebook.com/'+albumId+'/photos/?'+this.token + '&limit=' + count, 'Lib.FacebookUpload.showPhotos');
	},
			

	getUserPhotos: function(response){
		this.userId = response.id;
		this.loadScript('https://graph.facebook.com/'+this.userId+'/photos/?'+this.token, 'Lib.FacebookUpload.showPhotos');
	},
	
	showPhotos: function(response){
		$('#facebookPhotoExplorer div.albums').hide();
		$('#facebookPhotoExplorer div.photos').show();
		this.photos = response.data;
		var frag = document.createDocumentFragment();
		for(var i = 0, len = this.photos.length; i < len; i++){
			var photo = this.photos[i],
				li = document.createElement('li'),
				a = document.createElement('a'),
				img = document.createElement('img');
			a.href='#';
			a.rel=photo.images[0].source;
			img.src = photo.picture;
			a.appendChild(img);
			li.appendChild(a);
			frag.appendChild(li);
		}
		
		$('#facebookPhotoExplorer div.loading').hide();
		$('#facebookPhotoExplorer div.photos ul').empty().show().get(0).appendChild(frag);
	}
}