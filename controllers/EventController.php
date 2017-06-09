<?php
class event extends Controller {

    function indexAction(...$params){
        $modelsEvents = $this->loadModel('Events');
        if(!isset($params[0]) || $params[0] == '') {
            $data['events'] = $modelsEvents->getEventAll();
        } else {
            $data['events'] = $modelsEvents->getEvent($params[0]);
        // gestion interaction pour un utilisateur normal
        } if(isset($_SESSION['auth']) && $_SESSION['auth']['admin'] === '0') {
            if(!empty($this->data)) {
                // Si il y a des champs on verifie qu'il y a un id de l'evenement et que cest un nombre
                // puis on verifie que il ny  a que un champ sur 3 de remplit et que celui si est un nombre 
                if(!empty($this->data['id_event']) && is_numeric($this->data['id_event']) &&
                ((!empty($this->data['team_one']) && is_numeric($this->data['team_one']) && empty($this->data['null']) && empty($this->data['team_two'])) ||
                (empty($this->data['team_one']) && !empty($this->data['null']) && is_numeric($this->data['null']) && empty($this->data['team_two'])) ||
                (empty($this->data['team_one']) && empty($this->data['null']) && !empty($this->data['team_two']) && is_numeric($this->data['team_two'])))) {
                    // On verifie que l'evenement est valide et enssuite on verifie si le user a un solde sufisament eleve pour parier
                    $anEvent = $modelsEvents->getEventById($this->data['id_event'], 'beforeBegins');
                    if(!empty($anEvent) && !empty($this->data['team_one']) && $_SESSION['auth']['solde'] >= $this->data['team_one']) {
                        $choise_team = $anEvent['team_one_event'];
                        $sum = $this->data['team_one'];
                    } else if(!empty($anEvent) && !empty($this->data['null']) && $_SESSION['auth']['solde'] >= $this->data['null']) {
                        $choise_team = 'neutre';
                        $sum = $this->data['null'];
                    } else if(!empty($anEvent) && !empty($this->data['team_two']) && $_SESSION['auth']['solde'] >= $this->data['team_two']) {
                        $choise_team = $anEvent['team_two_event'];
                        $sum = $this->data['team_two'];
                    }
                    // si le champ choise_team existe on lance la requete pour valider et enregistrer le pari
                    // on actualise le solde du user
                    if(isset($choise_team)) {
                        $modelsEvents->betEvent($anEvent['id_event'], $choise_team, $sum);
                        $modelsUsers = $this->loadModel('Users');
                        $_SESSION['auth']['solde'] = $_SESSION['auth']['solde'] - $sum;
                        $modelsUsers->updateSolde($_SESSION['auth']['solde']);
                        $this->render('index', $data);
                    } else {
                        $this->render('index', $data);
                    }
                } else {
                    $this->render('index', $data);
                }
            } else {
                $this->render('index', $data);
            }
        // gestion interaction pour un utilisateur admin
        } else if (isset($_SESSION['auth']) && $_SESSION['auth']['admin'] !== '0') {
            if(!empty($this->data)) {
                if(isset($this->data['win'])) {
                    $modelsEvents = $this->loadModel('Events');
                    $win = explode(';', $this->data['win']);
                    $anEvent = $modelsEvents->getEventById($win[1], 'afterEnd');
                    if(!empty($anEvent) && $anEvent['winner_event'] === null) {
                        if($win[0] === $anEvent['team_one_event']) {
                            $score = 0;
                        } else {
                            $score = 1;
                        }
                        $modelsOdds = $this->loadModel('Odds');
                        $rank_1 = $modelsOdds->getRankTeam($anEvent['category_event'], $anEvent['team_one_event'])['rank'];
                        $rank_2 = $modelsOdds->getRankTeam($anEvent['category_event'], $anEvent['team_two_event'])['rank'];
                        $coteOne = round(1/$this->estimation($rank_1,$rank_2),2);
                        $coteTwo = round(1/$this->estimation($rank_2,$rank_1),2);
                        $modelsUser = $this->loadModel('Users');
                        $bet = $modelsEvents->getBetByEventId($anEvent['id_event']);
                        foreach($bet as $key => $value) {
                            $value['sum'] = intval($value['sum']);
                            $user = $modelsUser->getUserById($value['id_user']);
                            if($value['choise_team'] === $anEvent['team_one_event']) {
                                $newSolde = $user[0]['solde'] + ($value['sum']*$coteOne);
                            } else if($value['choise_team'] === $anEvent['team_two_event']) {
                                $newSolde = $user[0]['solde'] + ($value['sum']*$coteTwo);
                            }
                            $modelsUser->updateSoldeById($user[0]['id'], $newSolde);
                            $modelsEvents->updateBetValidation($value['id_bet']); 
                        }

                        $retour = $this->nouveau_rangs($rank_1, $rank_2, $score);
                        $elo_p1 = $retour[0];
                        $elo_p2 = $retour[1];
                        $modelsOdds->newRankTeam($anEvent['category_event'], $anEvent['team_one_event'], $elo_p1);
                        $modelsOdds->newRankTeam($anEvent['category_event'], $anEvent['team_two_event'], $elo_p2);
                        $modelsEvents->valideWinner($win[0], $win[1]);
                        if(!isset($params[0]) || $params[0] == '') {
                            $data['events'] = $modelsEvents->getEventAll();
                        } else {
                            $data['events'] = $modelsEvents->getEvent($params[0]);
                        }
                        $this->render('index', $data);
                    } else {
                        $this->render('index', $data);
                    }
                } else if (isset($this->data['delete'])) {
                    $modelsEvents = $this->loadModel('Events');
                    $anEvent = $modelsEvents->getEventById($this->data['delete'], 'beforeBegins');
                    if(!empty($anEvent)) {
                        $modelsEvents->deletEvent($anEvent['id_event']);
                        if(!isset($params[0]) || $params[0] == '') {
                            $data['events'] = $modelsEvents->getEventAll();
                        } else {
                            $data['events'] = $modelsEvents->getEvent($params[0]);
                        }
                        $this->render('index', $data);
                    } else {
                        $this->render('index', $data);
                    }
                } else {
                    $this->render('index', $data);
                }
            } else {
                $this->render('index', $data);
            }
        } else {
            $this->render('index', $data);
        }
    }
}