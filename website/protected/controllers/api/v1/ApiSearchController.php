<?php

/**
 * search API service request handler
 *
 * @author eugene
 */
class ApiSearchController extends ApiController {
    public function actionSearch() {
        try {
            $this->requireAuthentification();
            $searchRequestApi = new SearchRequestApiModel();
            $searchRequestApi->initFromArray($this->getRequest());

            $searchService = new PostgresSearchService();
            $searchResults = $searchService->search(
                $searchRequestApi->toSearchRequest(), $this->getPaging());

            $searchResultsApi = array();
            foreach ($searchResults as $searchResult) {
                //TODO Add searchResults fields filtering for each object type here
                $searchResultApi = new SearchResultApiModel();
                $searchResultApi->initFromSearchResult($searchResult);
                $searchResultsApi[] = $searchResultApi->toArray();
            }

            $this->sendResponse(self::RESULT_SUCCESS, NULL, $searchResultsApi, TRUE);
        } catch (InvalidArgumentException $ex) {
            $message = $ex->getMessage();
            $this->sendResponse(self::RESULT_INVALID_ARGUMENT, $message);
        } catch (Exception $ex) {
            $this->sendInternalError($ex);
        }
    }
}
