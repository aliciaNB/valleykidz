<?php
/*
 * CREATE TABLE users(
	user_id int(8) primary key not null,
    password varchar(255) not null,
    admin boolean,
    client boolean);

CREATE table client(
	client_id int(8) primary key
);

CREATE table clinician(
    clinician_id int(8) primary key
);


CREATE TABLE profilelinks(
    client_id int(8),
    clinician_id int(8),
    PRIMARY KEY(client_id, clinician_id),
    FOREIGN KEY (client_id) REFERENCES client(client_id),
    FOREIGN KEY (clinician_id) REFERENCES clinician(clinician_id)

)
 */
/**
 * @author Michael Britt
 * @version 1.0
 * Date: 10/22/2019
 * Class database connects to database for dbt
 */
$user = $_SERVER['USER'];
if($user == 'mbrittgr'){
    $path = "/home2/$user/config.php";
}
else{
    $path = "/home/$user/config.php";
}

require_once($path);
class database
{
    private $_dbh;
    private $_errormessage;

    /**
     * database constructor. Start out disconnected
     */
    public function __construct()
    {
        $this->connect();
    }

    /**
     * Attempts to connect to database, saves error message if not connected
     * @return void
     */
    public function connect()
    {
        try {
            $this->_dbh = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD
            );
        } catch (PDOException $e) {
            $this->_errormessage = $e->getMessage();
        }
    }
    public function getUser($userid, $pass)
    {
        //check for user id
        $check = $this->getId($userid);
        //return id error if not found
        if($check!=null)
        {
            return $check;
        }
        //else check for both id and pass
        $sql = "SELECT * FROM `users` WHERE user_id=:user_id and password=:pass";
        $statement= $this->_dbh->prepare($sql);
        $statement->bindParam(":user_id", $userid, PDO::PARAM_STR);
        $statement->bindParam(":pass", $pass, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        //if both are not correct this means only password is left
        if(!$result)
        {
            return "Password doest not match client id";
        }
        return $result['client'];

    }

    public function getId($userid)
    {
        $sql = "SELECT * FROM `users` WHERE user_id=:user_id";
        $statement= $this->_dbh->prepare($sql);
        $statement->bindParam(":user_id", $userid, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if(!$result)
        {
            return "User ID does not exist";
        }
    }

    public function addClient($clinicianid, $clientid)
    {
        if($this->isClient($clientid)) {
            if($this->isLinked($clinicianid,$clientid))
            {
                return "Customer Already Connected To Profile";
            }
        else{
            $sql= "INSERT INTO profilelinks(client_id, clinician_id) VALUES (:client, :clinician)";
            $statement = $this->_dbh->prepare($sql);
            $statement->bindParam("clinician", $clinicianid, PDO::PARAM_STR);
            $statement->bindParam("client", $clientid, PDO::PARAM_STR);
            $statement->execute();
        }
        }
        else{
            return "Client does not exist check with admin to add";
        }
    }
    public function isLinked($clinicianid, $clientid)
    {
        $sql = "SELECT * FROM `profilelinks` WHERE clinician_id=:clinician and client_id=:client";
        $statement = $this->_dbh->prepare($sql);
        $statement->bindParam("clinician", $clinicianid, PDO::PARAM_STR);
        $statement->bindParam("client", $clientid, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result;
    }
    public function isClient($clientid)
    {
        $sql = "SELECT * FROM `client` WHERE client_id=:client";
        $statement= $this->_dbh->prepare($sql);
        $statement->bindParam("client", $clientid, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getLinks($clinicianid)
    {
        $sql = "SELECT client_id FROM `profilelinks` WHERE clinician_id=:clinician";
        $statement = $this->_dbh->prepare($sql);
        $statement->bindParam("clinician", $clinicianid, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function removeClient($clinicianid, $clientid)
    {

        if($this->isClient($clientid)) {
            if(!($this->isLinked($clinicianid,$clientid)))
            {
                return "Customer Not Connected TO Your Profile";
            }
            else{
                $sql= "DELETE FROM profilelinks WHERE client_id=:client and clinician_id=:clinician";
                $statement = $this->_dbh->prepare($sql);
                $statement->bindParam("clinician", $clinicianid, PDO::PARAM_STR);
                $statement->bindParam("client", $clientid, PDO::PARAM_STR);
                $statement->execute();
            }
        }
        else{
            return "Client does not exist check with admin to add";
        }
    }
}