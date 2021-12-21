<?php
//This class is a main class where every Message is created,sent,received.
namespace App;

use App\SQLiteConnectionEstablisher;

class Message {

    //db instance
    private $db;


    //Connects the database to the $db instance
    private function connectToDb(){
        //Establishing the sqlite database connection
        $this->db = (new SQLiteConnectionEstablisher())->connect();
    }



    /*
        Function to retrieve userid for corresponding username from database.
        returns userid in type of integer
    */
    private function retrieveUserIdForUsername($username){

        //db is already connected by the caller function

        //SQL Query to get the corresponding line of the database for given username
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
          
        // Use of bindValue function with PARAM_STR to defend from SQLInjection
        $stmt->bindValue(':username', $username, \PDO::PARAM_STR);

        //Execution of the query
        $stmt->execute();
        
        //Fetching all the results into an array.
        $result = $stmt->fetchAll();

        //If the user for the corresponding name does not exist
        if($result[0][0] == null){
            throw new Exception('User Does Not Exist.');
        }

        //result should have 1 element. Returning that element as integer userid
        $userid = (int)$result[0][0];

        return $userid;

    }


    /*
        Function to retrieve username for corresponding userid from database.
        returns userid in type of integer
        Throws Exception which gets handled in the api call at index.php
     */
    private function retrieveUsernameForUserId($userid){

        //db is already connected by the caller function

        //SQL Query to get the corresponding line of the database for given userid
        $stmt = $this->db->prepare("SELECT * FROM users WHERE userid = :userid");
          
        // Use of bindValue function with PARAM_INT to defend from SQLInjection
        $stmt->bindValue(':userid', $userid, \PDO::PARAM_INT);

        //Execution of the query
        $stmt->execute();
        
        //Fetching all the results into an array.
        $result = $stmt->fetchAll();

        //If the user for the corresponding name does not exist
        if($result[0][0] == null){
            throw new Exception('User Does Not Exist.');
        }

        //result should have 1 element. Returning that element as username
        $username = $result[0][1];

        return $username;

    }



    /*
        Function to retrieve messages for the username from database.
        Returns all the messages for the user whether it is sent or received by the username.
     */
    public function retrieveMessageForId($username) {
        
        //Establishing database connection
        $this->connectToDb();

        //Getting userid for the username in order to get the messages from database
        $userid = $this->retrieveUserIdForUsername($username);

        //SQL Query to get the corresponding line of the database for given username
        $stmt = $this->db->prepare("SELECT * FROM messages WHERE receiverUserid = :userid OR senderUserid = :userid ORDER BY messageid ASC");
          
        // Use of bindValue function with PARAM_INT to defend from SQLInjection
        $stmt->bindValue(':userid', $userid, \PDO::PARAM_INT);

        //Execution of the query
        $stmt->execute();
        
        //Fetching all the results into an array.
        $result = $stmt->fetchAll();

        //result contains all the messages
        $messages = $result;

        /*This foreach loop , loops through the messages and creates an array of arrays.
        Each array contains messageid, sendername, receivername and the message string.
        This aforementioned array of arrays will be converted into a jSON before the return of it in api call.
        */
        $message_array = array();
        foreach($messages as $row) {
            $messageid = $row["messageid"];

            $senderusername = $this->retrieveUsernameForUserId($row["senderUserid"]);
            $receiverUsername = $this->retrieveUsernameForUserId($row["receiverUserid"]);

            $message = $row["message"];
            $message_instance = array($messageid,$senderusername,$receiverUsername,$message);
            array_push($message_array,$message_instance);
        }

        //closing connection
        $db = null;

        //No need to encode message array since it will be encoded as json in return data.
        return $message_array;

    }



    /*
    Generates message id by incrementing the largest message id in the database
    and returning that value as integer.
    */
    private function generateMessageId(){

        //Establishing database connection
        $this->connectToDb();

        //SQL Query to get the list of messageids with ascending order
        $stmt = $this->db->prepare("SELECT messageid FROM messages  ORDER BY messageid DESC");

        //Execution of the query
        $stmt->execute();
        
        //Fetching all the results into an array.
        $result = $stmt->fetchAll();

        $resultVal = (int)$result[0][0];

        //incrementing this int value in order to create new messageid
        $resultVal = $resultVal + 1;

        return $resultVal;

    }



    /*
        Function add a new message to database by given parameters.
     */
    public function sendMessageForUsername($senderusername,$receiverusername,$messageStr){

        //Establishing database connection
        $this->connectToDb();

        //Getting userid for the senderusername
        $senderuserid = $this->retrieveUserIdForUsername($senderusername);

        //Getting userid for the receiverusername
        $receiveruserid = $this->retrieveUserIdForUsername($receiverusername);

        //Correct messageid needs to be generated.
        $messageid = $this->generateMessageId();

        //SQL Query to insert new messages into the database.
        $stmt = $this->db->prepare("INSERT INTO messages (messageid,senderUserid,receiverUserid,message) VALUES (:messageid , :senderuserid , :receiveruserid , :messageStr );");        

        // Use of bindValue function with PARAM_INT to defend from SQLInjection
        $stmt->bindValue(':messageid', $messageid, \PDO::PARAM_INT);        

        // Use of bindValue function with PARAM_INT to defend from SQLInjection
        $stmt->bindValue(':senderuserid', $senderuserid, \PDO::PARAM_INT);

        // Use of bindValue function with PARAM_INT to defend from SQLInjection
        $stmt->bindValue(':receiveruserid', $receiveruserid, \PDO::PARAM_INT);  
        
        // Use of bindValue function with PARAM_STR to defend from SQLInjection
        $stmt->bindValue(':messageStr', $messageStr, \PDO::PARAM_STR);        
        
        //Execution of the query
        $stmt->execute();
        
        //Fetching all the results into an array.
        $result = $stmt->fetchAll();

        //closing connection
        $db = null;

        //No need to encode message array since it will be encoded as json in return data.
        return $result;

    }

}