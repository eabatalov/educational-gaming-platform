<?php

/**
 * Controller is responsible for handling of login/signup requests
 * for different login providers.
 * @author eugene
 */
class DefaultController extends CController {
    /**
     * Handle for user's signup or login through some login provider
     */
    public function actionIndex() {
        //try {
            if (!isset(Yii::app()->session['hybridauth-referrer'])) {
                Yii::app()->session['hybridauth-referrer'] = Yii::app()->request->urlReferrer;
            }
            $this->_doHybAuth();
        //} catch (Exception $e) {
            /*
             * TODO we need some rollback of DB here.
             * We could get Exception in the middle of the process
             * At least we should log fatal exceptions
             */
            //Yii::app()->user->setFlash('hybridauth-error', "Something went wrong, did you cancel?");
            //$this->redirect(Yii::app()->session['hybridauth-referrer'], true);
        //}
    }

    /**
     * Main method to handle login attempts.  If the user passes authentication with their
     * chosen provider then it displays a form for them to choose their username and email.
     * The email address they choose is *not* verified.
     * 
     * If they are already logged in then it links the new provider to their account
     * 
     * @throws Exception if a provider isn't supplied, or it has non-alpha characters
     */
    private function _doHybAuth() {
        if (!isset($_GET['provider']))
            throw new Exception("You haven't supplied a provider");

        if (!ctype_alpha($_GET['provider'])) {
            throw new Exception("Invalid characters in provider string");
        }


        $identity = new HybridAuthIdentity($_GET['provider'], $this->module->getHybridauth());

        if ($identity->authenticate()) {
            // They have authenticated AND we have a user record associated with that provider
            if (Yii::app()->user->isGuest) {
                $this->_loginUser($identity);
            } else {
                //they shouldn't get here because they are already logged in AND have a record for
                // that provider.  Just bounce them on
                $this->redirect(Yii::app()->user->returnUrl);
            }
        } else if ($identity->errorCode == HybridAuthIdentity::ERROR_USERNAME_INVALID) {
            // They have authenticated to their provider but we don't have a matching HaLogin entry
            if (Yii::app()->user->isGuest) {
                // They aren't logged in => display a form to choose their username & email 
                // (we might not get it from the provider)
                if ($this->module->withYiiUser == true) {
                    Yii::import('application.modules.user.models.*');
                } else {
                    Yii::import('application.models.*');
                }

                $haProfile = $identity->getAdapter()->getUserProfile();                            
                $user = User::createInstance($haProfile->email,
                    $haProfile->firstName, $haProfile->lastName, TRUE, UserRole::CUSTOMER);

                if ($user->validate()) {
                    $userStorage = new PostgresUserStorage();
                    //TODO make random password generation in this case
                    $hauthPassword = "HybAuth:make_it_random!";
                    $userStorage->addUser($user, $hauthPassword);
                    $authUser = $userStorage->getAuthentificatedUser($user->getEmail(), $hauthPassword);

                    if ($this->module->withYiiUser == true) {
                        throw new Exception("Not implemented");
                        $profile = new Profile();
                        $profile->first_name=$haProfile->firstName;
                        $profile->last_name=$haProfile->lastName;
                        $profile->user_id=$user->id;
                        $profile->save();
                    }

                    $identity->setAuthUser($authUser);
                    $this->_linkProvider($identity);
                    $this->_loginUser($identity);
                } else {
                    //TODO what we do here? Manual register?
                    throw new Exception("Registering user for whom we 
                            couldn't create valid User instance is not implemented");
                }
            } else {
                // They are already logged in, link their user account with new provider
                $identity->setId(Yii::app()->user->id);
                $this->_linkProvider($identity);
                $this->redirect(Yii::app()->user->returnUrl);
            }
        }
    }

    private function _linkProvider($identity) {
        $hauthStorage = new PostgresHybridAuthStorage();
        $hauthStorage->saveHAuthRecord(new HybridAuthRecord(
                $identity->getId(),
                $identity->getLoginProvider(),
                $identity->getLoginProviderIdentifier()));
    }

    private function _loginUser($identity) {
        Yii::app()->user->login($identity, 0);
        $this->redirect(Yii::app()->user->returnUrl);
    }

    /** 
     * Action for URL that Hybrid_Auth redirects to when coming back from providers.
     * Calls Hybrid_Auth to process login. 
     */
    public function actionCallback() {
        require dirname(__FILE__) . '/../Hybrid/Endpoint.php';
        Hybrid_Endpoint::process();
    }

    public function actionUnlink() {
        $login = HaLogin::getLogin(Yii::app()->user->getid(),$_POST['hybridauth-unlinkprovider']);
        $login->delete();
        $this->redirect(Yii::app()->getRequest()->urlReferrer);
    }
}
