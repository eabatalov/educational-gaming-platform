<?php

/**
 * Postgres implementation of ISearchService
 *
 * @author eugene
 */
class PostgresSearchService implements ISearchService {

    /*
     * Creates object and connects to postgres DB
     * @returns PostgresSearchService object connected to DB
     * @throws InternalErrorException if connection falied
     */
    function __construct() {
        $this->conn = pg_connect(PostgresUtils::getConnString(), PGSQL_CONNECT_FORCE_NEW);
        TU::throwIf($this->conn == FALSE, TU::INTERNAL_ERROR_EXCEPTION, pg_last_error(),
            InternalErrorException::ERROR_CONNECTION_PROBLEMS);
    }

    /*
     * Release all the resources handled by this object
     * @returns: void
     */
    public function __destruct() {
        if ($this->conn != FALSE) {
            //pg_close($this->conn);
        }
    }

    /*
     * @throws: InternalErrorException
     */
    public function search(SearchRequest $request) {
        $searchResults = array();

        //Users search
        if ($request->getObjectType() == SearchRequest::OBJ_TYPE_ALL ||
            $request->getObjectType() == SearchRequest::OBJ_TYPE_USER) {
            $result = pg_query_params($this->conn, self::$SQL_SEARCH_USERS,
                array($request->getQuery() . '%'));
            TU::throwIf($result == FALSE, TU::INTERNAL_ERROR_EXCEPTION, pg_last_error());

            //Everything for code reusage for now!
            $userStorage = new PostgresUserStorage();
            while(($data = pg_fetch_object($result)) != FALSE) {
                $searchResults[] = new SearchResult(SearchRequest::OBJ_TYPE_USER,
                        $userStorage->getUserById($data->id));
            }
        }
        //Other object types search appending to $searchResults
        //var_dump($searchResults);
        return $searchResults;
    }

    private $conn;
    private static $SQL_SEARCH_USERS =
        "SELECT id
         FROM egp.users
         WHERE name LIKE $1 OR surname LIKE $1 OR email LIKE $1
         LIMIT 20"; //hardcoded limit for now
}