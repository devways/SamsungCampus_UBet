<?php
class odds extends Model {
    protected $table = 'events';

    function getOdd($category){
        $category = strtolower($category);
        $sql = "SELECT * FROM events WHERE category_event=:category AND deleted IS NULL";
        $query = self::$_pdo->prepare($sql);
        $query->bindParam(":category", $category);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    function getOddAll(){
        $sql = "SELECT * FROM ".$this->table." WHERE deleted IS NULL";
        $query = self::$_pdo->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    function getRankTeam($category, $team) {
        $category = strtolower($category);
        $team = strtolower($team);
        $sql = "SELECT * FROM team WHERE category=:category AND team=:team";
        $query = self::$_pdo->prepare($sql);
        $query->bindParam(":category", $category);
        $query->bindParam(":team", $team);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    function newRankTeam($category, $team, $rank) {
        $category = strtolower($category);
        $team = strtolower($team);
        $sql = "UPDATE team SET rank=:rank WHERE category=:category AND team=:team";
        $query = self::$_pdo->prepare($sql);
        $query->bindParam(":category", $category);
        $query->bindParam(":team", $team);
        $query->bindParam(":rank", $rank);
        $query->execute();
    }
}