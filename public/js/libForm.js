var Lib = Lib || { };
Lib.Form = {

		ucfirst: function(str){
		    str += '';
		    var f = str.charAt(0).toUpperCase();
		    return f + str.substr(1);
		},

		submitHandler: function(form){
			if(typeof(tinyMCE) != 'undefined'){
				tinyMCE.triggerSave();
			}
			var c = form.elements;
		
			var formStatus = true;
			if (typeof this.Forms == 'undefined') {
				return formStatus;
			}
			var formName = (form.id) ? form.id : 'form';
			var rules = this.Forms[formName];
		
			for(var key in rules) {
				var element = form[key];
				var ruleset = rules[key];
		
				var elementStatus = true;
				var errorMessages = [];
		
				for(var i in ruleset) {
					var validatorName = ruleset[i].name;
		
					if(typeof(this.validators[validatorName]) == 'undefined'){
					    continue;
					}
		
					var errorMsg = this.validators[validatorName](element, ruleset[i].parameters, ruleset[i].messages);
		            if(errorMsg && errorMsg.length != 0){
		                elementStatus = false;
		                errorMessages.push(errorMsg);
		                if(ruleset[i].breakChainOnFailure){
		                    break;
		                }
		            }
				}
		
				if(element.disabled){
				    elementStatus = true;
				    errorMessages = [];
				}
		
				this.updateError(element, elementStatus, errorMessages);
		        if(!elementStatus){
		            formStatus = false;
		        }
			}
			return formStatus;
		},

		updateError: function(element, status, errorMessages){
		    if(status){
		        $('#'+element.id + 'Error').html('').css('display','none');
		    } else {
		        var errorString = this.ucfirst(errorMessages.join(', '));
		        $('#'+element.id + 'Error').css('display','none').html(errorString).fadeIn("def");
		    }
		},

		validators:{
			id0: function(element, parameters, messages){
				if(parameters.allowWhiteSpace){
				    var ok = element.value.match(/^[a-z0-9\s]*$/i);
				} else {
				    var ok = element.value.match(/^[a-z0-9]*$/i);
				}
				return (ok) ? '' : messages.notAlnum;
			},

		id1: function(element, parameters, messages){
			if(parameters.allowWhiteSpace){
			    var ok = element.value.match(/^[a-z\s]*$/i);
			} else {
			    var ok = element.value.match(/^[a-z]*$/i);
			}
	        return (ok) ? '' : messages.notAlpha;
		},

		id2: function(element, parameters, messages){
		    var msg = '';
			if(parameters.inclusive){
	    	    if(element.value < parameters.max && element.value > parameters.min){
	    			return '';
	    		}
	            msg = messages.notBetweenStrict;
			} else {
	    	    if(element.value <= parameters.max && element.value >= parameters.min){
	    			return '';
	    		}
		        msg = messages.notBetween;
			}
	
			msg = msg.replace('%value%', element.value);
	        msg = msg.replace('%min%', parameters.min);
	        msg = msg.replace('%max%', parameters.max);
	        return msg;
		},
	
		id3: function(element, parameters, messages){
		    if(element.value.length == 0){
		        return '';
		    }
		    var ok = element.value.match(parameters.regex);
	        return (ok) ? '' : messages.dateInvalid;
		},
	
		id4: function(element, parameters, messages){
			var ok =  element.value.match(/^[0-9]*$/i);
	        return (ok) ? '' : messages.notDigits;
		},
	
		id5: function(element, parameters, messages){
			var RegEx = new RegExp(/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.([a-z]){2,4})$/i);
		    var ok =  RegEx.exec(element.value);
	        return (ok) ? '' : messages.emailAddressInvalid;
		},
	
		id6: function(element, parameters, messages){
			var ok = (element.value == parseFloat(element.value));
	        return (ok) ? '' : messages.notFloat;
		},
	
		id7: function(element, parameters, messages){
			var ok =(element.value > parameters.min);
	        return (ok) ? '' : messages.notGreaterThan;
		},
	
		id8: function(element, parameters, messages){
			return '';
		},
	
		id9: function(element, parameters, messages){
	        return '';
		},
	
		id10: function(element, parameters, messages){
	        if(!element.value){
	            return '';
	        }
		    for(var i = 0; i < parameters.haystack.length; i++){
	            if(element.value == parameters.haystack[i]){
	                return '';
	            }
	        }
	        return messages.notInArray;
		},
	
		id11: function(element, parameters, messages){
			if(element.value.length == 0){
			    return '';
			}
		    var ok = (element.value == parseInt(element.value) && element.value == parseFloat(element.value));
	        return (ok) ? '' : messages.notInt;
		},
	
		id12: function(element, parameters, messages){
			var ok =(element.value < parameters.max);
			return (ok) ? '' : messages.notLesserThan;
		},
	
		id13: function(element, parameters, messages){
			var ok = (element.value.length > 0);
	        return (ok) ? '' : messages.isEmpty;
		},
	
		id14: function(element, parameters, messages){
			var RegEx = new RegExp(parameters.regex);
			var ok = RegEx.exec(element.value);
			return (ok) ? '' : messages.regexNotMatch;
		},
	
		id15: function(element, parameters, messages){
			if(element.value.length > parameters.max){
	            return messages.stringLengthTooLong;
			}
	        if(element.value.length < parameters.min){
	            return messages.stringLengthTooShort;
	        }
	
			return '';
		},
	
		id16: function(element, parameters, messages){
			var referenceValue = $('#'+parameters.reference).get(0).value;
	        var ok = (element.value == referenceValue);
			return (ok) ? '' : messages.notSame;
		},
	
		id17: function(element, parameters, messages){
		    return '';
		},
	
		id18: function(element, parameters, messages){
		    return '';
		},
		
		id20: function(element, parameters, messages){
		    if(element.value.length == 0 || element.value == '0'){
		    	return messages.locationRequired;
		    }
		    return '';
		}
	},

	ajaxValidationHandler: function(element, validatorName, errorMessages, params){
	    if(typeof(this.ajaxValidators[validatorName]) == 'undefined'){
	        return;
	    }
	
	    this.ajaxValidators[validatorName](element, errorMessages, params);
	
	},

	ajaxValidators:{
	    id0: function(element, messages, params){
	        var requestParams = {};
	        requestParams[element.id] = element.value;
	        $.get(params.route, requestParams, function(response, status){
	            var displayMessages = [];
	            for(var msgId in response.messageNames){
	                displayMessages.push(messages[response.messageNames[msgId]]);
	            }
	
	            Lib.Form.updateError(element, response.status, displayMessages);
	        }, 'json');
	    },
	
	    id1: function(element, messages, params){
	        var requestParams = {};
	        requestParams[element.id] = element.value;
	        requestParams.userId = params.userId;
	        $.get(params.route, requestParams, function(response, status){
	            var displayMessages = [];
	            for(var msgId in response.messageNames){
	                displayMessages.push(messages[response.messageNames[msgId]]);
	            }
	
	            Lib.Form.updateError(element, response.status, displayMessages);
	        }, 'json');
	    },
	
	    id2: function(element, messages, params){
	        var requestParams = {};
	        requestParams[element.id] = element.value;
	        requestParams.userId = params.userId;
	        $.get(params.route, requestParams, function(response, status){
	            var displayMessages = [];
	            for(var msgId in response.messageNames){
	                displayMessages.push(messages[response.messageNames[msgId]]);
	            }
	
	            Lib.Form.updateError(element, response.status, displayMessages);
	        }, 'json');
	    },
	
	    id3: function(element, messages, params){
	        var requestParams = {};
	        requestParams[element.id] = element.value;
	        var deferredCheck = function(){
	            $.get(params.route, requestParams, function(response, status){
	                var displayMessages = [];
	                for(var msgId in response.messageNames){
	                    displayMessages.push(messages[response.messageNames[msgId]]);
	                }
	
	                Lib.Form.updateError(element, response.status, displayMessages);
	            }, 'json');
	        };
	        setTimeout("deferredCheck", 1000);
	    }
	}
};

