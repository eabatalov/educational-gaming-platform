/*================= CONSTANTS =================*/
_LEARZING_HOST = "http://localhost:8080/";
_LEARZING_API_ENDPOINT = _LEARZING_HOST + "api/";

_API_EP_AUTH_TOKEN = _LEARZING_HOST + "auth/token";
_API_EP_USER = _LEARZING_API_ENDPOINT + "user";
_API_EP_FRIENDS = _LEARZING_API_ENDPOINT + "friends";
_API_EP_MESSAGING = _LEARZING_API_ENDPOINT + "messaging";
_API_EP_Search = _LEARZING_API_ENDPOINT + "search";

_ACCESS_TOKEN_SAVE_DIV_ID = "_learz_acc_token_saved";

/*================= SERVICES =================*/
_authService = {
    login : function (email, password) {},
    logout : function() {},
    _accessToken : null,
    _clientId : null
};

function User(id, email, name, surname, isOnline, role) {
    this.id = id;
    this.email = email;
    this.name;
    this.surname = surname;
    this.isOnline = isOnline;
    this.role = role; 
}

function apiUserToUser(apiUser) {
    return new User(apiUser.id,
        apiUser.name,
        apiUser.surname,
        apiUser.is_online === "true" ? true : false,
        apiUser.role);
}

function UserToApiUser(user) {
    return new {
        id : user.id,
        email : user.emil,
        name : user.name,
        surname : user.surname,
        is_online : user.isOnline ? "true" : false,
        role: user.role
    };
}

_userService = {
    get : function(userId) {},
    register : function(user, password) {},
    update : function(user) {}
};

_friendsService = {
    
};

_messagingService = {
    
};

_searchService = {
    
};

/* ================= SDK MAIN OBJECT ================= */
LEARZ = {
    init : function(config) {
        this.config.clientId = config.clientId;
        this.auth._clientId = this.config.clientId;
        if (document.getElementById(_ACCESS_TOKEN_SAVE_DIV_ID) !== null) {
            this.auth._accessToken =
                document.getElementById(_ACCESS_TOKEN_SAVE_DIV_ID).textContent;
        }
    },
    config : null,
    auth : _authService,
    user : _userService,
    friends : _friendsService,
    messaging : _messagingService,
    search : _searchService
};