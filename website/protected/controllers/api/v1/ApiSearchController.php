<?php

/**
 * search API service request handler
 *
 * @author eugene
 */
class ApiSearchController extends ApiController {
    /*
     GET ( request : { query : String, object_type = ObjectType | "all" } ): get search results for query @query
	RETURNS: search_results: [ SearchResult ]

        Javascript types:
        SearchResult: { object_type : ObjectType, object : Object }
        ObjectType: "user" | ...
        Object : User | ...
     */
    public function actionSearch() {
        try {
            $this->requireAuthentification();
            $searchRequestApi = new SearchRequestApiModel();
            $searchRequestApi->initFromArray($this->getRequest());

            $searchService = new PostgresSearchService();
            $searchResults = $searchService->search($searchRequestApi->toSearchRequest());

            $searchResultsApi = array();
            foreach ($searchResults as $searchResult) {
                $searchResultApi = new SearchResultApiModel();
                $searchResultApi->initFromSearchResult($searchResult);
                $searchResultsApi[] = $searchResultApi->toArray($this->getFields());
            }

            $this->sendResponse(self::RESULT_SUCCESS, NULL, "search_results", $searchResultsApi);
        } catch (InvalidArgumentException $ex) {
            $message = $ex->getMessage();
            $this->sendResponse(self::RESULT_INVALID_ARGUMENT, $message);
        } catch (Exception $ex) {
            $this->sendInternalError($ex);
        }
    }
}
