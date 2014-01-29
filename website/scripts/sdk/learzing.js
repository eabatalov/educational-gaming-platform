/*================= CONSTANTS =================*/
_LEARZING_HOST = "http://learzing.com/";
_LEARZING_API_ENDPOINT = _LEARZING_HOST + "api/";

_API_EP_AUTH_TOKEN = _LEARZING_HOST + "auth/token";
_API_EP_USER = _LEARZING_API_ENDPOINT + "user";
_API_EP_FRIENDS = _LEARZING_API_ENDPOINT + "friends";
_API_EP_MESSAGING = _LEARZING_API_ENDPOINT + "messaging";
_API_EP_SEARCH = _LEARZING_API_ENDPOINT + "search";
_API_EP_SKILLS = _LEARZING_API_ENDPOINT + "skills";

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
                completionCallback.call(thisObject, this._mkResponse(data));
        };
    },
    _ajaxError : function(completionCallback, thisObject) {
        return function(jqXHR, textStatus, errorThrown) {
            if (jqXHR.responseJSON.status === LEARZING_STATUS_UNAUTHORIZED) {
                this._authService._revokeCreds();
            }
            if (completionCallback !== null) {
                completionCallback.call(thisObject, this._mkResponse(jqXHR.responseJSON));
            }
        };
    },
    _mkResponse : function(response) {
        if (!isObject(response) || !('status' in response))
            response = {
                status : LEARZING_STATUS_INTERNAL_SERVER_ERROR,
                texts : [ "Internal server error has occured. "
                          + "Please try to send your request later." ]
            };
        return response;
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
_AUTH_COOKIE_KEY_TOKEN = "LEARZING_API_TOKEN_KEY";
_AUTH_COOKIE_KEY_TYPE = "LEARZING_API_TOKEN_TYPE";
_authService = {
    login : function(email, password, completionCallback) {
        if (this._accessTokenInfo === null) {
            LEARZ._services.api.get(_API_EP_AUTH_TOKEN,
                { email: email, password: password, client_id: this._clientId },
                function(apiResponse) {
                    if (apiResponse.status === LEARZING_STATUS_SUCCESS) {
                        localStorage.setItem(_LOCAL_STORAGE_TOKEN_KEY, JSON.stringify(apiResponse.data));
                        this._accessTokenInfo = apiResponse.data;
                        $.cookie(_AUTH_COOKIE_KEY_TOKEN, apiResponse.data.access_token);
                        $.cookie(_AUTH_COOKIE_KEY_TYPE, apiResponse.data.token_type);
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
                        $.removeCookie(_AUTH_COOKIE_KEY_TOKEN);
                        $.removeCookie(_AUTH_COOKIE_KEY_TYPE);
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
        var tokenCookie = $.cookie(_AUTH_COOKIE_KEY_TOKEN);
        var tokenInfoStorage = localStorage.getItem(_LOCAL_STORAGE_TOKEN_KEY);
        
        if (tokenInfoStorage !== null) {
            this._accessTokenInfo = $.parseJSON(tokenInfoStorage);
        } else if (tokenCookie !== undefined) {
            this._accessTokenInfo = {
                access_token : tokenCookie,
                token_type :  $.cookie(_AUTH_COOKIE_KEY_TYPE)
            };
        }
    },
    _revokeCreds : function() {
        if (this._accessTokenInfo !== null) {
            this._accessTokenInfo.access_token = null;
            this._accessTokenInfo.token_type = null;
        }
        localStorage.setItem(_LOCAL_STORAGE_TOKEN_KEY, null);
        $.removeCookie(_AUTH_COOKIE_KEY_TOKEN);
        $.removeCookie(_AUTH_COOKIE_KEY_TYPE);
    },
    _accessTokenInfo : null,
    _clientId : null,
};

function _User(email, name, surname, isOnline, role, avatar,
    birthDate, gender, id) {
    this.id = id;
    this.email = email;
    this.name = name;
    this.surname = surname;
    this.is_online = isOnline;
    this.role = role; 
    this.avatar = avatar;
    this.birthdate = birthDate;
    this.gender = gender;
}

function _Date(day, month, year) {
    this.day = day;
    this.month = month;
    this.year = year;
}

function _PassChange(old, nnew) {
    this.old = old;
    this.new = nnew;
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
    get : function(completionCallback, userId, fieldsFilter) {
        var requestData = {};
        if (userId !== undefined && userId !== null) {
            requestData['userid'] = userId;
        }
        if (fieldsFilter !== undefined && fieldsFilter !== null) {
            requestData.fields = fieldsFilter.fields;
        }
        LEARZ._services.api.get(_API_EP_USER,
            requestData,
            function(apiResponse) {
                if (completionCallback !== null)
                    completionCallback(apiResponse);
            }, this
        );
    },
    register : function(user, password, completionCallback) {
        LEARZ._services.api.post(_API_EP_USER,
            { user : user, password : password },
            function(apiResponse) {
                if (completionCallback !== null)
                    completionCallback(apiResponse);
            }, this
        );
    },
    update : function(user, completionCallback, passChange) {
        var requestData = {
            user : user
        };
        if (passChange !== undefined && passChange !== null)
            requestData.password = passChange;

        LEARZ._services.api.put(_API_EP_USER,
            requestData,
            function(apiResponse) {
                if (completionCallback !== null)
                    completionCallback(apiResponse);
            }, this
        );
    }
};

_friendsService = {
    get : function(userId, completionCallback, fieldsFilter, paging) {
        var requestData = {
            userid : userId
        };
        if (fieldsFilter !== undefined && fieldsFilter !== null) {
            requestData.feilds = fieldsFilter;
        }
        if (paging !== undefined && paging !== null) {
            requestData.paging = paging;
        }

        LEARZ._services.api.get(_API_EP_FRIENDS,
            requestData,
            function(apiResponse) {
                if (completionCallback !== null)
                    completionCallback(apiResponse);
            }, this
        );
    },
    add : function(userId, completionCallback) {
        var requestData = {
            userid : userId
        };

        LEARZ._services.api.post(_API_EP_FRIENDS,
            requestData,
            function(apiResponse) {
                if (completionCallback !== null)
                    completionCallback(apiResponse);
            }, this
        );
    },
    remove : function(userId, completionCallback) {
        var requestData = {
            userid : userId
        };

        LEARZ._services.api.del(_API_EP_FRIENDS,
            requestData,
            function(apiResponse) {
                if (completionCallback !== null)
                    completionCallback(apiResponse);
            }, this
        );
    }
};

_messagingService = {
    
};

_SearchObjectTypes = {
    all : "all",
    user : "user"
};

function _SearchRequest(query, objectType) {
    this.query = query;
    this.object_type = objectType;
}

function _SearchResult(objectType, object) {
    this.object_type = objectType;
    this.object = object;
}

function _Paging(offset, limit, total) {
    this.offset = offset;
    this.limit = limit;
    this.total = total;
}

_searchService = {
    get : function(searchRequest, completionCallback, paging) {
        var requestData = {
            query : searchRequest.query,
            object_type : searchRequest.object_type
        };
        if (paging !== undefined && paging !== null) {
            requestData.paging = paging;
        }
        LEARZ._services.api.get(_API_EP_SEARCH,
            requestData,
            function(apiResponse) {
                if (completionCallback !== null)
                    completionCallback(apiResponse);
            }, this
        );
    }
};

function _UserSkill(userId, skillId, value) {
    this.user_id = userId;
    this.skill_id = skillId;
    this.value = value;
}

_skillsService = {
    getAllUserSkills : function(completionCallback, userId, fieldsFilter, paging) {
        var requestData = {
            user_id : userId
        };
        if (fieldsFilter !== undefined && fieldsFilter !== null) {
            requestData.feilds = fieldsFilter;
        }
        if (paging !== undefined && paging !== null) {
            requestData.paging = paging;
        }
        if (userId !== undefined && userId !== null) {
            requestData.user_id = userId;
        }

        LEARZ._services.api.get(_API_EP_SKILLS,
            requestData,
            function(apiResponse) {
                if (completionCallback !== null)
                    completionCallback(apiResponse);
            }, this
        );
    },
    getUserSkill : function(skillId, completionCallback, userId, fieldsFilter) {
        //client side skill_id validation for now
        if (!(skillId in LEARZ.consts.SKILLS)) {
            throw LEARZ.exceptions.invalidArgumentException("Invalid skillId '" + skillId.toString() + "' passed");
        }
        var requestData = {
            skill_id : skillId
        };
        if (userId !== undefined && userId !== null) {
            requestData.user_id = userId;
        }
        if (fieldsFilter !== undefined && fieldsFilter !== null) {
            requestData.feilds = fieldsFilter;
        }

        LEARZ._services.api.get(_API_EP_SKILLS,
            requestData,
            function(apiResponse) {
                if (completionCallback !== null)
                    completionCallback(apiResponse);
            }, this
        );
    },
    put : function(skillId, value, completionCallback) {
        var requestData = {
            skill_id : skillId,
            value : value
        };

        LEARZ._services.api.put(_API_EP_SKILLS,
            requestData,
            function(apiResponse) {
                if (completionCallback !== null)
                    completionCallback(apiResponse);
            }, this
        );
    }
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
    /* services */
    services : {
        auth : _authService,
        user : _userService,
        friends : _friendsService,
        messaging : _messagingService,
        search : _searchService,
        skills : _skillsService
    },
    /* public constants */
    consts : {
        SKILLS : LEARZ_SKILLS_MAP
    },
    /* exceptions */
    exceptions : {
        invalidArgumentException : _LearzingInvalidArgumentException
    },
    /* public helper functions */
    objs : {
        User : _User,
        PassChange : _PassChange,
        Date : _Date,
        FieldsFilter : _FieldsFilter,
        SearchObjectTypes : _SearchObjectTypes,
        SearchRequest : _SearchRequest,
        SearchResult : _SearchResult,
        Paging : _Paging,
        UserSkill : _UserSkill
    },

    /* private part */
    _services : {
        api : _apiCommunicationService
    },
    _config : {}
};
