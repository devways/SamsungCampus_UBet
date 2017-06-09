<?php
class events extends Model {
    protected $table = 'events';

    function getEvent($category){
        $category = strtolower(str_replace('_', ' ', $category));
        $sql = "SELECT * FROM ".$this->table." WHERE category_event=:category AND deleted IS NULL ORDER BY date_end_event DESC";
        $query = self::$_pdo->prepare($sql);
        $query->bindParam(":category", $category);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    function getEventById($id, $time = null) {
        date_default_timezone_set("Europe/Madrid");
        $date = date('Y-m-d H:i:s');
        $sql = "SELECT * FROM ".$this->table." WHERE id_event=:id AND deleted IS NULL ";
        if($time == 'beforeBegins') {
            $sql .= " AND :date < date_begin_event";
        } else if($time == 'afterBegins') {
            $sql .= " AND :date > date_begin_event";
        } else if($time == 'beforeEnd') {
            $sql .= " AND :date < date_end_event";
        } else if($time == 'afterEnd') {
            $sql .= " AND :date > date_end_event";
        }
        $query = self::$_pdo->prepare($sql);
        $query->bindParam(":id", $id);
        $query->bindParam(':date', $date);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    function getEventAll(){
        $sql = "SELECT * FROM ".$this->table." WHERE deleted IS NULL ORDER BY date_end_event DESC";
        $query = self::$_pdo->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    function addEvent($field) {
        $sql = "INSERT INTO ".$this->table." (name_event, category_event, team_one_event, team_two_event, date_begin_event, date_end_event) 
        VALUES (:name_event, :category_event, :team_one_event, :team_two_event, :date_begin_event, :date_end_event)";
        $query = self::$_pdo->prepare($sql);
        $date_begin = $field['date_begin'] . ' ' . $field['hour_begin'] . ':00';
        $date_end = $field['date_end'] . ' ' . $field['hour_end'] . ':00';
        $query->bindParam(":name_event", $field['event']);
        $query->bindParam(":category_event", $field['category']);
        $query->bindParam(":team_one_event", $field['team_one']);
        $query->bindParam(":team_two_event", $field['team_two']);
        $query->bindParam(":date_begin_event", $date_begin);
        $query->bindParam(":date_end_event", $date_end);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    function getCategory($category){
        $category = strtolower(str_replace('_', ' ', $category));
        $sql = "SELECT * FROM categories WHERE name_category=:category";
        $query = self::$_pdo->prepare($sql);
        $query->bindParam(":category", $category);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    function betEvent($event, $team, $sum) {
        $sql = "INSERT INTO betting (id_user, id_event, choise_team, sum) VALUES (:user, :event, :team, :sum)";
        $query = self::$_pdo->prepare($sql);
        $query->bindParam(":user", $_SESSION['auth']['id']);
        $query->bindParam(":event", $event);
        $query->bindParam(":team", $team);
        $query->bindParam(":sum", $sum);
        $query->execute();
    }

    function updateBetValidation($id_bet) {
        $sql = "UPDATE betting SET (validation=1) WHERE id_bet = :id_bet";
        $query = self::$_pdo->prepare($sql);
        $query->bindParam(":id_bet", $id_bet);
        $query->execute();
    }

    function getBetByUserId() {
        $sql = "SELECT * FROM betting WHERE id_user=:id";
        $query = self::$_pdo->prepare($sql);
        $query->bindParam(":id", $_SESSION['auth']['id']);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    function getBetByEventId($id_event) {
        $sql = "SELECT * FROM betting WHERE id_event=:id_event AND validation IS NULL";
        $query = self::$_pdo->prepare($sql);
        $query->bindParam(":id_event", $id_event);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    function valideWinner($winner, $id_event) {
        $sql = "UPDATE ".$this->table." SET winner_event=:winner WHERE id_event=:id_event";
        $query = self::$_pdo->prepare($sql);
        $query->bindParam(":winner", $winner);
        $query->bindParam(":id_event", $id_event);
        $query->execute();
    }

    function deletEvent($id_event) {
        $sql = "UPDATE ".$this->table." SET deleted=1 WHERE id_event=:id_event";
        $query = self::$_pdo->prepare($sql);
        $query->bindParam(":id_event", $id_event);
        $query->execute();
    }

    function getTeam($category, $team){
        $category = strtolower(str_replace('_', ' ', $category));
        $team = strtolower(str_replace('_', ' ', $team));
        $sql = "SELECT * FROM team WHERE category=:category AND team=:team";
        $query = self::$_pdo->prepare($sql);
        $query->bindParam(":category", $category);
        $query->bindParam(":team", $team);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    function insertTeam($category, $team){
        $category = strtolower(str_replace('_', ' ', $category));
        $team = strtolower(str_replace('_', ' ', $team));
        $sql = "INSERT INTO team (category, team) VALUES (:category, :team)";
        $query = self::$_pdo->prepare($sql);
        $query->bindParam(":category", $category);
        $query->bindParam(":team", $team);
        $query->execute();
    }
}