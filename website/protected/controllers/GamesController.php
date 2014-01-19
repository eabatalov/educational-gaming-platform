<?php

/**
 * Description of GamesController
 *
 * @author eugene
 */
class GamesController extends EGPWebFrontendController {

    /*
     * Shows catalog with games
     */
    public function actionCatalog() {
        $this->requireAuthentification();
        $this->render("Catalog");
    }
}
