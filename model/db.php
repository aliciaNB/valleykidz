<?php
/**
 * This class sets up a db object and includes pdo for db interactions
 * @author ValleyKidz team
 * @date 11/13/2019
 */

/*
************************* USER TABLES ***********************************

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

CREATE table admin(
    admin_id int(8) primary key,
    user_name varchar(255)
);

CREATE TABLE profilelinks(
    client_id int(8),
    clinician_id int(8),
    PRIMARY KEY(client_id, clinician_id),
    FOREIGN KEY (client_id) REFERENCES client(client_id),
    FOREIGN KEY (clinician_id) REFERENCES clinician(clinician_id)
);

************************* SAMPLE USERS ****************************************

INSERT INTO users (admin, client, password, user_id)
VALUES (0, 1, 'test', 123456), (0, 0, 'test', 1234), (1, 0, 'test', 2345);

INSERT INTO clinician (clinician_id, user_name) VALUES (1234, 'jelzughbi');
INSERT INTO client (client_id) VALUES (123456);
INSERT INTO admin (admin_id, user_name) VALUES (2345, 'admin');

************************ FORM TABLES *******************************************

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
	targetName varchar(255),
    isDefault boolean
);

Create Table skills
(
    skillId int AUTO_INCREMENT PRIMARY KEY,
    skillName varchar(255),
    skillCategory char(4),
    isDefault boolean,
    skillDescriptions varchar(255)
);

CREATE TABLE emotions
(
    emotionId int AUTO_INCREMENT PRIMARY KEY,
    emotionName varchar(255),
    isDefault boolean
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
	PRIMARY KEY(formId,targetId, dateSubmitted),
	FOREIGN KEY (formId) REFERENCES forms(formId),
	FOREIGN KEY (targetId) REFERENCES targets(targetId)
);

CREATE TABLE dateSubmissionsEmotions
(
	formId int,
    dateSubmitted dateTime,
	emotionId int,
	intensity int(1),
	PRIMARY KEY (formId, EmotionId, dateSubmitted),
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
	PRIMARY KEY (formId, skillId, dateSubmitted),
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

************************* DEFAULT FORM DATA ***********************************

INSERT INTO emotions (emotionId,emotionName,isDefault)
VALUES (1,'joy',1),(2,'gratitude',1),(3,'compassion',1),(4,'vulnerability',1),
(5,'self acceptance',1),(6,'sadness',1),(7,'depression',1),(8,'anger',1),(9,'frustration',1),(10,'anxiety',1);

INSERT INTO targets (targetName,isDefault)
VALUES ('suicidal ideation',1),('self harm',1), ('substance use',1), ('medication',1);

INSERT INTO skills (skillName, skillCategory,isDefault,skillDescriptions)
VALUES('wise mind', 'cm',1, "Accessed wisdom.  Know truth.  Be centered and calm.  Balanced Emotional Mind and Reasonable Mind.  Meditate."),
('observe', 'cm',1, "Just notice the experience.  \"Teflon mind.\"  Control your attention.  Smell the roses.  Experience\" what is happening."),
('describe','cm',1, "Put experiences into words.  Describe to yourself what is happening.  Put words on the experience."),
('participate','cm',1, "Enter into the experience.  Act intuitively from wise mind.  Practice changing the harmful and accepting yourself."),
('nonjudgmental stance', 'cm',1,"See but don't evaluate.  Unglue your opinions.  Accept each moment."),
('one-mindfully','cm',1,"Be in-the-moment.  Do one thing at a time.  Let go of distractions.  Concentrate your mind on the task at hand."),
('efectiveness','cm',1, "Focus on what works.  Learn the rules.  Play by the rules.  Act skillfully.  Let go of vengeance and useless anger.");

INSERT INTO skills (skillName, skillCategory, isDefault, skillDescriptions)
VALUES('objective effectiveness','ie',1, "DEAR MAN:  Describe. Express. Assert. Reinforce. Mindful. Appear confident. Negotiate."),
('relationship effectiveness','ie',1, "GIVE:  Gentle. Interested. Validation. Easy manner."),
('self-respect effectiveness','ie',1,"FAST:  Fair. No Apologies. Stick to values. Be Truthful.  Cheerleading.");

INSERT INTO skills(skillName, skillCategory, isDefault, skillDescriptions)
VALUES('identifying primary emotions','er',1,"Use the model of emotions to identify your primary emotions."),
('checking the facts','er',1,"Identify the facts of the situation (rather than thoughts, interpretations, or beliefs)."),
('problem solving','er',1,"Identify the problem, check the facts, identify your goal, brainstorm solutions, evaluate solutions, and put a solution into action."),
('opposite-to-emotion action','er',1,"Change emotions by acting opposite to the current emotion (when it isn't justified). Approach rather than avoid."),
('acquire positives in the Short-term','er',1,"Doing pleasurable things that you can do now."),
('acquire positives in the long-term','er',1,"Making choices that match morals and values."),
('build mastery','er',1,"Try to do one (hard or challenging) thing a day to make yourself feel competent and in control."),
('cope ahead','er',1,"Imagine how you would skillfully cope with a situation before you are in it."),
('please','er',1,"Reduce vulnerability, treat: Physical illness, balance Eating. Avoid drugs, balance Sleep. Exercise daily."),
('mindfulness to current emotion','er',1,null);

INSERT INTO skills(skillName,skillCategory, isDefault, skillDescriptions)
VALUES('tipp','dt',1, "Temperature.   Intense exercise.    Progressive muscle relaxation.  Paced breathing."),
('distract','dt',1,"Wise Mind ACCEPTS Activities.  Contributing.  Comparisons.  Emotions.  Pushing away.  Thoughts.  Sensations."),
('self-soothe','dt',1,"with the 5 senses.  Enjoy sights, sounds, smells, tastes and touch.  Be mindful of soothing sensations."),
('improve','dt',1,"the moment: Imagery.  Meaning.  Prayer.  Relaxation.  One thing in the moment.  Vacation.  Encouragement."),
('pros and cons','dt',1,"think about the +/- aspects of tolerating distress and the +/- aspects of not tolerating distress (engaging in impulsive behavior)"),
('half-smile','dt',1,"If you can't change your feelings, change your face. Create posture of acceptance, willingness, and openness to experience."),
('radical acceptance','dt',1,"Choose to recognize and accept reality.  Freedom from suffering = acceptance of facts from deep within / not approval."),
('turning the mind','dt',1,"Choosing over and over again to accept even though emotion mind wants to reject reality."),
('willingness','dt',1,"Doing what is needed in each situation.");

***************** SAMPLE FORM FOR SAMPLE USER 123456 ***********************************

INSERT INTO forms(clientId, startDate) VALUES (123456, '2019-11-07');
INSERT INTO formTargets (formId, targetId) VALUES (1, 1),(1,2),(1,3),(1,4);
INSERT INTO formEmotions(formId,emotionId) VALUES (1,1),(1,2),(1,3),(1,4),(1,5),(1,6),(1,7),(1,8),(1,9),(1,10);

*/

//----------------- DEFINE CONFIG FILE USED AND PATHING ----------------------------

$user = $_SERVER['USER'];
if ($user == NULL) {
    $path = "/home/valleyki/config.php";
} else if ($user == 'mbrittgr'){
    $path = "/home2/$user/config.php";
} else if ($user == 'abuehner') {
    $path = "/home/$user/valleykidz_config.php";
} else {
    $path = "/home/$user/config.php";
}
require_once($path);
require_once("validation.php");

//-------------------------- Start of Class ---------------------------------------

/**
 * Class database Creates a database connection using config file
 * and processes pdo requests
 */
class database
{
    private $_dbh;
    private $_errormessage;
    private $mysqli;

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
            $this->_dbh = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        } catch (PDOException $e) {
            $this->_errormessage = $e->getMessage();
        }
    }

    //----------------------------- Validate user in db ------------------------------------------

    /**
     * Finds out if user is a client/clinician then verifies password is correct
     * @param $userid String representation of the user information either # or username
     * @param $pass String reprsents password corelatting to db
     * @return string Error message or uuid of user request
     */
    public function getUser($userid, $pass)
    {
        $isAdmin = false;

        // check if client
        $sql = "SELECT * FROM users WHERE user_id=:user_id";
        $statement= $this->_dbh->prepare($sql);
        $statement->bindParam(":user_id", $userid, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result) {// Client
            $sql = "SELECT * FROM users WHERE user_id=:user_id and password=:pass";
            $statement= $this->_dbh->prepare($sql);
            $statement->bindParam(":user_id", $userid, PDO::PARAM_STR);
            $statement->bindParam(":pass", $pass, PDO::PARAM_STR);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
        } else {
            // check if clinician
            $sql = "SELECT * FROM clinician WHERE user_name=:user_id";
            $statement= $this->_dbh->prepare($sql);
            $statement->bindParam(":user_id", $userid, PDO::PARAM_STR);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $sql = "SELECT * FROM clinician INNER JOIN users 
                ON users.user_id = clinician.clinician_id WHERE clinician.user_name=:user_name and users.password=:pass";
                $statement= $this->_dbh->prepare($sql);
                $statement->bindParam(":user_name", $userid, PDO::PARAM_STR);
                $statement->bindParam(":pass", $pass, PDO::PARAM_STR);
                $statement->execute();
                $result = $statement->fetch(PDO::FETCH_ASSOC);
            } else {
                // check if admin
                $sql = "SELECT * FROM admin WHERE user_name=:user_id";
                $statement= $this->_dbh->prepare($sql);
                $statement->bindParam(":user_id", $userid, PDO::PARAM_STR);
                $statement->execute();
                $result = $statement->fetch(PDO::FETCH_ASSOC);

                if ($result) {
                    $sql = "SELECT * FROM admin INNER JOIN users 
                    ON users.user_id = admin.admin_id WHERE admin.user_name=:user_name and users.password=:pass";
                    $statement= $this->_dbh->prepare($sql);
                    $statement->bindParam(":user_name", $userid, PDO::PARAM_STR);
                    $statement->bindParam(":pass", $pass, PDO::PARAM_STR);
                    $statement->execute();
                    $result = $statement->fetch(PDO::FETCH_ASSOC);

                    if ($result) {
                        $isAdmin = true;
                    }
                } else {
                    return "User id does not exist";
                }
            }
        }

        //if all three are not correct this means only password is left
        if (!$result && !$isAdmin) {
            return "Password doest not match id";
        }

        //if user is admin return a different result other than 1 or 0
        if ($isAdmin) {
            return $result['client'] = "2";
        } else {
            return $result['client'];
        }
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
        if (!$result) {
            return "User ID does not exist";
        }
    }

    /**
     * Gets clinician id provided Clinician user_name
     * @param $user_name Represents username of clinician
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
     * Gets admin id provided admin user_name
     * @param $user_name Represents username of admin
     * @return mixed string representation of admin id
     */
    public function getAdminID($user_name)
    {
        $sql = "SELECT admin_id FROM admin WHERE user_name=:user_name";
        $statement= $this->_dbh->prepare($sql);
        $statement->bindParam(":user_name", $user_name, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        return $result['admin_id'];
    }

    //--------------------------- Add New User Accounts/Change PW ---------------------------------------------------

    /**
     * This function checks if the users db table already contains a client id.
     *
     * @param $clientId Client Id to check db for.
     * @return boolean If the client id exists already.
     */
    public function checkIfClientExists($clientId)
    {
        $sql = "SELECT client_id FROM `client` WHERE client_id=:client_id";
        $statement= $this->_dbh->prepare($sql);

        $statement->bindParam(":client_id", $clientId, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return true; //form not valid if result is true (already exists)
        } else {
            return false; //form valid if result is false
        }
    }

    /**
     * This function checks if a clinician account exists for creating a new
     * clinician account.
     *
     * @param $clnId User provided clinician Id.
     * @param $clnUsername User provided username.
     * @return boolean Clinician already exists.
     */
    public function checkIfClinicianExists($clnId, $clnUsername)
    {
        //FIXME might have to split this into two methods
        $sql = "SELECT clinician_id, user_name FROM `clinician` WHERE clinician_id=:clinician_id AND user_name=:user_name";
        $statement= $this->_dbh->prepare($sql);

        $statement->bindParam(":clinician_id", $clnId, PDO::PARAM_INT);
        $statement->bindParam(":user_name", $clnUsername, PDO::PARAM_STR);

        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return true; //form not valid if result is true (already exists)
        } else {
            return false; //form valid if result is false
        }
    }

    /**
     * This function checks if a client id already exists for updating
     * a client account password.
     *
     * @param $clientId User provided client id.
     * @return boolean client id exists.
     */
    public function checkIfClientIdExists($clientId)
    {
        $sql = "SELECT user_id, client FROM `users` WHERE user_id=:user_id AND client=:client";
        $statement= $this->_dbh->prepare($sql);

        $client = 1; //is a client

        $statement->bindParam(":user_id", $clientId, PDO::PARAM_INT);
        $statement->bindParam(":client", $client, PDO::PARAM_INT);

        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return true; //is a client account
        } else {
            return false; //is not a client account
        }
    }

    /**
     * This function checks if a clinician username exists for
     * updating clinician account password.
     *
     * @param $clnUsername User provided clinician username.
     * @return integer/boolean Clinician id or username does not exist.
     */
    public function checkIfClinicianUsernameExists($clnUsername)
    {
        $sql = "SELECT clinician_id FROM `clinician` WHERE user_name=:user_name";
        $statement= $this->_dbh->prepare($sql);

        $statement->bindParam(":user_name", $clnUsername, PDO::PARAM_STR);

        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            return $result['clinician_id']; //is a clinician account
        } else {
            return false; //is not a clinician account
        }
    }

    /**
     * This function inserts a New client Account into the db.
     *
     * @param $clientId int New client id.
     * @param $password string New client password.
     * @return boolean Success of new client account insert.
     */
    public function insertClientAccount($clientId, $password)
    {
        try {
            //insert statement for the user table
            $sql = "INSERT INTO users VALUES (:user_id, :password, :admin, :client);";
            $statement= $this->_dbh->prepare($sql);

            $admin = 0; //not a admin
            $client = 1; //is a client

            //bind params
            $statement->bindParam(":user_id", $clientId, PDO::PARAM_INT);
            $statement->bindParam(":password", $password, PDO::PARAM_STR);
            $statement->bindParam(":admin", $admin, PDO::PARAM_BOOL);
            $statement->bindParam(":client", $client, PDO::PARAM_BOOL);

            //run the statement
            $statement->execute();

            //FIXME may want to check if ($result) statement ran

            //insert statement for the client table
            $sql = "INSERT INTO client VALUES (:client_id);";
            $statement= $this->_dbh->prepare($sql);

            //bind param
            $statement->bindParam(":client_id", $clientId, PDO::PARAM_INT);

            //run the statement
            $statement->execute();

        } catch (PDOException $ex) {
            return false; //something went wrong
        }
        return true;
    }

    /**
     * This function inserts a new clinician account into the db.
     *
     * @param $clnId User provided clinician id.
     * @param $clnPassword User provided password.
     * @param $clnUsername User provided clinician username.
     * @return boolean Success of new clinician account insert.
     */
    public function insertClinicianAccount($clnId, $clnPassword, $clnUsername)
    {
        try {
            //insert clinician into users table
            $sql = "INSERT INTO users VALUES (:user_id, :password, :admin, :client);";
            $statement= $this->_dbh->prepare($sql);

            $admin = 0; //not a admin
            $cln = 0; //not a client

            //bind params
            $statement->bindParam(":user_id", $clnId, PDO::PARAM_INT);
            $statement->bindParam(":password", $clnPassword, PDO::PARAM_STR);
            $statement->bindParam(":admin", $admin, PDO::PARAM_BOOL);
            $statement->bindParam(":client", $cln, PDO::PARAM_BOOL);

            //run the statement
            $statement->execute();

            //FIXME may want to check if ($result) statement ran

            //insert into clinician table
            $sql = "INSERT INTO clinician VALUES (:clinician_id, :user_name);";
            $statement= $this->_dbh->prepare($sql);

            //bind param
            $statement->bindParam(":clinician_id", $clnId, PDO::PARAM_INT);
            $statement->bindParam(":user_name", $clnUsername, PDO::PARAM_STR);

            //run the statement
            $statement->execute();

        } catch (PDOException $ex) {
            return false; //something went wrong
        }
        return true;
    }

    /**
     * This function changes the password of a current client account in the db.
     *
     * @param $clientId Client id to update password for.
     * @param $newPassword New client password.
     * @return boolean update password success.
     */
    public function changeClientPassword($clientId, $newPassword)
    {
        try {
            //update client password in the db table
            $sql = "UPDATE users SET password=:password WHERE user_id=:user_id AND client=:client;";
            $statement = $this->_dbh->prepare($sql);

            $client = 1; //is a client

            //bind params
            $statement->bindParam(":user_id", $clientId, PDO::PARAM_INT);
            $statement->bindParam(":password", $newPassword, PDO::PARAM_STR);
            $statement->bindParam(":client", $client, PDO::PARAM_BOOL);

            //run the statement
            $statement->execute();
        } catch (PDOException $ex) {
            return false; //something went wrong
        }
        return true;
    }

    //TODO php doc
    public function changeClinicianPassword($clnId, $newPassword)
    {
        try {
            $sql = "UPDATE users SET password=:password WHERE user_id=:user_id AND client=:client;";
            $statement = $this->_dbh->prepare($sql);

            $client = 0; //clinician is not a client

            //bind params
            $statement->bindParam(":user_id", $clnId, PDO::PARAM_INT);
            $statement->bindParam(":password", $newPassword, PDO::PARAM_STR);
            $statement->bindParam(":client", $client, PDO::PARAM_BOOL);

            //run the statement
            $statement->execute();
        } catch (PDOException $ex) {
            return false; //something went wrong
        }
        return true;
    }

    //--------------------------- Update profiles links -----------------------------------------------------------

    /**
     * Takes a clinican and client id and links their profile if possible
     * @param $clinicianid String clinicians id
     * @param $clientid String clients id
     * @return string Error message  or inserts profile link into db
     */
    public function addClient($clinicianid, $clientid)
    {
        if ($this->isClient($clientid)) {//verify client exists
            if ($this->isLinked($clinicianid,$clientid)) {//verify if a link already exists
                return "Client is already connected to profile";
            } else {//link does not exist and client exists
                $sql= "INSERT INTO profilelinks(client_id, clinician_id) VALUES (:client, :clinician)";
                $statement = $this->_dbh->prepare($sql);
                $statement->bindParam("clinician", $clinicianid, PDO::PARAM_STR);
                $statement->bindParam("client", $clientid, PDO::PARAM_STR);
                $statement->execute();
            }
        } else {//client not found
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
        if ($this->isClient($clientid)) {//check if client number exists
            if (!($this->isLinked($clinicianid,$clientid))) {//link does not exist cant remove
                return "Customer Not Connected To Your Profile";
            } else {//link does exist remove from db
                $sql= "DELETE FROM profilelinks WHERE client_id=:client and clinician_id=:clinician";
                $statement = $this->_dbh->prepare($sql);
                $statement->bindParam("clinician", $clinicianid, PDO::PARAM_STR);
                $statement->bindParam("client", $clientid, PDO::PARAM_STR);
                $statement->execute();
            }
        } else {//id does not exist
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
        if ($uuid===null) {
            return "n";
        }
        $sql = "SELECT * FROM users WHERE user_id=:uuid";
        $statement = $this->_dbh->prepare($sql);
        $statement->bindParam("uuid", $uuid, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result['admin'] ==="1") {//is admin
            return "a";
        } elseif ($result['client']==="0") { //is clinician
            return "cln";
        } elseif ($result['client']==="1") {//is client
            return "cl";
        }
    }

    //--------------------------------- Insert defaults -----------------------------------------

    /**
     * Grabs default skills from db
     * @return mixed Array of all the default skills
     */
    public function getDefaultSkills()
    {
        $sql = "SELECT * FROM skills WHERE isDefault=1";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * Retrieves default emotions from db
     * @return mixed Array of default emotions
     */
    public function getDefaultEmotions()
    {
        $sql = "SELECT emotionId,emotionName FROM emotions WHERE isDefault=1";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * Retrieves default targets from db
     * @return mixed Array of defaul targets
     */
    public function getDefaultTargets()
    {
        $sql = "SELECT targetId,targetName FROM targets WHERE isDefault=1";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * Takes a form number in inserts default skills at that time into the associative table
     * @param $formId int form num being added to
     */
    public function insertDefaultSkills($formId)
    {
        $defaults =$this->getDefaultSkills();
        $sql = "INSERT INTO formSkills(formId,skillsId) VALUES";
        foreach ($defaults as $value) {
            $sql.='('.$formId.','.$value["skillId"].'),';
        }
        $statement = $this->_dbh->prepare(rtrim($sql, ','));
        $statement->execute();
    }

    /**
     * Takes a form number in inserts default emotions at that time into the associative table
     * @param $formId int form num being added to
     */
    public function insertDefaultEmotions($formId)
    {
        $defaults =$this->getDefaultEmotions();
        $sql = "INSERT INTO formEmotions(formId,emotionId) VALUES";
        foreach ($defaults as $value) {
            $sql.='('.$formId.','.$value["emotionId"].'),';
        }
        $statement = $this->_dbh->prepare(rtrim($sql, ','));
        $statement->execute();
    }

    /**
     * Takes a form number in inserts default targets at that time into the associative table
     * @param $formId int form num being added to
     */
    public function insertDefaultTargets($formId)
    {
        $defaults=$this->getDefaultTargets();
        $sql = "INSERT INTO formTargets(formId,targetId) VALUES";
        foreach ($defaults as $value) {
            $sql.='('.$formId.','.$value["targetId"].'),';
        }
        $statement = $this->_dbh->prepare(rtrim($sql, ','));
        $statement->execute();
    }


    //------------------------------------ Retrieve existing forms ---------------------------------------------
    /**
     * Takes a client Id number and returns all the targets on their current form
     * @param $clientId int client id
     * @return mixed associative array of the clients current targets
     */
    public function getFormTargets($clientId)
    {
        // Getting the current formId from the clientId
        $formId = $this->getCurrentFormId($clientId);

        // Getting the client's current targets and returning them
        $sql = "SELECT targetName FROM targets INNER JOIN formTargets ON targets.targetId = formTargets.targetId 
                WHERE formTargets.formId=:formId";
        $statement = $this->_dbh->prepare($sql);
        $statement->bindParam("formId", $formId, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * Takes a client Id number and returns all the emotions on their current form
     * @param $clientId int client id
     * @return mixed associative array of the clients current emotions
     */
    public function getFormEmotions($clientId)
    {
        // Getting the current formId from the clientId
        $formId = $this->getCurrentFormId($clientId);

        // Getting the client's current targets and returning them
        $sql = "SELECT emotionName FROM emotions INNER JOIN formEmotions ON emotions.emotionId = formEmotions.emotionId 
                WHERE formEmotions.formId=:formId";
        $statement = $this->_dbh->prepare($sql);
        $statement->bindParam("formId", $formId, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    // Gets the client's current form and returns the ID
    public function getCurrentFormId($clientId)
    {
        $sql = "SELECT formId FROM `forms` WHERE clientId=:clientId AND endDate IS NULL";
        $statement = $this->_dbh->prepare($sql);
        $statement->bindParam("clientId", $clientId, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result[0]['formId'];
    }

    // Gets the client's current form and returns the ID
    public function getRecentClosedFormId($clientId)
    {
        $sql = "SELECT formId FROM `forms` WHERE clientId=:clientId ORDER BY endDate DESC";
        $statement = $this->_dbh->prepare($sql);
        $statement->bindParam("clientId", $clientId, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result[0]['formId'];
    }

    /**
     * Gets the skills from the database and returns them
     * @return array An associative array of the skills sorted by category
     */
    public function getSkills()
    {
        // Getting all Core Mindfulness skills
        $sql = "SELECT skillName, skillDescriptions FROM skills WHERE skillCategory = 'cm' AND isDefault=1";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute();
        $cm = $statement->fetchAll(PDO::FETCH_ASSOC);

        // Getting all Interpersonal Effectiveness skills
        $sql = "SELECT skillName, skillDescriptions FROM skills WHERE skillCategory = 'ie' AND isDefault=1";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute();
        $ie = $statement->fetchAll(PDO::FETCH_ASSOC);

        // Getting all Emotion Regulation skills
        $sql = "SELECT skillName, skillDescriptions FROM skills WHERE skillCategory = 'er' AND isDefault=1";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute();
        $er = $statement->fetchAll(PDO::FETCH_ASSOC);

        // Getting all Distress Tolerance skills
        $sql = "SELECT skillName, skillDescriptions FROM skills WHERE skillCategory = 'dt' AND isDefault=1";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute();
        $dt = $statement->fetchAll(PDO::FETCH_ASSOC);

        $skills = array('Core Mindfulness'=>$cm, 'Interpersonal Effectiveness'=>$ie,
            'Emotion Regulation'=>$er, 'Distress Tolerance'=>$dt);
        return $skills;
    }

    /**
     * Selects recent open form and returns start date to determine how far back the date picker can go
     * @param $clientId current client from session information
     * @return array|null a result either the current start date of an open form or null if none exists
     * @throws Exception error if can not convert to a date time object
     */
    public function getDateRange($clientId)
    {
        $sql = "SELECT startDate FROM forms WHERE clientId=:clientId AND endDate IS NULL";
        $statement = $this->_dbh->prepare($sql);
        $statement->bindParam("clientId", $clientId, PDO::PARAM_STR);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        if ($results == null) {
            return null;
        }

        $startDate = new DateTime($results[0]['startDate']);
        $currentDate = new DateTime('Today');
        $dateCounter = $startDate;
        $dateArray = array();

        while (true) {
            $dateArray[$dateCounter->format('l')] = array($dateCounter->format('M. d'),
                $dateCounter->format('Y-m-d'));
            if ($dateCounter == $currentDate) {
                break;
            }
            $dateCounter->add(new DateInterval('P1D'));
        }

        return $dateArray;
    }

    /**
     * Takes the post array from when the client submits their form and either adds or updates the database with the
     * date and new data being selected
     * @param $post The post array
     * @param $clientId The client's ID
     */
    public function submitClientData($post, $clientId)
    {
        $dataExists = $this->doesClientDataAlreadyExist($clientId, $post['date']);

        if ($dataExists) {
            $this->updateClientData($post, $clientId);
        } else {
            $this->addClientData($post, $clientId);
        }
    }

    /**
     * Post values for a form date submitted
     * @param $post represent a post array of values
     * @param $clientId client associated with form submisssion
     */
    private function addClientData($post, $clientId)
    {
        $formId = $this->getCurrentFormId($clientId);

        $this->addClientTargets($post['urges'], $post['actions'], $formId, $post['date']);
        $this->addClientEmotions($post['intensity'], $formId, $post['date']);
        $this->addClientSkills($post['degree'], $post['coreskills'], $formId, $post['date']);
        $this->addClientNotes($post['notes'], $formId, $post['date']);
    }

    /**
     * Updates existing db with new posted information
     * @param $post represents post array of a user
     * @param $clientId the client id that is submitting the form
     */
    private function updateClientData($post, $clientId)
    {
        $formId = $this->getCurrentFormId($clientId);

        $this->updateClientTargets($post['urges'], $post['actions'], $formId, $post['date']);
        $this->updateClientEmotions($post['intensity'], $formId, $post['date']);
        $this->updateClientSkills($post['degree'], $post['coreskills'], $formId, $post['date']);
        $this->updateClientNotes($post['notes'], $formId, $post['date']);
    }

    /**
     * Adds client targets to submittedTargets table
     * @param $urges number value of urgers selected
     * @param $actions true false of wether action was taken via checkbox input
     * @param $formId form number currently working with
     * @param $date the date of the form submission
     */
    private function addClientTargets($urges, $actions, $formId, $date)
    {
        $formTargets = $this->getCurrentFormTargets($formId);

        foreach ($formTargets as $targets) {
            $urge = ($urges[$targets[0]] == "" ? null : $urges[$targets[0]]);
            $action = ($actions[$targets[0]] == null ? 0 : $actions[$targets[0]]);

            $sql = "INSERT INTO dateSubmissionTargets (formId, targetId, dateSubmitted, urge, action) VALUES
            (:formId, :targetId, :date, :urge, :action)";
            $statement = $this->_dbh->prepare($sql);
            $statement->bindParam("urge", $urge, PDO::PARAM_INT);
            $statement->bindParam("action", $action, PDO::PARAM_INT);
            $statement->bindParam("formId", $formId, PDO::PARAM_STR);
            $statement->bindParam("targetId", $targets[1], PDO::PARAM_STR);
            $statement->bindParam("date", $date, PDO::PARAM_STR);
            $statement->execute();
            $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    /**
     * Update a existing target submitted date with new information
     * @param $urges urge 0-5 for the target updating
     * @param $actions true/false of wether action taken for current target
     * @param $formId the form id number currently being submitted
     * @param $date date of submission
     */
    private function updateClientTargets($urges, $actions, $formId, $date)
    {
        $formTargets = $this->getCurrentFormTargets($formId);

        foreach ($formTargets as $targets) {
            $urge = ($urges[$targets[0]] == "" ? null : $urges[$targets[0]]);
            $action = ($actions[$targets[0]] == null ? 0 : $actions[$targets[0]]);

            $sql = "UPDATE dateSubmissionTargets SET urge=:urge, action=:action 
            WHERE formId=:formId AND targetId=:targetId AND dateSubmitted=:dateSubmitted";
            $statement = $this->_dbh->prepare($sql);
            $statement->bindParam("urge", $urge, PDO::PARAM_INT);
            $statement->bindParam("action", $action, PDO::PARAM_INT);
            $statement->bindParam("formId", $formId, PDO::PARAM_STR);
            $statement->bindParam("targetId", $targets[1], PDO::PARAM_STR);
            $statement->bindParam("dateSubmitted", $date, PDO::PARAM_STR);
            $statement->execute();
            $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    /**
     * Add client emotion from submitted form
     * @param $intensities represents emotions level of intensity
     * @param $formId the form being submitted
     * @param $date the date the form is submitted on
     */
    private function addClientEmotions($intensities, $formId, $date)
    {
        $formEmotions = $this->getCurrentFormEmotions($formId);

        foreach ($formEmotions as $emotions) {
            $intensity = ($intensities[$emotions[0]] == "" ? null : $intensities[$emotions[0]]);

            $sql = "INSERT INTO dateSubmissionsEmotions (formId, dateSubmitted, emotionId, intensity) VALUES 
            (:formId, :date, :emotionId, :intensity)";
            $statement = $this->_dbh->prepare($sql);
            $statement->bindParam("intensity", $intensity, PDO::PARAM_INT);
            $statement->bindParam("formId", $formId, PDO::PARAM_STR);
            $statement->bindParam("emotionId", $emotions[1], PDO::PARAM_STR);
            $statement->bindParam("date", $date, PDO::PARAM_STR);
            $statement->execute();
            $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    private function updateClientEmotions($intensities, $formId, $date)
    {
        $formEmotions = $this->getCurrentFormEmotions($formId);

        foreach ($formEmotions as $emotions) {
            $intensity = ($intensities[$emotions[0]] == "" ? null : $intensities[$emotions[0]]);

            $sql = "UPDATE dateSubmissionsEmotions SET intensity=:intensity
            WHERE formId=:formId AND emotionId=:emotionId AND dateSubmitted=:dateSubmitted";
            $statement = $this->_dbh->prepare($sql);
            $statement->bindParam("intensity", $intensity, PDO::PARAM_INT);
            $statement->bindParam("formId", $formId, PDO::PARAM_STR);
            $statement->bindParam("emotionId", $emotions[1], PDO::PARAM_STR);
            $statement->bindParam("dateSubmitted", $date, PDO::PARAM_STR);
            $statement->execute();
            $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    private function addClientSkills($degrees, $coreskills, $formId, $date)
    {
        $allSkills = $this->getSkillsArray();

        foreach ($allSkills as $skillId => $skill) {
            $degree = ($degrees[$skillId - 1] == "" ? null : $degrees[$skillId - 1]);
            $used = ($coreskills[$skill] == null ? 0 : 1);

            $sql = "INSERT INTO dateSubmissionSkills (formId, dateSubmitted, skillId, degree, used) VALUES 
            (:formId, :date, :skillId, :degree, :used)";
            $statement = $this->_dbh->prepare($sql);
            $statement->bindParam("formId", $formId, PDO::PARAM_INT);
            $statement->bindParam("date", $date, PDO::PARAM_STR);
            $statement->bindParam("skillId", $skillId, PDO::PARAM_STR);
            $statement->bindParam("degree", $degree, PDO::PARAM_INT);
            $statement->bindParam("used", $used, PDO::PARAM_INT);
            $statement->execute();
            $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    private function updateClientSkills($degrees, $coreskills, $formId, $date)
    {
        $allSkills = $this->getSkillsArray();

        foreach ($allSkills as $skillId => $skill) {
            $degree = ($degrees[$skillId - 1] == "" ? null : $degrees[$skillId - 1]);
            $used = ($coreskills[$skill] == null ? 0 : 1);

            $sql = "UPDATE dateSubmissionSkills SET degree=:degree, used=:used 
            WHERE formId=:formId AND dateSubmitted=:date AND skillId=:skillId";
            $statement = $this->_dbh->prepare($sql);
            $statement->bindParam("formId", $formId, PDO::PARAM_INT);
            $statement->bindParam("date", $date, PDO::PARAM_STR);
            $statement->bindParam("skillId", $skillId, PDO::PARAM_STR);
            $statement->bindParam("degree", $degree, PDO::PARAM_INT);
            $statement->bindParam("used", $used, PDO::PARAM_INT);
            $statement->execute();
            $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    private function addClientNotes($note, $formId, $date)
    {
        $sql = "INSERT INTO noteSubmission (formId, dateSubmitted, noteInfo) VALUES
        (:formId, :date, :note)";
        $statement = $this->_dbh->prepare($sql);
        $statement->bindParam("formId", $formId, PDO::PARAM_INT);
        $statement->bindParam("date", $date, PDO::PARAM_STR);
        $statement->bindParam("note", $note, PDO::PARAM_STR);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    private function updateClientNotes($note, $formId, $date)
    {
        $sql = "UPDATE noteSubmission SET noteInfo=:note WHERE formId=:formId AND dateSubmitted=:date";
        $statement = $this->_dbh->prepare($sql);
        $statement->bindParam("formId", $formId, PDO::PARAM_INT);
        $statement->bindParam("date", $date, PDO::PARAM_STR);
        $statement->bindParam("note", $note, PDO::PARAM_STR);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getSkillsArray()
    {
        $sql = "SELECT * FROM skills";
        $statement = $this->_dbh->prepare($sql);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        $allSkills = array();

        foreach ($results as $result) {
            $allSkills[$result['skillId']] = $result['skillName'];
        }
        return $allSkills;
    }

    public function getCurrentFormTargets($formId)
    {
        $sql = "SELECT targetName, targets.targetId FROM targets LEFT JOIN formTargets ON targets.targetId = formTargets.targetId 
        WHERE formId=:formId";
        $statement = $this->_dbh->prepare($sql);
        $statement->bindParam("formId", $formId, PDO::PARAM_STR);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        $targetsArray = array();
        foreach ($results as $result) {
            array_push($targetsArray, array($result['targetName'], $result['targetId']));
        }

        return $targetsArray;
    }

    private function getCurrentFormEmotions($formId)
    {
        $sql = "SELECT emotionName, emotions.emotionId FROM emotions LEFT JOIN formEmotions 
        ON emotions.emotionId = formEmotions.emotionId WHERE formId=:formId";
        $statement = $this->_dbh->prepare($sql);
        $statement->bindParam("formId", $formId, PDO::PARAM_STR);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        $emotionsArray = array();
        foreach ($results as $result) {
            array_push($emotionsArray, array($result['emotionName'], $result['emotionId']));
        }

        return $emotionsArray;
    }

    private function doesClientDataAlreadyExist($clientId, $date)
    {
        $formId = $this->getCurrentFormId($clientId);

        $sql = "SELECT skillId FROM dateSubmissionSkills WHERE formId=:formId AND dateSubmitted=:currentdate";
        $statement = $this->_dbh->prepare($sql);
        $statement->bindParam("formId", $formId, PDO::PARAM_STR);
        $statement->bindParam("currentdate", $date, PDO::PARAM_STR);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $results != null;
    }

    /**
     * Takes the client ID and the selected date and returns an associative array of the client's targets, emotions,
     * skills, and notes for the selected date
     * @param $clientId The client's ID number
     * @param $date The selected date
     * @return mixed An associative array of the client's targets, emotions, skills, and notes
     */
    public function getClientFormData($clientId, $date)
    {
        $formId = $this->getCurrentFormId($clientId);

        $clientData['targets'] = $this->getClientTargetData($formId, $date);
        $clientData['emotions'] = $this->getClientEmotionData($formId, $date);
        $clientData['skills'] = $this->getClientSkillData($formId, $date);
        $clientData['notes'] = $this->getClientNotesData($formId, $date);

        return $clientData;
    }

    private function getClientTargetData($formId, $date)
    {
        $sql = "SELECT targetName, urge, action FROM targets INNER JOIN dateSubmissionTargets 
        ON targets.targetId=dateSubmissionTargets.targetId WHERE formId=:formId AND dateSubmitted=:date";
        $statement = $this->_dbh->prepare($sql);
        $statement->bindParam("formId", $formId, PDO::PARAM_STR);
        $statement->bindParam("date", $date, PDO::PARAM_STR);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        $targets = array();
        foreach ($results as $result) {
            $targets[$result['targetName']] = array($result['urge'], $result['action']);
        }
        return $targets;
    }

    private function getClientEmotionData($formId, $date)
    {
        $sql = "SELECT emotionName, intensity FROM emotions INNER JOIN dateSubmissionsEmotions 
        ON emotions.emotionId=dateSubmissionsEmotions.emotionId WHERE formId=:formId AND dateSubmitted=:date";
        $statement = $this->_dbh->prepare($sql);
        $statement->bindParam("formId", $formId, PDO::PARAM_STR);
        $statement->bindParam("date", $date, PDO::PARAM_STR);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        $emotions = array();
        foreach ($results as $result) {
            $emotions[$result['emotionName']] = $result['intensity'];
        }
        return $emotions;
    }

    private function getClientSkillData($formId, $date)
    {
        $sql = "SELECT skillName, degree, used FROM skills INNER JOIN dateSubmissionSkills 
        ON skills.skillId=dateSubmissionSkills.skillId WHERE formId=:formId AND dateSubmitted=:date";
        $statement = $this->_dbh->prepare($sql);
        $statement->bindParam("formId", $formId, PDO::PARAM_STR);
        $statement->bindParam("date", $date, PDO::PARAM_STR);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        $clientSkills = array();
        foreach ($results as $result) {
            $clientSkills[$result['skillName']] = array($result['degree'], $result['used']);
        }

        $allSkills = $this->getSkills();
        $skillsData = array();

        foreach ($allSkills as $coreSkill => $subSkills) {
            $tempArray = array();
            foreach ($subSkills as $skill) {
                $skillName = $skill['skillName'];
                $tempArray[$skillName] = array($clientSkills[$skillName][0],
                    $clientSkills[$skillName][1]);
            }
            $skillsData[$coreSkill] = $tempArray;
        }
        return $skillsData;
    }

    private function getClientNotesData($formId, $date)
    {
        $sql = "SELECT noteInfo FROM noteSubmission WHERE formId=:formId AND dateSubmitted=:date";
        $statement = $this->_dbh->prepare($sql);
        $statement->bindParam("formId", $formId, PDO::PARAM_STR);
        $statement->bindParam("date", $date, PDO::PARAM_STR);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $results[0]['noteInfo'];
    }

    //--------------------------------- Update  Form Table ------------------------------

    /**
     * Creates a new form with open end date and closes previous form
     * @param $clientId id of customer within db
     * @return mixed int of form id just created is returned
     */
    public function createForm($clientId)
    {
        //grab today's date
        $today = date("Y-m-d");

        $sql = "INSERT INTO forms (clientId, startDate) VALUES(:clientId, :startDate)";
        $statement= $this->_dbh->prepare($sql);
        $statement->bindParam(":clientId", $clientId, PDO::PARAM_INT);
        $statement->bindParam(":startDate", $today, PDO::PARAM_STR);
        $statement->execute();
        $id = $this->_dbh->lastInsertId();//retrive form num of new formid created
        return $id;
    }

    /**
     * Closes an open form of the client if it exists
     * @param $clientId represents client id in db
     */
    public function closeForm($clientId)
    {
        $sql = "SELECT * FROM forms WHERE clientId=:clientId and endDate is null";
        $statement= $this->_dbh->prepare($sql);
        $statement->bindParam(":clientId", $clientId, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if (isset($result)) { //has an open form
            //grab today's date
            $today = date("Y-m-d");
            $sql= "UPDATE forms SET endDate =:today WHERE clientId=:clientId and endDate IS NULL";
            $statement= $this->_dbh->prepare($sql);
            $statement->bindParam(":clientId", $clientId, PDO::PARAM_INT);
            $statement->bindParam(":today", $today, PDO::PARAM_STR);
            $statement->execute();
        }
    }

    //------------------------------ UPDAtE EMOTIONS ----------------------------------------------

    /**
     * Retrieves an emotion id if one exists from emotions table
     * @param $emotionString string name of emotion
     * @return mixed null if it does not exist otherwise returns id.
     */
    public function getEmotionId($emotionString)
    {
        $escapedString =filter_var($emotionString, FILTER_SANITIZE_STRING);
        $emotionString= strtolower($escapedString);//return escaped string and lowercase it
        $sql = "SELECT emotionId FROM emotions WHERE emotionName=:ename";
        $statement= $this->_dbh->prepare($sql);
        $statement->bindParam(":ename", $emotionString, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result['emotionId'];
    }

    /**
     * This method inserts new emotions into the emotion table and returns its id of new insert
     * @param $emotionString represents the name of an emotions entered in custom form
     * @return mixed int id of latest insert into table
     */
    public function insertEmotion($emotionString)
    {
        $escapedString=filter_var($emotionString, FILTER_SANITIZE_STRING);
        $emotionString= strtolower($escapedString);//return escaped string and lowercase it
        $sql = "INSERT INTO emotions (emotionName, isDefault) VALUES(:emotionString ,0);";
        $statement= $this->_dbh->prepare($sql);
        $statement->bindParam(":emotionString", $emotionString, PDO::PARAM_STR);
        $statement->execute();
        $id = $this->_dbh->lastInsertId();
        return $id;
    }

    /**
     * Insert customer emotions into associative table that produces the form for the client developed by clinician
     * @param $formNum current form num
     * @param $eId emotions Id num
     */
    public function insertCustomEmotions($formNum, $eId)
    {
        $sql = "INSERT INTO formEmotions (formId, emotionId) VALUES(:fid ,:eid )";
        $statement= $this->_dbh->prepare($sql);
        $statement->bindParam(":fid", $formNum, PDO::PARAM_INT);
        $statement->bindParam(":eid", $eId, PDO::PARAM_INT);
        $statement->execute();
    }

    //----------------------------- Update Targets ----------------------

    /**
     * Retrieves an taget id if one exists from target table
     * @param $targetString string name of target
     * @return mixed null if it does not exist otherwise returns id.
     */
    public function getTargetId($targetString)
    {
        $escapedString = filter_var($targetString, FILTER_SANITIZE_STRING);
        $targetString= strtolower($escapedString);//return escaped string and lowercase it
        $sql = "SELECT targetId FROM targets WHERE targetName=:tname";
        $statement= $this->_dbh->prepare($sql);
        $statement->bindParam(":tname", $targetString, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result['targetId'];
    }

    /**
     * This method inserts new target into the targets table and returns its id of new insert
     * @param $targetString represents the name of a target entered in custom form
     * @return mixed int id of latest insert into table
     */
    public function insertTarget($targetString)
    {
        $escapedString  =filter_var($targetString, FILTER_SANITIZE_STRING);
        $targetString= strtolower($escapedString);//return escaped string and lowercase it
        $sql = "INSERT INTO targets(targetName, isDefault) VALUES(:targetString ,0)";
        $statement= $this->_dbh->prepare($sql);
        $statement->bindParam(":targetString", $targetString, PDO::PARAM_STR);
        $statement->execute();
        $id = $this->_dbh->lastInsertId();
        return $id;
    }

    /**
     * Insert customer target into associative table that produces the form for the client developed by clinician
     * @param $formNum current form num
     * @param $tId targets id num
     */
    public function insertCustomTargets($formNum, $tid)
    {
        $sql = "INSERT INTO formTargets (formId, targetId) VALUES(:fid ,:tid )";
        $statement= $this->_dbh->prepare($sql);
        $statement->bindParam(":fid", $formNum, PDO::PARAM_INT);
        $statement->bindParam(":tid", $tid, PDO::PARAM_INT);
        $statement->execute();
    }

    /**
     * This will retrieve the custom emotions from the most recent form submitted by the client.
     * @param $clientId id of the client being worked with
     * @return mixed an array of custom emotion names
     */
    public function getRecentCustomEmotions($clientId)
    {
        $formId = $this->getRecentClosedFormId($clientId);//grab most currect form
        $sql = "SELECT emotions.emotionName FROM formEmotions INNER JOIN emotions on 
            formEmotions.emotionId = emotions.emotionId WHERE emotions.isDefault=0 AND formId=:formId";
        $statement= $this->_dbh->prepare($sql);
        $statement->bindParam(":formId", $formId, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * This will retrieve the custom targets from the most recent form submitted by the client
     * @param $clientId id of the client being worked with
     * @return mixed an array of customer target names.
     */
    public function getRecentCustomTargets($clientId)
    {
        $formId = $this->getRecentClosedFormId($clientId);//grab current form

        $sql = "SELECT targets.targetName FROM formTargets INNER JOIN targets on targets.targetId =
            formTargets.targetId WHERE formTargets.formId=:formId  AND targets.isDefault=0";
        $statement= $this->_dbh->prepare($sql);
        $statement->bindParam(":formId", $formId, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    //--------------------- Pull Forms --------------------------

    /**
     * Grabs a list of all current forms in sorted order by most current date to least current
     * @param $clientId the id of a customer
     * @return mixed an array of form information including id,start, and end date
     */
    public function getAllForms($clientId)
    {
        $sql="SELECT formId,startDate,endDate FROM forms WHERE clientId=:clientId ORDER BY (endDate IS NOT NULL), startDate DESC";
        $statement= $this->_dbh->prepare($sql);
        $statement->bindParam(":clientId", $clientId, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }


    /**
     * Retrieve emotion names from form id provided
     * @param $formId form numb provided for db retrieval
     * @return mixed return an array of form information which includes all emotions within form
     */
    public function getEmotionsFromForm($formId)
    {
        $sql="SELECT emotions.emotionName FROM formEmotions INNER JOIN emotions on formEmotions.emotionId = emotions.emotionId
            WHERE formEmotions.formId=:formId ORDER BY emotions.emotionId ASC";
        $statement= $this->_dbh->prepare($sql);
        $statement->bindParam(":formId", $formId, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * Grab emotions results from db between target dates with form id provided
     * @param $startDate starting date of form
     * @param $endDate ending date of form
     * @param $formId id of form
     * @return mixed a result of
     * @throws Exception
     */
    public function getEmotionsBetweenDates($startDate, $endDate, $formId)
    {
        $startDate= new DateTime($startDate);
        $startDate= $startDate->format("Y-m-d");
        $endDate = new DateTime($endDate);
        $endDate= $endDate->format("Y-m-d");

        $sql= "SELECT dateSubmissionsEmotions.dateSubmitted, emotions.emotionName, dateSubmissionsEmotions.intensity FROM dateSubmissionsEmotions 
        INNER JOIN emotions on dateSubmissionsEmotions.emotionId = emotions.emotionId WHERE dateSubmissionsEmotions.formId=:formId AND dateSubmissionsEmotions.dateSubmitted 
        BETWEEN :start and :end";

        $statement= $this->_dbh->prepare($sql);
        $statement->bindParam(":formId", $formId, PDO::PARAM_INT);
        $statement->bindParam(":start", $startDate, PDO::PARAM_STR);
        $statement->bindParam(":end", $endDate, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * Grabs all the targets associated to a form id returnin there name as an array
     * @param $formId from number provided associated to db
     * @return mixed returns array of targets names
     */
    public function getTargetsFromForm($formId)
    {
        $sql="SELECT targets.targetName from formTargets INNER JOIN targets on formTargets.targetId= targets.targetId WHERE formId =:formId 
        ORDER BY targets.targetId ASC";
        $statement= $this->_dbh->prepare($sql);
        $statement->bindParam(":formId", $formId, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * Retrieves all targets between two dates from a given form
     * @param $startDate the start date of the form
     * @param $endDate end date of the form
     * @param $formId represents from num in db
     * @return mixed returns an array including date submitted, urge, action, and name
     */
    public function getTargetsBetweenDates($startDate, $endDate, $formId)
    {
        $sql="SELECT dateSubmissionTargets.dateSubmitted, dateSubmissionTargets.urge, dateSubmissionTargets.action, 
        targets.targetName FROM dateSubmissionTargets INNER JOIN targets on 
        dateSubmissionTargets.targetId = targets.targetId WHERE dateSubmissionTargets.formId=:formId AND 
        dateSubmissionTargets.dateSubmitted BETWEEN :start and :end";

        $statement= $this->_dbh->prepare($sql);
        $statement->bindParam(":formId", $formId, PDO::PARAM_INT);
        $statement->bindParam(":start", $startDate, PDO::PARAM_STR);
        $statement->bindParam(":end", $endDate, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * Grabs all the skills associated to a form id returnin there name as an array
     * @param $formId from number provided associated to db
     * @return mixed returns array of skills names, catagory
     */
    public function getSkillsFromForm($formId)
    {
        // Getting all Core Mindfulness skills
        $sql = "SELECT skillName FROM skills INNER JOIN formSkills ON skills.skillId=formSkills.skillsId 
                WHERE skillCategory = 'cm' AND formId=:formId";
        $statement = $this->_dbh->prepare($sql);
        $statement->bindParam(":formId", $formId, PDO::PARAM_INT);
        $statement->execute();
        $cm = $statement->fetchAll(PDO::FETCH_ASSOC);

        // Getting all Interpersonal Effectiveness skills
        $sql = "SELECT skillName FROM skills INNER JOIN formSkills ON skills.skillId=formSkills.skillsId 
                WHERE skillCategory = 'ie' AND formId=:formId";
        $statement = $this->_dbh->prepare($sql);
        $statement->bindParam(":formId", $formId, PDO::PARAM_INT);
        $statement->execute();
        $ie = $statement->fetchAll(PDO::FETCH_ASSOC);

        // Getting all Emotion Regulation skills
        $sql = "SELECT skillName FROM skills INNER JOIN formSkills ON skills.skillId=formSkills.skillsId 
                WHERE skillCategory = 'er' AND formId=:formId";
        $statement = $this->_dbh->prepare($sql);
        $statement->bindParam(":formId", $formId, PDO::PARAM_INT);
        $statement->execute();
        $er = $statement->fetchAll(PDO::FETCH_ASSOC);

        // Getting all Distress Tolerance skills
        $sql = "SELECT skillName FROM skills INNER JOIN formSkills ON skills.skillId=formSkills.skillsId 
                WHERE skillCategory = 'dt' AND formId=:formId";
        $statement = $this->_dbh->prepare($sql);
        $statement->bindParam(":formId", $formId, PDO::PARAM_INT);
        $statement->execute();
        $dt = $statement->fetchAll(PDO::FETCH_ASSOC);

        $skills = array('Core Mindfulness'=>$cm, 'Interpersonal Effectiveness'=>$ie,
            'Emotion Regulation'=>$er, 'Distress Tolerance'=>$dt);
        return $skills;
    }

    /**
     * Retrieves the value of skills between two dates matching a form id
     * @param $startDate start date of form data requested
     * @param $endDate end date of form data requested
     * @param $formId form that data is desired from
     * @return mixed null if no data exists or an array of skill information containing degree,used,skillname and date
     * @throws Exception if datetime object can not be created
     */
    public function getSkillsBetweenDates($startDate, $endDate, $formId)
    {
        $startDate= new DateTime($startDate);
        $startDate= $startDate->format("Y-m-d");
        $endDate = new DateTime($endDate);
        $endDate= $endDate->format("Y-m-d");

        $sql= "SELECT dateSubmitted, skillName, degree, used FROM dateSubmissionSkills 
        INNER JOIN skills on dateSubmissionSkills.skillId = skills.skillId WHERE dateSubmissionSkills.formId=:formId 
        AND dateSubmitted 
        BETWEEN :start and :end";

        $statement= $this->_dbh->prepare($sql);
        $statement->bindParam(":formId", $formId, PDO::PARAM_INT);
        $statement->bindParam(":start", $startDate, PDO::PARAM_STR);
        $statement->bindParam(":end", $endDate, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
}
