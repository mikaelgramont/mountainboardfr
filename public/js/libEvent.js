var Lib = Lib || { };
Lib.Event = {
    globalListeners_: {},
    delegatedListenerKeys_ : {},
    delegatedListeners_ : {},
    
    listenOnClass: function(eventType, className, callback, scope) {
        if (!this.delegatedListeners_[eventType]) {
            this.delegatedListeners_[eventType] = {};
            document.body.addEventListener(eventType,
                    this.globalListener_.bind(this), true);
        }
        if (!this.delegatedListeners_[eventType][className]) {
            this.delegatedListeners_[eventType][className] = [];
        }
        eventKey = Lib.uuid();
        this.delegatedListeners_[eventType][className].push(eventKey);
        
        if (scope) {
            callback = callback.bind(scope);
        }
        this.delegatedListenerKeys_[eventKey] = callback;
        return eventKey;
    },
    
    unlistenByKey: function(key) {
        loop:
        for (var eventType in this.delegatedListeners_) {
            for (var className in this.delegatedListeners_[eventType]) {
                if (this.delegatedListeners_[eventType][className] == key) {
                    delete this.delegatedListeners_[eventType][className];
                    if (Object.keys(this.delegatedListeners_[eventType]).length == 0) {
                        delete this.delegatedListeners_[eventType];
                    }
                    break loop;
                }
            }  
        }
        delete this.delegatedListenerKeys_[key];
    },
    
    unlistenByClass: function(className) {
        var key;
        loop:
        for (var eventType in this.delegatedListeners_) {
            for (var listenerClassName in this.delegatedListeners_[eventType]) {
                if (listenerClassName == className) {
                    key = this.delegatedListeners_[eventType][listenerClassName];
                    delete this.delegatedListeners_[eventType][listenerClassName];
                    if (Object.keys(this.delegatedListeners_[eventType]).length == 0) {
                        delete this.delegatedListeners_[eventType];
                    }
                    break loop;
                }
            }  
        }
        delete this.delegatedListenerKeys_[key];
    },
    globalListener_: function(event) {
        if (!this.delegatedListeners_[event.type]) {
            return;
        }
        
        for(className in this.delegatedListeners_[event.type]) {
            var ancestor = Lib.getAncestor(event.target, className);
            if (!ancestor) {
                continue;
            }
            
            var cbKeysList = this.delegatedListeners_[event.type][className];
            for (var i = 0, l = cbKeysList.length; i < l; i++) {
                cb = this.delegatedListenerKeys_[cbKeysList[i]];
                if (cb) {
                	cb(event);
                }
            }
        }
    }
};