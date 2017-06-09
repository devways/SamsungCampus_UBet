<?php
class users extends Model{
    protected $table = 'user';

    function getUserById($id){
        $sql = "SELECT * FROM ".$this->table." WHERE id=:id";
        $query = self::$_pdo->prepare($sql);
        $query->bindParam(":id", $id);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    function userExists($post) {
        $sql = "SELECT * FROM ".$this->table." WHERE username=:username OR email=:email";
        $query = self::$_pdo->prepare($sql);
        $query->bindParam(":username", $post['username']);
        $query->bindParam(":email", $post['email']);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    function userPaypal($id, $payer) {
        $sql = "UPDATE user SET id_paypal=:payer WHERE id=:id";
        $query = self::$_pdo->prepare($sql);
        $query->bindParam(":payer", $payer);
        $query->bindParam(":id", $_SESSION['auth']['id']);
        $query->execute();
    }

    function connectUser($post) {
        $sql = "SELECT * FROM ".$this->table." WHERE username=:username OR email=:username";
        $query = self::$_pdo->prepare($sql);
        $query->bindParam(":username", $post['username']);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    function subscribeUser($post) {
        $sql = "INSERT INTO ".$this->table." (username, password, email) VALUES (:username, :password, :email)";
        $query = self::$_pdo->prepare($sql);
        $query->bindParam(":username", $post['username']);
        $query->bindParam(":password", $post['password']);
        $query->bindParam(":email", $post['email']);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    function updateSolde($solde) {
        $sql = "UPDATE ".$this->table." SET solde=:solde WHERE id=:user";
        $query = self::$_pdo->prepare($sql);
        $query->bindParam(":solde", $solde);
        $query->bindParam(":user", $_SESSION['auth']['id']);
        $query->execute();
    }

    function updateSoldeById($id, $solde) {
        $sql = "UPDATE ".$this->table." SET solde=:solde WHERE id = :user";
        $query = self::$_pdo->prepare($sql);
        $query->bindParam(":solde", $solde);
        $query->bindParam(":user", $id);
        $query->execute();
    }
}
?>