/*================= CONSTANTS =================*/
_LEARZING_HOST = "http://localhost:8080/";
_LEARZING_API_ENDPOINT = _LEARZING_HOST + "api/";

_API_EP_AUTH_TOKEN = _LEARZING_HOST + "auth/token";
_API_EP_USER = _LEARZING_API_ENDPOINT + "user";
_API_EP_FRIENDS = _LEARZING_API_ENDPOINT + "friends";
_API_EP_MESSAGING = _LEARZING_API_ENDPOINT + "messaging";
_API_EP_Search = _LEARZING_API_ENDPOINT + "search";

_ACCESS_TOKEN_SAVE_DIV_ID = "_learz_acc_token_saved";

LEARZING_STATUS_SUCCESS = "SUCCESS";
LEARZING_STATUS_UNAUTHORIZED = "AUTHORIZATION_FAILED";
LEARZING_STATUS_INVALID_ARGUMENT = "INVALID_ARGUMENT";
LEARZING_STATUS_INTERNAL_SERVER_ERROR = "INTERNAL_ERROR";
LEARZING_STATUS_AJAX_ERROR = "AJAX_ERROR";

/*================= OTHER =================*/
function _LearzingInvalidArgumentException(message) {
    this.name = "Invalid argument exception";
    this.message = message;
    this.toString = function(){ return this.name + ": " + this.message; };
}
/*
 * function completionCallback(APIResponse)
 * APIResponse : { status, texts, data }
 */
/*================= SERVICES =================*/
_apiCommunicationService = {
    get : function(url, data, completionCallback, thisObject) {
        return this._performRequest("GET", url,
            "request=" + encodeURIComponent(JSON.stringify(data)),
            completionCallback, thisObject);
    },
    post : function(url, data, completionCallback, thisObject) {
        return this._performRequest("POST", url,
            JSON.stringify(data),
            completionCallback, thisObject);
    },
    put : function(url, data, completionCallback, thisObject) {
        return this._performRequest("PUT", url,
            JSON.stringify(data),
            completionCallback, thisObject);
    },
    del : function(url, data, completionCallback, thisObject) {
        return this._performRequest("DELETE", url,
            JSON.stringify(data),
            completionCallback, thisObject);
    },
    /*
     * @param {type} method
     * @param {type} url
     * @param {type} data
     * @param {type} completionCallback
     * @param {type} thisObject - "this" context of completionCallback
     * @returns {jqXHR}
     */
    _performRequest : function(method, url, data, completionCallback, thisObject) {
        if (thisObject === null || thisObject === undefined) {
            thisObject = window;
        }
        var jqXHR = $.ajax(url, {
            accepts : "application/json",
            async : true,
            cache : false,
            contentType : "application/json; charset=UTF-8",
            context : this,
            data : data,
            /* TODO As we are making SDK that may be used on different websites with
             * different domains, need to set "jsonp" dataType here.
             * See http://api.jquery.com/jQuery.ajax/ about it. 
             * For mow it is not clear that web SDK we'll be used from different
             * domain. */
            dataType : "json",
            global : false,
            headers : this._mkRequestHeaders(),
            processData : false,
            type : method,
            url : url,
            error : this._ajaxError(completionCallback, thisObject),
            success : this._ajaxSuccess(completionCallback, thisObject)
        });
        return jqXHR;        
    },
    _ajaxSuccess : function(completionCallback, thisObject) {
        return function(data, textStatus, jqXHR) {
            if (completionCallback !== null)
                completionCallback.call(thisObject, data);
        };
    },
    _ajaxError : function(completionCallback, thisObject) {
        return function(jqXHR, textStatus, errorThrown) {
            /*alert("Got ajax error:\n" +
                  textStatus.toString( + "\n" +
                  errorThrown.toString()));*/
            if (completionCallback !== null) {
                var resultObject = jqXHR.responseJSON !== null ?
                    jqXHR.responseJSON :
                    { status : LEARZING_STATUS_AJAX_ERROR, texts : [
                        "We are having troubles with sending request to server.\n\
                         Please try sending your request again."]};
                completionCallback.call(thisObject, resultObject);
            }
        };
    },
    _mkRequestHeaders : function() {
        var result = {};
        if (this._authService._accessTokenInfo !== null) {
            result.Authorization = this._authService._accessTokenInfo.token_type +
            " " +
            this._authService._accessTokenInfo.access_token;
        }
        return result;
    },
    _init : function(authService) {
        this._authService = authService;
    },
    _authService : null
};

_LOCAL_STORAGE_TOKEN_KEY = "LEARZING_API_TOKEN";
_COOKEY_TOKEN_KEY = "LEARZING_API_TOKEN";
_authService = {
    login : function(email, password, completionCallback) {
        if (this._accessTokenInfo === null) {
            LEARZ._services.api.get(_API_EP_AUTH_TOKEN,
                { email: email, password: password, client_id: this._clientId },
                function(apiResponse) {
                    if (apiResponse.status === LEARZING_STATUS_SUCCESS) {
                        localStorage.setItem(_LOCAL_STORAGE_TOKEN_KEY, JSON.stringify(apiResponse.data));
                        this._accessTokenInfo = apiResponse.data;
                        $.cookie(_COOKEY_TOKEN_KEY, apiResponse.data.access_token);
                    }
                    if (completionCallback !== null)
                        completionCallback(apiResponse);
                }, this
            );
        } else {
            if (completionCallback !== null) {
                completionCallback({
                    status : LEARZING_STATUS_INVALID_ARGUMENT,
                    texts : ["You shouldn't be logged in to perform login action"]
                });
            }
        }
    },
    logout : function(completionCallback) {
        if (this._accessTokenInfo !== null) {
            LEARZ._services.api.del(_API_EP_AUTH_TOKEN,
                { access_token: this._accessTokenInfo.access_token, client_id: this._clientId },
                function(apiResponse) {
                    if (apiResponse.status === LEARZING_STATUS_SUCCESS) {
                        this._accessTokenInfo = null;
                        localStorage.removeItem(_LOCAL_STORAGE_TOKEN_KEY);
                        $.removeCookie(_COOKEY_TOKEN_KEY);
                    }
                    if (completionCallback !== null)
                        completionCallback(apiResponse);
                }, this
            );
        } else
            if (completionCallback !== null) {
                completionCallback({
                    status : LEARZING_STATUS_INVALID_ARGUMENT,
                    texts : ["You should be logged in to perform logout action"]
                });
            }
    },
    _init : function(clientId) {
        this._clientId = clientId;
        if (localStorage.getItem(_LOCAL_STORAGE_TOKEN_KEY) !== null) {
            this._accessTokenInfo = $.parseJSON(localStorage.getItem(_LOCAL_STORAGE_TOKEN_KEY));
        }
    },
    _accessTokenInfo : null,
    _clientId : null
};

function _User(email, name, surname, isOnline, role, id) {
    this.id = id;
    this.email = email;
    this.name = name;
    this.surname = surname;
    this.isOnline = isOnline;
    this.role = role; 
}

function _apiUserToUser(apiUser) {
    return new User(apiUser.id,
        apiUser.name,
        apiUser.surname,
        apiUser.is_online === "true" ? true : false,
        apiUser.role);
}

function _UserToApiUser(user) {
    return {
        id : user.id,
        email : user.email,
        name : user.name,
        surname : user.surname,
        is_online : user.isOnline ? "true" : "false",
        role: user.role
    };
}

/*
 * @param [String] fields
 * @returns {_FieldsFilter object}
 */
function _FieldsFilter(fields) {
    if (!$.isArray(fields))
        throw LEARZ.exceptions.invalidArgumentException("Fields filter should be array");
    this.fields = fields;
}

_userService = {
    get : function(userId, completionCallback, fieldsFilter) {
        
    },
    register : function(user, password, completionCallback) {
        var apiUser = _UserToApiUser(user);
        LEARZ._services.api.post(_API_EP_USER,
            { user : apiUser, password : password },
            function(apiResponse) {
                if (completionCallback !== null)
                    completionCallback(apiResponse);
            }, this
        );
    },
    update : function(user, completionCallback) {
        
    },
};

_friendsService = {
    
};

_messagingService = {
    
};

_searchService = {
    
};

function clientSupportsHTML5LocalStorage() {
    try {
        return 'localStorage' in window && window['localStorage'] !== null;
    } catch (e) {
        return false;
    }
}

function formatErrorMessage(apiResponse) {
    if (apiResponse.result !== "SUCCESS")
        return "Learzing API returned error: " +
            apiResponse.result + " " + apiResponse.texts.toString();
}
/* ================= SDK MAIN OBJECT ================= */
LEARZ = {
    init : function(config) {
        this._config.clientId = config.clientId;
        this.services.auth._init(this._config.clientId);
        this._services.api._init(this.services.auth);
        if (!clientSupportsHTML5LocalStorage()) {
            alert("Fatal error. You need latest version of your browser to use Learzing");
        }
    },
    services : {
        auth : _authService,
        user : _userService,
        friends : _friendsService,
        messaging : _messagingService,
        search : _searchService
    },
    /* exceptions */
    exceptions : {
        invalidArgumentException : _LearzingInvalidArgumentException
    },
    /* public helper functions */
    objs : {
        User : _User,
        FieldsFilter : _FieldsFilter
    },

    /* private part */
    _services : {
        api : _apiCommunicationService
    },
    _config : {}
};