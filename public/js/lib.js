'use strict';

var Lib = {
		CARD_ACTION_CLASSES_BLACKLIST_: [
		'linkLeft', 'linkRight',
		'dataLink',
		'headerCardAction',
		'headerCardActionInMenu',
		'headerCardActionInMenuOnly',
	],
	
	currentMenuDisplayMode_: null,
	
	overlayEl_: document.getElementById('overlay'),
	modalContainerEl_: document.getElementById('modalContainer'),
	desktopMenuEl_: document.getElementById('catMenu'),
	mobileMenuEl_: document.getElementById('mobileMenu'),
	searchButtonEl_: document.getElementById('searchButton'),
	simpleSearchEl_: document.getElementById('simpleSearchMobile'),
	modalCloseEl_: document.getElementById('closeModal'),
	modalContainerEl_: document.getElementsByClassName('modalContent')[0],
		
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
    
    showOverlay: function(mode) {
    	this.currentMenuDisplayMode_ = mode;
        this.overlayEl_.classList.add('visible');
    },
    
    hideOverlay: function() {
    	if (this.currentMenuDisplayMode_ == 'mobileMenu') {
    		this.dismissMobileMenu();	
    	} else if (this.currentMenuDisplayMode_ == 'modal') {
    		this.dismissModal();	
    	} else if (this.currentMenuDisplayMode_ == 'search') {
    		this.dismissSearch();	
    	}
    	this.currentMenuDisplayMode_ = null;
    },
    
    setupMenus: function(menuId) {
    	document.getElementById('menuButton').addEventListener(
    		'click', this.showMobileMenu.bind(this));
    	
    	this.overlayEl_.addEventListener('click', (function(e) {
        	if (e.target == this.overlayEl_) {
        		this.hideOverlay();
        	}
       	}).bind(this));
        document.body.addEventListener(
        	'keyup', this.onModalKeyPress.bind(this), true);
    	
    	this.searchButtonEl_.addEventListener(
        	'click', this.onSearchButtonClick.bind(this));

        Lib.Event.listenOnClass(
        	'click', 'category', this.onCategoryClick, this);
        this.desktopMenuEl_.addEventListener(
        	'mouseleave', this.closeMenuDropdowns.bind(this));
    },
    
    onSearchButtonClick: function(e) {
    	this.showOverlay('search');
    	this.simpleSearchEl_.classList.toggle("offscreen");
        if (this.simpleSearchEl_.classList.contains("offscreen")) {
            document.getElementById('searchTerms').focus();
        }
    },
    
    dismissSearch: function() {
		this.simpleSearchEl_.classList.add("offscreen");
    	setTimeout((function() {
	    	this.overlayEl_.classList.remove('visible');
    	}).bind(this), 300)    	
    },
    
    onCategoryClick: function(e) {
    	this.closeMenuDropdowns();
    	
    	var parentEl = e.target.parentElement;
    	if (parentEl.classList.contains('activeCat')) {
    		return;
    	} else if (parentEl.classList.contains('inactiveCat')) {
    		e.preventDefault();
    		parentEl.getElementsByTagName('ul')[0].classList.toggle('visible');
    	}
    },
    
    closeMenuDropdowns: function(e) {
    	var cats = this.desktopMenuEl_.getElementsByClassName('inactiveCat');
    	for(var i = 0, l = cats.length; i < l; i++) {
    		var uls = cats[i].getElementsByTagName('ul');
			if (uls && uls[0]) {
				uls[0].classList.remove('visible');
			}
    	}
    },
    
    showMobileMenu: function() {
    	this.showOverlay('mobileMenu');
    	setTimeout((function() {
    		this.mobileMenuEl_.classList.remove("offscreen");
    	}).bind(this), 0)
    },
    
    dismissMobileMenu: function() {
		this.mobileMenuEl_.classList.add("offscreen");
    	setTimeout((function() {
	    	this.overlayEl_.classList.remove('visible');
    	}).bind(this), 300)
    },
    
    showModal: function(contentId) {
        this.modalListeners_.closeClick = Lib.Event.listenOnClass(
        	'click', 'closeModal', this.dismissModal, this);

        if (contentId) {
        	this.showModalContainer_();
        	this.hideModalContentChildren_(this.getModalContentEl(contentId));
        }
        this.modalCloseEl_.classList.add('visible');
    	this.showOverlay('modal');
    },
    
    dismissModal: function() {
        this.hideModalContainer_();
        this.hideModalContentChildren_();
    	this.overlayEl_.removeEventListener('click', this.dismissModal);

    	Lib.Event.unlistenByKey(this.modalListeners_.closeClick);
    	delete this.modalListeners_.closeClick;
    	
        document.removeEventListener('keyup', this.onModalKeyPress);
        
    	setTimeout((function() {
    		this.modalCloseEl_.classList.remove('visible');
	    	this.overlayEl_.classList.remove('visible');
    	}).bind(this), 0)
    },

    showModalContainer_: function() {
    	this.modalContainerEl_.classList.add('visible');
    },
    
    hideModalContainer_: function() {
    	this.modalContainerEl_.classList.remove('visible');
    },
    
    hideModalContentChildren_: function(exceptEl) {
        var modalContentEls = this.overlayEl_.getElementsByClassName(
    		'modalContentItem');
        for(var i = 0, l = modalContentEls.length; i < l; i++) {
	    	if (exceptEl && modalContentEls[i] === exceptEl) {
	    		modalContentEls[i].classList.add('visible');
	    	} else {
	    		modalContentEls[i].classList.remove('visible');
	    	}
	    }
    },
    
    onModalKeyPress: function(e) {
		if (e.keyCode == 27) {
			this.dismissModal();
		}    	
    },

    setupPageScrollListener: function() {
    	// TODO: adjust threshold depending on page position: if posY less than
    	// header height, use header height. otherwise use 10
        var timeout;
        var currentVerticalDirection;
        var lastVerticalPosition = 0;
        var lastVerticalDirection = 'down'; // 'up' or 'down';
        var currentState = 'visible';
        var visibilityState;
        var headerEl = document.getElementById('header');
        var THRESHOLD = 20;
        var MAX_SCREEN_WIDTH_FOR_FIXED_HEADER = 667;

        function onScroll(e) {
            var currentVerticalPosition = window.scrollY;

            if (currentVerticalPosition < lastVerticalPosition) {
                currentVerticalDirection = 'up';
            } else {
                currentVerticalDirection = 'down';
            }

            if (currentVerticalDirection != lastVerticalDirection) {
                // need to update
                if (currentVerticalDirection == 'up') {
                    // Show
                    visibilityState = 'visible';
                } else {
                    if (currentVerticalPosition > THRESHOLD) {
                        // Hide
                        visibilityState = 'hidden';
                    }
                }
            } else {
                if (currentVerticalDirection == 'down' &&
                    currentVerticalPosition > THRESHOLD) {
                    visibilityState = 'hidden';
                }
            }

            lastVerticalPosition = currentVerticalPosition;
            lastVerticalDirection = currentVerticalDirection;
            headerEl.classList.toggle('offscreen', visibilityState == 'hidden');
        }
        window.addEventListener('scroll', function(e) {
            if (window.innerWidth > MAX_SCREEN_WIDTH_FOR_FIXED_HEADER) {
                headerEl.classList.remove('offscreen');
                return;
            }
            if (timeout) {
                return;
            }
            timeout = setTimeout(function() {
                onScroll(e);
                timeout = null;
            }, 200);
        });
    }
};