<?php
class odd extends Controller {

    function indexAction(...$params){
        $modelsOdds = $this->loadModel('Odds');
        if(!isset($params[0]) || $params[0] == '') {
            $data['events'] = $modelsOdds->getOddAll();
        } else {
            $data['events'] = $modelsOdds->getOdd($params[0]);
        }
        
        foreach($data['events'] as $key => $values) {
            $rank_1 = $modelsOdds->getRankTeam($data['events'][$key]['category_event'], $data['events'][$key]['team_one_event'])['rank'];
            $rank_2 = $modelsOdds->getRankTeam($data['events'][$key]['category_event'], $data['events'][$key]['team_two_event'])['rank'];
            if ((1/$this->estimation($rank_1,$rank_2)) > 4) {
                $data['events'][$key]['coteOne'] = 4;    
            } else {
                $data['events'][$key]['coteOne'] = round(1/$this->estimation($rank_1,$rank_2),2);
            } if ((1/$this->estimation($rank_2,$rank_1)) > 4) {
                $data['events'][$key]['coteTwo'] = 4;    
            } else {
                $data['events'][$key]['coteTwo'] = round(1/$this->estimation($rank_2,$rank_1),2);
            }
        }
        $this->render('index', $data);
    }
}