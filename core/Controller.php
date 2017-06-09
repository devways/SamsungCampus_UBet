<?php
class Controller {
    protected $vars = array();
    protected $layout = 'default';
    protected $data = [];
    protected $euro;
    function __construct(){    
        if (isset($_POST)){
           $this->data = $_POST;
        }
    }

    function render($filename, $data = null){
        if($data !== null) {
            extract($data);
        }
        ob_start();
        require(ROOT.'views/'.get_class($this).'/'.$filename.'.php');
        $content_for_layout = ob_get_clean();
        if($this->layout == false){
            echo $content_for_layout;
        } else {
            require(ROOT.'views/layout/'.$this->layout.'.php');
        }
        unset($_SESSION['info']);
    }
    
    protected function loadModel($name){
        require_once(ROOT.'models/'.strtolower($name).'Model.php');
        return $this->$name = new $name();
    }

    protected function isAlphaNum($field) {
		if(!preg_match('/^[a-zA-Z0-9_ ]+$/', $field)) {
            return false;
		}
        return true;
	}

    protected function isDate($field) {
        if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $field)) {
            return false;
        }
        return true;
    }

    protected function isHour($field) {
        if(!preg_match("/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/", $field)) {
            return false;
        }
        return true;
    }

	protected function isEmail($field) {
		if(!filter_var($field, FILTER_VALIDATE_EMAIL)){
			return false;
		}
        return true;
	}

    protected function hashPassword($password) {
		return password_hash($password, PASSWORD_BCRYPT);
	}

	protected function isAlpha($field) {
		if(!preg_match('/^[a-zA-Z- ]+$/', $field)) {
			return false;
		}
        return true;
	}

    protected function setExpressCheckout($sum, $ret) {
        $url = 'https://api-3t.sandbox.paypal.com/nvp';
        
        $user = '******.gmail.com';
        $password = '***************';
        $signature = '*************************************';

        $params = array(
                'METHOD' => 'SetExpressCheckout',
                'VERSION' => 74.0,
                'USER' => $user,
                'PWD' => $password,
                'SIGNATURE' => $signature,
                'RETURNURL' => "http://localhost/ScoolProjects/Depot/B.E.T/user/deal/".$ret,
                'CANCELURL' => "http://localhost/ScoolProjects/Depot/B.E.T/user/deal/".$ret,
                'PAYMENTREQUEST_0_AMT' => $sum,
                'PAYMENTREQUEST_0_CURRENCYCODE' => 'EUR' 
        );

        $params = http_build_query($params);
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_VERBOSE => 1    
        ));

        $response = curl_exec($curl);
        $responseArray = array();
        parse_str($response, $responseArray);
        if(curl_errno($curl)) {
            curl_close($curl);
            die();
        } else {
            if($responseArray['ACK'] == 'Success') {
                header("Location: https://www.sandbox.paypal.com/webscr&cmd=_express-checkout&token=".$responseArray['TOKEN']);
                exit();
            } else {
                die();
            }
            curl_close($curl);
        }
        curl_close($curl);
    }

    protected function getExpressCheckout() {
        $url = 'https://api-3t.sandbox.paypal.com/nvp';
        
        $user = '************.gmail.com';
        $password = '****************';
        $signature = '***********************************************';

        $params = array(
                'METHOD' => 'GetExpressCheckoutDetails',
                'VERSION' => 74.0,
                'USER' => $user,
                'PWD' => $password,
                'SIGNATURE' => $signature,
                'TOKEN' => $_GET['token']
        );

        $params = http_build_query($params);
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_VERBOSE => 1    
        ));

        $response = curl_exec($curl);
        $responseArray = array();
        parse_str($response, $responseArray);
        if(curl_errno($curl)) {
            curl_close($curl);
            die();
        } else {
            if($responseArray['ACK'] != 'Success' || $responseArray['CHECKOUTSTATUS'] != 'PaymentActionNotInitiated') {
                header("Location: http://localhost/ScoolProjects/Depot/B.E.T/user/deal");
                exit();
            }
            curl_close($curl);
        }
        //curl_close($curl);
    }

    protected function doExpressCheckout($sum) {
        $url = 'https://api-3t.sandbox.paypal.com/nvp';
        
        $user = '*************.gmail.com';
        $password = '***************';
        $signature = '****************************************';

        $params = array(
                'METHOD' => 'DoExpressCheckoutPayment',
                'VERSION' => 74.0,
                'USER' => $user,
                'PWD' => $password,
                'SIGNATURE' => $signature,
                'TOKEN' => $_GET['token'],
                'PAYERID' => $_GET['PayerID'],
                'PAYMENTACTION' => 'Sale',
                'PAYMENTREQUEST_0_AMT' => $sum,
                'PAYMENTREQUEST_0_CURRENCYCODE' => 'EUR' 

        );

        $params = http_build_query($params);
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_VERBOSE => 1    
        ));

        $response = curl_exec($curl);
        $responseArray = array();
        parse_str($response, $responseArray);
        $_SESSION['transID'] = $responseArray['PAYMENTINFO_0_TRANSACTIONID'];
        $_SESSION['PayerID'] = $_GET['PayerID'];
        if(curl_errno($curl)) {
            curl_close($curl);
            die();
        } else {
            if($responseArray['ACK'] == 'Success') {
                $modelUser = $this->loadModel('Users');
                $user = $modelUser->getUserById($_SESSION['auth']['id']);
                if($user[0]['id_paypal'] === null) {
                    $modelUser->userPaypal($_SESSION['auth']['id'], $_SESSION['PayerID']);
                    $user = $modelUser->getUserById($_SESSION['auth']['id']);
                    $_SESSION['auth'] = $user[0];
                }
                $_SESSION['info'] = 'You have been buy '.$sum.' jeton';
                header("Location: http://localhost/ScoolProjects/Depot/B.E.T/user/deal");
                exit();
            } else {
                die();
            }
            curl_close($curl);
        }
        //curl_close($curl);
    }

    protected function refundTransaction($sum) {
        $url = 'https://api-3t.sandbox.paypal.com/nvp';
        
        $user = '********************.gmail.com';
        $password = '********************';
        $signature = '*********************************************';

        $params = array(
                'METHOD' => 'MassPay',
                'VERSION' => 74.0,
                'USER' => $user,
                'PWD' => $password,
                'SIGNATURE' => $signature,
                'RECEIVERTYPE' => 'UserID',
                'CURRENCYCODE' => 'EUR',
                'L_RECEIVERID0' =>  $_SESSION['auth']['id_paypal'],
                'L_AMT0' => $sum

        );

        $params = http_build_query($params);
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_VERBOSE => 1    
        ));

        $response = curl_exec($curl);
        $responseArray = array();
        parse_str($response, $responseArray);
        if(curl_errno($curl)) {
            curl_close($curl);
        } else {
            if($responseArray['ACK'] == 'Success') {
                $modelsUser = $this->loadModel('Users');
                $_SESSION['info'] = 'You have been sale '.($sum*1.1).' jeton';
                $_SESSION['auth']['solde'] = $_SESSION['auth']['solde']- ($sum*1.1);
                $sum = $_SESSION['auth']['solde'];
                $modelsUser->updateSolde($sum);
            } else {
                die();
            }
            curl_close($curl);
        }
        //curl_close($curl);
    }

    // protected function payViaPaypal($method = 'SetExpressCheckout') {
    //     $api_paypal = 'https://api-3t.sandbox.paypal.com/nvp?'; // Site de l'API PayPal. On ajoute déjà le ? afin de concaténer directement les paramètres.
    //     $version = 57.0; // Version de l'API
        
    //     $user = '**********************.gmail.com'; // Utilisateur API
    //     $pass = '**************'; // Mot de passe API
    //     $signature = '****************************************'; // Signature de l'API

    //     $api_paypal = $api_paypal.'VERSION='.$version.'&USER='.$user.'&PWD='.$pass.'&SIGNATURE='.$signature.
    //     "&METHOD=".$method.
    //     "&CANCELURL=".urlencode("http://localhost/ScoolProjects/Depot/B.E.T/user/deal/fails").
    //     "&RETURNURL=".urlencode("http://localhost/ScoolProjects/Depot/B.E.T/user/deal/success").
    //     "&AMT=".$this->data['euro'].
    //     "&CURRENCYCODE=EUR".
    //     "&DESC=".urlencode("JetoN FoR EveR").
    //     "&LOCALECODE=FR";


    //     $_SESSION['euro'] = $this->data['euro'];

    //     $ch = curl_init($api_paypal);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     $resultat_paypal = curl_exec($ch);
    //     if (!$resultat_paypal) {
    //         echo "<p>Erreur</p><p>".curl_error($ch)."</p>";
    //     } else {
    //         $liste_parametres = explode("&",$resultat_paypal); // Crée un tableau de paramètres
    //         foreach($liste_parametres as $param_paypal) // Pour chaque paramètre
    //         {
    //             list($nom, $valeur) = explode("=", $param_paypal); // Sépare le nom et la valeur
    //             $liste_param_paypal[$nom]=urldecode($valeur); // Crée l'array final
    //         }
    //         // Si la requête a été traitée avec succès
    //         if ($liste_param_paypal['ACK'] == 'Success') {
    //             // Redirige le visiteur sur le site de PayPal
    //             header("Location: https://www.sandbox.paypal.com/webscr&cmd=_express-checkout&token=".$liste_param_paypal['TOKEN']);
    //             exit();
    //         }
    //         else // En cas d'échec, affiche la première erreur trouvée.
    //         {echo "<p>Erreur de communication avec le serveur PayPal.<br />".$liste_param_paypal['L_SHORTMESSAGE0']."<br />".$liste_param_paypal['L_LONGMESSAGE0']."</p>";}		
    //     }
    //     curl_close($ch);
    // }

    //     protected function payPaypal($token, $payer) {
    //     $api_paypal = 'https://api-3t.sandbox.paypal.com/nvp?'; // Site de l'API PayPal. On ajoute déjà le ? afin de concaténer directement les paramètres.
    //     $version = 57.0; // Version de l'API
        
    //     $user = '**********************.gmail.com'; // Utilisateur API
    //     $pass = '***************'; // Mot de passe API
    //     $signature = '***************************************'; // Signature de l'API

    //     $api_paypal = $api_paypal.'VERSION='.$version.'&USER='.$user.'&PWD='.$pass.'&SIGNATURE='.$signature.
    //     "&METHOD=DoExpressCheckoutPayment".
    //     "&PAYMENTREQUEST_0_PAYMENTACTION=Sale".
    //     "&TOKEN=".urldecode($token).
    //     "&PAYERID=".urldecode($payer).
    //     "&AMT=".$_SESSION['euro'].
    //     "&CURRENCYCODE=EUR";

    //     var_dump($_SESSION['euro']);

    //     $ch = curl_init($api_paypal);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     $resultat_paypal = curl_exec($ch);
    //     if (!$resultat_paypal) {
    //         echo "<p>Erreur</p><p>".curl_error($ch)."</p>";
    //     } else {
    //         $liste_parametres = explode("&",$resultat_paypal); // Crée un tableau de paramètres
    //         foreach($liste_parametres as $param_paypal) // Pour chaque paramètre
    //         {
    //             list($nom, $valeur) = explode("=", $param_paypal); // Sépare le nom et la valeur
    //             $liste_param_paypal[$nom]=urldecode($valeur); // Crée l'array final
    //         }
    //         // Si la requête a été traitée avec succès
    //         if ($liste_param_paypal['ACK'] == 'Success') {
    //             // Redirige le visiteur sur le site de PayPal
    //             echo 'ok';
    //         }
    //         else // En cas d'échec, affiche la première erreur trouvée.
    //         {echo "<p>Erreur de communication avec le serveur PayPal.<br />".$liste_param_paypal['L_SHORTMESSAGE0']."<br />".$liste_param_paypal['L_LONGMESSAGE0']."</p>";}		
    //     }
    //     curl_close($ch);
    // }

    function estimation($elo_1, $elo_2) {
        $exp = ($elo_2 - $elo_1) / 400;
        return 1/ (1 + pow(10,$exp));
    }
    // Calcul de la nouvelle cote de P1
    function calcul_elo_p1($elo_1, $elo_2, $score) {
        $k = $this->valeur_k($elo_1);
        $estimation = $this->estimation($elo_1, $elo_2);
        $nouveau_rang = $elo_1 + $k * ($score - $estimation);
        // On ne veut personne en dessous d'une cote elo de 300
        if ($nouveau_rang < 300) {
            $nouveau_rang = 300;
        }
        return array($nouveau_rang, $estimation);
    }
    // Calcule la valeur de K en fonction de la cote du joueur
    function valeur_k($elo) {
        if ($elo < 1000) {
            $k = 80;
        }
        if ($elo >= 1000 && $elo < 2000) {
            $k = 50;
        }
        if ($elo >= 2000 && $elo <= 2400) {
            $k = 30;
        }
        if ($elo > 2400) {
            $k = 20;
        }
        return $k;
    }
    /* 
    // Calcul des nouvelles cotes de P1 et P2
    // score = 1 si P1 gagne
    // score = 0 si P1 perd
    // score = 0.5 s'il y a match nul
    */
    function nouveau_rangs($elo_1, $elo_2, $score) {
        // Score pour P2 VS P1
        $score_2 = 1 - $score;
        $calcul_p1 = $this->calcul_elo_p1($elo_1, $elo_2, $score);
        $estimation_p1 = $calcul_p1[1];
        $elo_p1 = round($calcul_p1[0]);
        $calcul_p2 = $this->calcul_elo_p1($elo_2, $elo_1, $score_2);
        $estimation_p2 = $calcul_p2[1];
        $elo_p2 = round($calcul_p2[0]);
        return array($elo_p1, $elo_p2, $estimation_p1, $estimation_p2);
    }
}
?>