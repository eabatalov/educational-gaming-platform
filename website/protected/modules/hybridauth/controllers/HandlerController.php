<?php

/**
 * Controller is responsible for handling of login/signup requests
 * for different login providers.
 * @author eugene
 */
class HandlerController extends EGPControllerBase {

    private $haIdentity;
    private $isSafeContext;

    public function __construct($id, $module = null) {
        parent::__construct($id, $module);
        $this->isSafeContext = FALSE;
    }
    /**
     * Handle for user's signup or login through some login provider
     */
    public function actionSignup() {
        if ($this->isSafeContext)
            $this->doHybSignUp();
        else
            $this->doActionSafe();
    }

    public function actionLogin() {
        if ($this->isSafeContext)
            $this->doHybLogin();
        else
            $this->doActionSafe();
    }

    private function doActionSafe() {
        try {
            if (!isset(Yii::app()->session['hybridauth-referrer'])) {
                Yii::app()->session['hybridauth-referrer'] = Yii::app()->request->urlReferrer;
            }
            $providerName = $this->getGETVal('provider');
            if (!ctype_alpha($providerName)) {
                throw new Exception("Invalid characters in provider string");
            }
            $this->haIdentity =
                new HybridAuthIdentity($providerName, $this->module->getHybridauth());
            $this->isSafeContext = TRUE;
            //Main work is here
            $this->action->run();
        } catch (Exception $ex) {
            /*
             * TODO we need some rollback of DB here.
             * We could get Exception in the middle of the process
             * At least we should log fatal exceptions
             */
            Yii::app()->user->setFlash('hybridauth-error',
                "Sorry. Something went wrong." . PHP_EOL .
                TU::htmlFormatExceptionForUser($ex));
            $this->redirect(Yii::app()->session['hybridauth-referrer'], true);
        }
    }

    private function doHybLogin() {
        if ($this->haIdentity->authenticate()) {
            // They have authenticated AND we have a user record associated with that provider
            if (Yii::app()->user->isGuest) {
                $this->loginUser($this->haIdentity);
            } else {
                //they shouldn't get here because they are already logged in AND have a record for
                // that provider.  Just bounce them on
                $this->redirect(Yii::app()->user->returnUrl);
            }
        } else if ($this->haIdentity->errorCode == HybridAuthIdentity::ERROR_USERNAME_INVALID) {
            // They have authenticated to their provider but we don't have a matching HaLogin entry
            if (!Yii::app()->user->isGuest) {
                // They are already logged in, link their user account with new provider
                $this->haIdentity->setId(Yii::app()->user->id);
                $this->linkProvider($this->haIdentity);
                $this->redirect(Yii::app()->user->returnUrl);
            }
        }
        throw new InvalidArgumentException("Couldn't login.");
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
    private function doHybSignUp() {
        if (!Yii::app()->user->isGuest) {
            //they shouldn't get here because they are already logged in and thus signed up
            $this->redirect(Yii::app()->user->returnUrl);
        }

        if ($this->haIdentity->authenticate())
            throw new InvalidArgumentException('User has login provider record already. Can\'t signup.');

        if ($this->haIdentity->errorCode == HybridAuthIdentity::ERROR_USERNAME_INVALID) {
            /*They have authenticated to their provider but we don't have a matching HaLogin entry,
              current user is guest. Thus it is time for Signup scenario */
            /*TODO Display a form to choose their username & email (we might not get it from the provider)
              And then we can enable all the other providers on signup */
            if ($this->module->withYiiUser == true) {
                Yii::import('application.modules.user.models.*');
            } else {
                Yii::import('application.models.*');
            }

            $haProfile = $this->haIdentity->getAdapter()->getUserProfile();                      
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

                $this->haIdentity->setAuthUser($authUser);
                $this->linkProvider($this->haIdentity);
                $this->loginUser($this->haIdentity);
            } else {
                throw new Exception("Registering user for whom we 
                        couldn't create valid User instance is not implemented");
            }
        }
    }

    private function linkProvider($identity) {
        $hauthStorage = new PostgresHybridAuthStorage();
        $hauthStorage->saveHAuthRecord(new HybridAuthRecord(
                $identity->getId(),
                $identity->getLoginProvider(),
                $identity->getLoginProviderIdentifier()));
    }

    private function loginUser($identity) {
        AuthUtils::login($identity, 0);
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
