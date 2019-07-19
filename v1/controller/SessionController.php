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

    if (array_key_exists("sessionid", $_GET)) {

    } else if (empty($_GET)) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $response = new Response();
            $response->setHttpStatusCode(405);
            $response->setSuccess(false);
            $response->addMessage("Request method not allowed");
            $response->send();
            exit;
        }

        // sleep(1);

        if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
            $response = new Response();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage("Content type header not set to JSON");
            $response->send();
            exit;
        }

        $rowPostData = file_get_contents('php://input');
        if (!$jsonData = json_decode($rowPostData)) {
            $response = new Response();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage("Request body is not valid JSON");
            $response->send();
            exit;
        }

        if (!isset($jsonData->username) || !isset($jsonData->password)) {
            $response = new Response();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            (!isset($jsonData->username) ? $response->addMessage("User name not supplied") : false);
            (!isset($jsonData->password) ? $response->addMessage("Password not supplied") : false);
            $response->send();
            exit;
        }

        if (strlen($jsonData->username) < 1 || strlen($jsonData->username) > 255 || strlen($jsonData->password) < 1 || strlen($jsonData->password) > 255) {
            $response = new Response();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            (strlen($jsonData->username) < 1 ? $response->addMessage("User name cannot be blank") : false);
            (strlen($jsonData->username) > 255 ? $response->addMessage("User name cannot be greater than 255 characters") : false);
            (strlen($jsonData->password) < 1 ? $response->addMessage("Password cannot be blank") : false);
            (strlen($jsonData->password) > 255 ? $response->addMessage("Password cannot be greater than 255 characters") : false);
            $response->send();
            exit;
        }

        try {
            $username = $jsonData->username;
            $password = $jsonData->password;

            $query = $writeDb->prepare('SELECT id, fullname, username, password, useractive, loginattempts FROM tbl_users WHERE username = :username');
            $query->bindParam(':username', $username, PDO::PARAM_STR);
            $query->execute();

            $rowCount = $query->rowCount();
            if ($rowCount === 0) {
                $response = new Response();
                $response->setHttpStatusCode(401);
                $response->setSuccess(false);
                $response->addMessage("Username or password is incorrect");
                $response->send();
                exit;
            }

            $row = $query->fetch(PDO::FETCH_ASSOC);
            $returned_id = $row['id'];
            $returned_fullname = $row['fullname'];
            $returned_username = $row['username'];
            $returned_password = $row['password'];
            $returned_useractive = $row['useractive'];
            $returned_loginattempts = $row['loginattempts'];

            if ($returned_useractive !== 'Y') {
                $response = new Response();
                $response->setHttpStatusCode(401);
                $response->setSuccess(false);
                $response->addMessage("User account not active");
                $response->send();
                exit;
            }

            if ($returned_loginattempts >= 3) {
                $response = new Response();
                $response->setHttpStatusCode(401);
                $response->setSuccess(false);
                $response->addMessage("User account is currently locked out");
                $response->send();
                exit;
            }

            if (!password_verify($password, $returned_password)) {
                $query = $writeDb->prepare('UPDATE tbl_users SET loginattempts = loginattempts+1 WHERE id = :id');
                $query->bindParam(':id', $returned_id, PDO::PARAM_INT);
                $query->execute();

                $response = new Response();
                $response->setHttpStatusCode(401);
                $response->setSuccess(false);
                $response->addMessage("Username or password is incorrect");
                $response->send();
                exit;
            }

            $accesstoken = base64_encode(bin2hex(openssl_random_pseudo_bytes(24)).time());
            $refreshtoken = base64_encode(bin2hex(openssl_random_pseudo_bytes(24)).time());

            $accesstokenexpiryseconds = 1200;
            $refreshtokenexpiryseconds = 1209600;

        } catch (PDOException $ex) {
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage("There was an issue logging in");
            $response->send();
            exit;
        }

        try {
            $writeDb->beginTransaction();

            $query= $writeDb->prepare('UPDATE tbl_users SET loginattempts = 0 WHERE id = :id');
            $query->bindParam(':id', $returned_id, PDO::PARAM_INT);
            $query->execute();

            $query = $writeDb->prepare('INSERT INTO tbl_sessions (userid, accesstoken, accesstokenexpiry, refreshtoken, refreshtokenexpiry) VALUES (:userid, :accesstoken, date_add(NOW(), INTERVAL :accesstokenexpiryseconds SECOND), :refreshtoken, date_add(NOW(), INTERVAL :refreshtokenexpiryseconds SECOND))');
            $query->bindParam(':userid', $returned_id, PDO::PARAM_INT);
            $query->bindParam(':accesstoken', $accesstoken, PDO::PARAM_STR);
            $query->bindParam(':accesstokenexpiryseconds', $accesstokenexpiryseconds, PDO::PARAM_INT);
            $query->bindParam(':refreshtoken', $refreshtoken, PDO::PARAM_STR);
            $query->bindParam(':refreshtokenexpiryseconds', $refreshtokenexpiryseconds, PDO::PARAM_INT);
            $query->execute();

            $lastSessionId = $writeDb->lastInsertId();
            $writeDb->commit();

            $returnData = array();
            $returnData['session_id'] = intval($lastSessionId);
            $returnData['access_token'] = $accesstoken;
            $returnData['access_token_expires_in'] = $accesstokenexpiryseconds;
            $returnData['refresh_token'] = $refreshtoken;
            $returnData['refresh_token_expires_in'] = $refreshtokenexpiryseconds;

            $response = new Response();
            $response->setHttpStatusCode(201);
            $response->setSuccess(true);
            $response->setData($returnData);
            $response->send();
            exit;
        } catch (PDOException $ex) {
            $writeDb->rollback();
            $response = new Response();
            $response->setHttpStatusCode(500);
            $response->setSuccess(false);
            $response->addMessage("There was an issue logging in - please try again");
            $response->send();
            exit;
        }
    } else {
        $response = new Response();
        $response->setHttpStatusCode(404);
        $response->setSuccess(false);
        $response->addMessage("Endpoint not found");
        $response->send();
        exit;
    }
?>