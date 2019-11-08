<?php
/*

CREATE TABLE users(
	user_id int(8) primary key not null,
    password varchar(255) not null,
    admin boolean,
    client boolean);

CREATE table client(
	client_id int(8) primary key
);

CREATE table clinician(
    clinician_id int(8) primary key,
    user_name varchar(255)
);

CREATE TABLE profilelinks(
    client_id int(8),
    clinician_id int(8),
    PRIMARY KEY(client_id, clinician_id),
    FOREIGN KEY (client_id) REFERENCES client(client_id),
    FOREIGN KEY (clinician_id) REFERENCES clinician(clinician_id)
);

************************Form Table*******************************************
CREATE TABLE forms
(
	formId int AUTO_INCREMENT PRIMARY KEY,
    clientId int,
    startDate dateTime,
    endDate dateTime,
    FOREIGN KEY(clientId) REFERENCES client(client_id)
);

CREATE TABLE targets
(
	targetId int PRIMARY KEY AUTO_INCREMENT,
	targetName varchar(255)
 );

Create Table skills
(
    skillId int AUTO_INCREMENT PRIMARY KEY,
    skillName varchar(255),
    skillCategory char(4)
);

CREATE TABLE emotions
(
    emotionId int AUTO_INCREMENT PRIMARY KEY,
    emotionName varchar(255)
);

Create Table formTargets
(
    formId int,
    targetId int,
	PRIMARY KEY(formId,targetId),
	FOREIGN KEY (formId) REFERENCES forms(formId),
	FOREIGN KEY (targetId) REFERENCES targets(targetId)
);

Create Table formEmotions
(
    formId int,
    emotionId int,
	PRIMARY KEY (formId, EmotionId),
	FOREIGN KEY (formId) REFERENCES forms(formId),
	FOREIGN KEY (emotionId) REFERENCES emotions(emotionId)
    );

Create Table formSkills
(
    formId int,
    skillsId int,
	PRIMARY KEY (formId, skillsId),
	FOREIGN KEY (formId) REFERENCES forms(formId),
	FOREIGN KEY (skillsId) REFERENCES skills(skillsId)
);


CREATE Table dateSubmissionTargets
(
    formId int,
    targetId int,
	dateSubmitted dateTime,
	urge int(1),
	action boolean,
	PRIMARY KEY(formId,targetId),
	FOREIGN KEY (formId) REFERENCES forms(formId),
	FOREIGN KEY (targetId) REFERENCES targets(targetId)
	);

CREATE TABLE dateSubmissionsEmotions
(
	formId int,
    dateSubmitted dateTime,
	emotionId int,
	intensity int(1),
	PRIMARY KEY (formId, EmotionId),
	FOREIGN KEY (formId) REFERENCES forms(formId),
	FOREIGN KEY (emotionId) REFERENCES emotions(emotionId)

);

CREATE TABLE dateSubmissionSkills
(
	formId int,
    dateSubmitted dateTime,
    skillId int,
	degree int(1),
	used boolean,
	PRIMARY KEY (formId, skillsId),
	FOREIGN KEY (formId) REFERENCES forms(formId),
	FOREIGN KEY (skillId) REFERENCES skills(skillId)
);

CREATE TABLE noteSubmission
(
    noteId int PRIMARY KEY AUTO_INCREMENT,
    formId int,
    dateSubmitted dateTime,
    noteInfo varchar(255),
   	FOREIGN KEY (formId) REFERENCES forms(formId)
);
*************************SAMPLE USERS****************************************

INSERT INTO users (admin, client, password, user_id) VALUES (0, 1, 'test', 123456), (0, 0, 'test', 1234);
INSERT INTO clinician (clinician_id, user_name) VALUES (1234, 'jelzughbhi');
INSERT INTO client (client_id) VALUES (123456);

UPDATE `clinician` SET `user_name` = 'jelzughbi' WHERE `clinician`.`clinician_id` = 1234;

 */
/**
 * @author Michael Britt
 * @version 1.0
 * Date: 10/22/2019
 * Class database connects to database for dbt
 */


//-----------------DEFINE CONFIG FILE USED AND PATHING----------------------------
$user = $_SERVER['USER'];
if ($user == NULL) {
    $path = "/home/valleyki/config.php";
} else if ($user == 'mbrittgr'){
    $path = "/home2/$user/config.php";
} else {
    $path = "/home/$user/config.php";
}

require_once($path);


//--------------------------Start of Class---------------------------------------

/**
 * Class database Creates a database connection using config file
 * and processes pdo requests
 */
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

    /**
     * Finds out if user is a client/clinician then verifies password is correct
     * @param $userid String representation of the user information either # or username
     * @param $pass String reprsents password corelatting to db
     * @return string Error message or uuid of user request
     */
    public function getUser($userid, $pass)
    {
        // check if client
        $sql = "SELECT * FROM users WHERE user_id=:user_id";
        $statement= $this->_dbh->prepare($sql);
        $statement->bindParam(":user_id", $userid, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result) // Client
        {
            $sql = "SELECT * FROM users WHERE user_id=:user_id and password=:pass";
            $statement= $this->_dbh->prepare($sql);
            $statement->bindParam(":user_id", $userid, PDO::PARAM_STR);
            $statement->bindParam(":pass", $pass, PDO::PARAM_STR);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
        }
        else // couldn't find user id
        {
            // check if clinician
            $sql = "SELECT * FROM clinician WHERE user_name=:user_id";
            $statement= $this->_dbh->prepare($sql);
            $statement->bindParam(":user_id", $userid, PDO::PARAM_STR);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            if ($result)
            {
                $sql = "SELECT * FROM clinician INNER JOIN users 
                ON users.user_id = clinician.clinician_id WHERE clinician.user_name=:user_name and users.password=:pass";
                $statement= $this->_dbh->prepare($sql);
                $statement->bindParam(":user_name", $userid, PDO::PARAM_STR);
                $statement->bindParam(":pass", $pass, PDO::PARAM_STR);
                $statement->execute();
                $result = $statement->fetch(PDO::FETCH_ASSOC);
            }
            else
            {
                return "User id does not exist";
            }
        }

        //if both are not correct this means only password is left
        if(!$result)
        {
            return "Password doest not match id";
        }
        return $result['client'];
    }

    /**
     * Retrieve an id from provided table if a user exists
     * @param $userid User Id provided of form
     * @return string If user id exists in users table
     */
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

    /**
     * Gets clinician id provided Clinician user_name
     * @param $user_name Represnet username of clinician
     * @return mixed string representation of clinician id
     */
    public function getClinicianID($user_name)
    {
        $sql = "SELECT clinician_id FROM clinician WHERE user_name=:user_name";
        $statement= $this->_dbh->prepare($sql);
        $statement->bindParam(":user_name", $user_name, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        return $result['clinician_id'];
    }

    /**
     * Takes a clinican and client id and links their profile if possible
     * @param $clinicianid String clinicians id
     * @param $clientid String clients id
     * @return string Error message  or inserts profile link into db
     */
    public function addClient($clinicianid, $clientid)
    {
        if($this->isClient($clientid))//verify client exists
        {
            if($this->isLinked($clinicianid,$clientid))//verify if a link already exists
            {
                return "Client is already connected to profile";
            }
            else//link does not exist and client exists
            {
                $sql= "INSERT INTO profilelinks(client_id, clinician_id) VALUES (:client, :clinician)";
                $statement = $this->_dbh->prepare($sql);
                $statement->bindParam("clinician", $clinicianid, PDO::PARAM_STR);
                $statement->bindParam("client", $clientid, PDO::PARAM_STR);
                $statement->execute();
            }
        }
        else//client not found
        {
            return "Client does not exist check with admin to add";
        }
    }

    /**
     * Check if a profile link exists
     * @param $clinicianid String Represent clinician id submitted from form
     * @param $clientid String Represents client id submitted from form
     * @return mixed Array or null representing found or not found links
     */
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

    /**
     * Checks if client # exists in client table
     * @param $clientid String representation of requested client #
     * @return mixed Array or null if found or not
     */
    public function isClient($clientid)
    {
        $sql = "SELECT * FROM client WHERE client_id=:client";
        $statement= $this->_dbh->prepare($sql);
        $statement->bindParam("client", $clientid, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * Retrieves all current profile links for clinician provided
     * @param $clinicianid String clinician id of db
     * @return mixed Null if no links Array if links
     */
    public function getLinks($clinicianid)
    {
        $sql = "SELECT client_id FROM `profilelinks` WHERE clinician_id=:clinician";
        $statement = $this->_dbh->prepare($sql);
        $statement->bindParam("clinician", $clinicianid, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * Removes client/clinician profilelink if possible
     * @param $clinicianid String represent clienician id in db
     * @param $clientid String represents client id in db
     * @return string Error message if fails or removes from db link connection.
     */
    public function removeClient($clinicianid, $clientid)
    {
        if($this->isClient($clientid))//check if client number exists
        {
            if(!($this->isLinked($clinicianid,$clientid)))//link does not exist cant remove
            {
                return "Customer Not Connected To Your Profile";
            }
            else//link does exist remove from db
            {
                $sql= "DELETE FROM profilelinks WHERE client_id=:client and clinician_id=:clinician";
                $statement = $this->_dbh->prepare($sql);
                $statement->bindParam("clinician", $clinicianid, PDO::PARAM_STR);
                $statement->bindParam("client", $clientid, PDO::PARAM_STR);
                $statement->execute();
            }
        }
        else//id does not exist
        {
            return "Client does not exist check with admin to add";
        }
    }

    /**
     * Find if user exists in db and of what type they are
     * @param $uuid String represent a uuid provided from sessions
     * @return string Represent the type of user either client,clinician,admin, or none
     */
    public function getuserType($uuid)
    {
        $sql = "SELECT * FROM users WHERE user_id=:uuid";
        $statement = $this->_dbh->prepare($sql);
        $statement->bindParam("uuid", $uuid, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if($result['admin'] ==="1")//is admin
        {
            return "a";
        }
        elseif ($result['client']==="0")//is clinician
        {
            return "cln";
        }
        elseif ($result['client']==="1")//is client
        {
            return "cl";
        }
        else//not any table
        {
            return "n";
        }
    }
}