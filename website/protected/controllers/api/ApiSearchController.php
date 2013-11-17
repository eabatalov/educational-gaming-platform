<?php

/**
 * search API service request handler
 *
 * @author eugene
 */
class ApiSearchController extends ApiController {
    /*
     * GET ( request : { query : String } ): get search results for query @query
	RETURNS: search_results: [SearchResult]

        Javascript types:
        SearchResult: { object_type: "User" | "GameDescription" | ..., object: object of type object_type }
     */
    public function actionSearch() {
        
    }
}
