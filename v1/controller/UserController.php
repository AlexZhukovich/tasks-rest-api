<?php
    require_once('../connector/DatabaseConnector.php');
    require_once('../model/Response.php');

    try {
        $writeDb = DatabaseConnector::connectWriteDatabase();

    } catch (PDOException $ex) {
        error_log("Connection error: ".$ex, 0);
        $response = new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage("Database connection error");
        $response->send();
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $response = new Response();
        $response->setHttpStatusCode(405);
        $response->setSuccess(false);
        $response->addMessage("Request method not allowed");
        $response->send();
        exit;
    }

    if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
        $response = new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        $response->addMessage("Content type header not set to JSON");
        $response->send();
        exit;
    }

    $rawPostData = file_get_contents('php://input');
    if (!$jsonDate = json_decode($rawPostData)) {
        $response = new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        $response->addMessage("Request body is not valid JSON");
        $response->send();
        exit;
    }

    if (!isset($jsonDate->fullname) || !isset($jsonDate->username) || !isset($jsonDate->password)) {
        $response = new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        (!isset($jsonDate->fullname) ? $response->addMessage("Full name not supplied") : false);
        (!isset($jsonDate->username) ? $response->addMessage("User name not supplied") : false);
        (!isset($jsonDate->password) ? $response->addMessage("Password name not supplied") : false);
        $response->send();
        exit;
    }

    if (strlen($jsonDate->fullname) < 1 || strlen($jsonDate->fullname) > 255 || strlen($jsonDate->username) < 1 || strlen($jsonDate->username) > 255 || strlen($jsonDate->password) < 1 || strlen($jsonDate->password) > 255) {
        $response = new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        (strlen($jsonDate->fullname) < 1 ? $response->addMessage("Full name cannot be blank") : false);
        (strlen($jsonDate->fullname) > 255 ? $response->addMessage("Full name cannot be greater than 255 characters") : false);
        (strlen($jsonDate->username) < 1 ? $response->addMessage("User name cannot be blank") : false);
        (strlen($jsonDate->username) > 255 ? $response->addMessage("User name cannot be greater than 255 characters") : false);
        (strlen($jsonDate->password) < 1 ? $response->addMessage("Password cannot be blank") : false);
        (strlen($jsonDate->password) > 255 ? $response->addMessage("Password cannot be greater than 255 characters") : false);
        $response->send();
        exit;
    }

    // Remove whitespace from full name and user name
    $fullname = trim($jsonDate->fullname);
    $username = trim($jsonDate->username);
    $password = $jsonDate->password;

    try {
        
        // check if user name exists
        $query = $writeDb->prepare('SELECT id FROM tbl_users WHERE username = :username');
        $query->bindParam(':username', $username, PDO::PARAM_STR);
        $query->execute();

        $rowCount = $query->rowCount();
        if ($rowCount !== 0) {
            $response = new Response();
            $response->setHttpStatusCode(409);
            $response->setSuccess(false);
            $response->addMessage("User name already exist");
            $response->send();
            exit;
        }

        // create a new user account
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query = $writeDb->prepare('INSERT INTO tbl_users (fullname, username, password) VALUES (:fullname, :username, :password)');
        $query->bindParam(':fullname', $fullname, PDO::PARAM_STR);
        $query->bindParam(':username', $username, PDO::PARAM_STR);
        $query->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        $query->execute();

        $rowCount = $query->rowCount();
        if ($rowCount === 0) {
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage("[INSERT] >>> There was an issue creating a user account - please try again");
            $response->send();
            exit;
        }

        $lastUserId = $writeDb->lastInsertId();
        $returnData = array();
        $returnData['user_id'] = $lastUserId;
        $returnData['fullname'] = $fullname;
        $returnData['username'] = $username;

        $response = new Response();
        $response->setHttpStatusCode(201);
        $response->setSuccess(true);
        $response->addMessage("User created");
        $response->setData($returnData);
        $response->send();
        exit;
    } catch (PDOException $ex) {
        error_log("Database query error: ".$ex, 0);
        $response = new Response();
        $response->setHttpStatusCode(500);
        $response->setSuccess(false);
        $response->addMessage("There was an issue creating a user account - please try again");
        $response->send();
        exit;
    }
?>