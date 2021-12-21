<?php
/**
 * This index.php file holds the slim api calls.
 * Handles the function calls from other files inside of the api calls.
 */


namespace App;
require 'vendor/autoload.php';

use App\Message;

//Simple configuration of Slim
$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

//Creation of new Slim App
$app = new \Slim\App(['settings' => $config]);


$container = $app->getContainer();

/*Retrieves messages for the given username in API call
    response given in the following order messageid,senderusername,receiverusername,message 
    sorted in ascending order of messageid

    POST can be a better method to use here for better security.
    GET can be used too although it might show up the secure data in url.
*/
$app->post('/getMessages', function($request, $response, $args){

    try {
        $message_retriever = new Message();

        //In order to get the body of api call.
        $username = $request->getParsedBody();

        //Retrieves all the messages into an array of arrays with function call.
        $result = $message_retriever->retrieveMessageForId($username["username"]);

        //Returns all the messages as Json with 200 OK code given.
        return $this->response->withStatus(200)->withJson($result);

    } catch (\Throwable $th) {

        //If an Exception happens in the function call above such as no user with the given name this section runs.
        $successArray = array("status"=> "False","message" => "ERROR : User does not exist." );
        return $this->response->withStatus(404)->withJson($successArray);
    }
    
});


/*
    Creates a new message by using given parameters with the help of functions in Message class.
    Adds that message into the database by giving it a unique messageid
    Params to the api call can be given as form-data which means key,value pairs.
    senderUsername : ...
    receiverUsername : ...
    message : ...
*/
$app->post('/postMessages', function ($request, $response, $args) {

    $message_retriever = new Message();
    
    try {
        //In order to get the body of api call.
        $allPostPutVars = $request->getParsedBody();

        //Function call with the api call body.
        $result = $message_retriever->sendMessageForUsername($allPostPutVars['senderUsername'],$allPostPutVars['receiverUsername'],$allPostPutVars['message']);
        $successArray = array("status"=> "True","message" => "Message sent!"  );
    } catch (\Throwable $th) {
        //If an Exception happens in the function call above such as no user with the given name this section runs.
        $successArray = array("status"=> "False","message" => "ERROR : Message could not be sent!" );
    }

    //Returns the successArray with 201 Created.
    return $this->response->withStatus(201)->withHeader(“Content-Type”,”application/json”)->withJson($successArray);
    
    
});

$app->run();