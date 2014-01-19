<?php

/**
 * Interface which defines all the search methods in EGP
 * @author eugene
 */
interface ISearchService {

    function search(SearchRequest $request, Paging &$paging);

}
