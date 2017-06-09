<?php
class user extends Controller {

    function index(){
        $modelsUser = $this->loadModel('Users');
        $data['user'] = $modelsUser->getUser(2);
        $this->render('index');
    }

    function subscribeAction(...$get) {
        if(!isset($_SESSION['auth'])) {
            if(empty($this->data)) {
                $this->render('subscribe');
            } else {
                $modelsUser = $this->loadModel('Users');
                $data = $modelsUser->userExists($this->data);
                if(empty($data) && $this->isAlphaNum($this->data['password']) && $this->isAlphaNum($this->data['confirmation_password']) &&
                 $this->isAlphaNum($this->data['username']) && $this->isEmail($this->data['email'])) {
                    $this->data['password'] = $this->hashPassword($this->data['password']);
                    $modelsUser->subscribeUser($this->data);
                    $_SESSION['info'] = 'subcribe successfuly';
                    header('Location: /ScoolProjects/Depot/B.E.T/index');
                } else {
                    $_SESSION['info'] = 'Having problem in your form';
                    header('Location: /ScoolProjects/Depot/B.E.T/user/subscribe');
                }
            }
        } else {
            header('Location: /ScoolProjects/Depot/B.E.T/index');
        }
    }

    function loginAction(...$get) {
        if(!isset($_SESSION['auth'])) {
            if(empty($this->data)) {
                $this->render('login');
            } else {
                $modelsUser = $this->loadModel('Users');
                $data = $modelsUser->connectUser($this->data);
                if(!empty($data) && password_verify($this->data['password'], $data[0]['password'])) {
                    $_SESSION['auth'] = $data[0];
                    $_SESSION['info'] = 'login successfully';
                    header('Location: /ScoolProjects/Depot/B.E.T/user/profilemanagement');
                } else {
                    $_SESSION['info'] = 'Having problem in your form';
                    header('Location: /ScoolProjects/Depot/B.E.T/login');
                }
            }
        } else {
            header('Location: /ScoolProjects/Depot/B.E.T/index');
        }
    }

    function logoutAction(...$get) {
        if(isset($_SESSION['auth'])) {
            unset($_SESSION['auth']);
            header('Location: /ScoolProjects/Depot/B.E.T/index');
        } else {
            header('Location: /ScoolProjects/Depot/B.E.T/index');
        }
    }

    function confirmedAction(...$get) {
        if(empty($this->data)) {
            $this->render('confirmed');
        }
    }

    function forgotAction(...$get) {
        if(empty($this->data)) {
            $this->render('forgot');
        }
    }

    function dealAction(...$get) {
        if(isset($_SESSION['auth']) && $_SESSION['auth']['admin'] === '0') {
            // Concerne les actions relative a l'achat de jeton
            if(isset($get[0]) && $get[0] === 'buy') {
                if(!empty($this->data) && $this->data['euro'] !== '' && $this->data['jeton'] !== '') {
                    $_SESSION['euro'] = $this->data['euro'];
                    $this->setExpressCheckout($_SESSION['euro'], 'buysuccess');
                } else {
                    $this->render('deal');
                }
            // Concerne les actions relative a la vente de jeton
            } elseif(isset($get[0]) && $get[0] === 'sale') {
                if(!empty($this->data) && $this->data['euro'] !== '' && $this->data['jeton'] !== '' && $_SESSION['auth']['id_paypal'] != null) {
                    $_SESSION['euro'] = $this->data['euro'];
                    $_SESSION['jeton'] = $this->data['jeton'];
                    $this->refundTransaction($_SESSION['euro']);
                    $this->render('deal');
                } else {
                    $this->render('deal');
                }
            // Conserne le retour de paypal sur notre site en cas de success
            } elseif(isset($get[0]) && $get[0] === 'buysuccess') {
                if(isset($_GET['token']) && isset($_GET['PayerID'])) {
                    $this->getExpressCheckout();
                    $this->doExpressCheckout($_SESSION['euro']);
                    $this->render('deal');
                } else {
                    $this->render('deal');
                }
            // Conserne la page de base sans interaction
            } else {
                $this->render('deal');
            }
        } else {
            $this->render('deal');
        }
    }

    function shopAction(...$get) {
        if(empty($this->data)) {
            $this->render('shop');
        }
    }

    function historyAction(...$get) {
        if(isset($_SESSION['auth'])) {
            $modelsUser = $this->loadModel('Users');
            $data = $modelsUser->getUserById($_SESSION['auth']['id']);
            $data = explode(';', $data[0]['betting']);
            die();
        }
        if(empty($this->data)) {
            $this->render('history');
        }
    }

    function bettingmanagementAction(...$get) {
        $modelsEvents = $this->loadModel('Events');
        if(isset($_SESSION['auth']) && $_SESSION['auth']['admin'] === '0') {
            if(!empty($this->data)) {
                $this->render('bettingmanagement');
            } else {
                $allBetForUser = $modelsEvents->getBetByUserId();
                
                die();
                $this->render('bettingmanagement');
            }
        } else {
            $this->render('bettingmanagement');
        }
    }

    function profilemanagementAction(...$get) {
        if(empty($this->data)) {
            $this->render('profilemanagement');
        }
    }

    function panelAction(...$get) {
        if(isset($_SESSION['auth']) && $_SESSION['auth']['admin'] === '1') {
            if(empty($this->data)) {
                $this->render('panel');
            } else {
                $eventModel = $this->loadModel('events');
                if($this->isAlphaNum($this->data['event']) && $eventModel->getCategory($this->data['category']) &&
                    $this->isAlphaNum($this->data['team_one']) && $this->isAlphaNum($this->data['team_two']) &&
                    $this->isDate($this->data['date_begin']) && $this->isHour($this->data['hour_begin']) &&
                    $this->isDate($this->data['date_end']) && $this->isHour($this->data['hour_end'])) {
                        if(empty($eventModel->getTeam($this->data['category'], $this->data['team_one']))) {
                            $eventModel->insertTeam($this->data['category'], $this->data['team_one']);
                        } if(empty($eventModel->getTeam($this->data['category'], $this->data['team_two']))) {
                            $eventModel->insertTeam($this->data['category'], $this->data['team_two']);
                        }
                        $_SESSION['info'] = 'events created successfuly';
                        $eventModel->addEvent($this->data);
                        $this->render('panel');
                } else {
                    $_SESSION['info'] = 'Having problem in your form';
                    $this->render('panel');
                }
            }
        } else {
            header('Location: /ScoolProjects/Depot/B.E.T/index');
        }
    }

    function bonjourAction() {
        echo 'Bonjour';
    }
}
?>