<?php

/**
 * Used by clients to authentificate on each request
 * Can be serialized and deserialized to/from JSON
 *
 * @author eugene
 */
class AccessTokenInfoApiModel extends SerializableApiModel {
    const TOKEN_TYPE_BEARER = "Bearer";
    public $access_token;
    public $token_type;
    //public $expires_in;
    //public $refresh_token;
}
