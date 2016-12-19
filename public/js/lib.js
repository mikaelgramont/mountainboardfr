'use strict';

var Lib = {
		CARD_ACTION_CLASSES_BLACKLIST_: [
		'dataLink',
		'headerCardAction',
		'headerCardActionInMenu',
		'headerCardActionInMenuOnly',
	],
		
    modalListeners_: {},

    uuid: function() {
        function s4() {
          return Math.floor((1 + Math.random()) * 0x10000)
            .toString(16)
            .substring(1);
        }
        return s4() + s4() + '-' + s4() + '-' + s4() + '-' +
          s4() + '-' + s4() + s4() + s4();
    },
    
    getAncestor: function(el, className) {
        var matches = HTMLElement.prototype.matches ||
            HTMLElement.prototype.webkitMatchesSelector ||
            HTMLElement.prototype.mozMatchesSelector ||
            HTMLElement.prototype.msMatchesSelector;
        if (matches.call(el, '.' + className)) {
            return el;
        }
        if (!el.parentElement) {
            return null;
        }
        return this.getAncestor(el.parentElement, className);
    },
        
    onHeaderCardMenuClick: function(event) {
        var cardWrapperEl = this.getAncestor(event.target, 'cardWrapper');
        var actionEls = cardWrapperEl.getElementsByClassName(
            'headerCardActionInMenu');
        
        var list = [];
        for (var i = 0, l = actionEls.length; i < l; i++) {
            var res = this.getActionList(actionEls[i]);
            list = list.concat(res);
        }
        
        var ul = document.createElement('ul');
        ul.classList.add('modalList');
        for (var i = 0, l = list.length; i < l; i++) {
        	var li = document.createElement('li');
        	var a = document.createElement('a');
        	a.setAttribute('href', list[i].href);
        	a.setAttribute('class', list[i].className);
        	var t = document.createTextNode(list[i].label);
        	a.appendChild(t);
        	li.appendChild(a);
        	ul.appendChild(li);
        }
        
        var modalContentId = 'actionMenu';
        var m = this.getModalContentEl(modalContentId);
        m.innerHTML = '';
        m.appendChild(ul);
        this.showModal(modalContentId);
    },
    
    maybeGetAttributesFromElement: function(el) {
        var list = [];
        if (!el.hasAttribute('href') || el.hasAttribute('aria-hidden')) {
            return list;
        }
        
        var label;
        if (el.hasAttribute('aria-label')) {
        	label = el.getAttribute('aria-label');
        } else if (el.hasAttribute('title')) {
        	label = el.getAttribute('title');
        } else {
        	label = el.innerText;
        }
        if (label.length == 0) {
            return list;
        }
        list.push({
            href: el.getAttribute('href'),
            label: label,
            className: el.className.split(' ').filter(function(name) {
            	return this.CARD_ACTION_CLASSES_BLACKLIST_.indexOf(name) == -1;
            }, this).join(' ')
        });
        return list;
    },

    getActionList: function(el) {
        if (el.tagName == 'A') {
            return this.maybeGetAttributesFromElement(el);
        } else {
            var list = [];
            var linkEls = el.getElementsByTagName('a');
            for (var i = 0, l = linkEls.length; i < l; i++) {
                list = list.concat(
                	this.maybeGetAttributesFromElement(linkEls[i]));
            }
            return list;
        }
    },

    getModalContentEl: function(contentId) {
    	return document.getElementById('modalContent-' + contentId);
    },
    
    showModal: function(contentId) {
        if (!contentId) {
        	return;
        }
    	var overlayEl = document.getElementById('overlay');
        var contentEl = this.getModalContentEl(contentId);
        var modalContentEls = overlayEl.getElementsByClassName(
        	'modalContentItem');
        for(var i = 0, l = modalContentEls.length; i < l; i++) {
        	if (modalContentEls[i] === contentEl) {
        		modalContentEls[i].classList.add('visible');
        	} else {
        		modalContentEls[i].classList.remove('visible');
        	}
        }
        overlayEl.classList.add('visible');
        
        if (this.modalListeners_.close) {
        	Lib.Event.unlistenByKey(this.modalListeners_.close);
        	delete this.modalListeners_.close;
        }
        this.modalListeners_.closeClick = Lib.Event.listenOnClass('click',
            'close', function(e) {
        	this.dismissModal();
        }, this);
        overlayEl.addEventListener('click',	(function(e) {
        	if (e.target == overlayEl) {
        		this.dismissModal();
        	}
       	}).bind(this));
    },
    
    dismissModal: function() {
        document.getElementById('overlay').classList.add('visible');
    	Lib.Event.unlistenByKey(this.modalListeners_.closeClick);
    	delete this.modalListeners_.closeClick;
    	var overlayEl = document.getElementById('overlay');
        overlayEl.classList.remove('visible');
        overlayEl.removeEventListener('click', this.dismissModal);
    }
};