<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/include/config.php';
require __DIR__ . '/include/Helper.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$defaultLogger = new Logger('defaultLogger');
$defaultLogger->pushHandler(new StreamHandler(__DIR__ . 'logs.log', Logger::WARNING));


\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

function echoResponse($status_code, $data) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('application/json');

    $response = $app->response();
    $response->header('Access-Control-Allow-Methods', 'GET,POST,DELETE,PUT');
    $response->header('Access-Control-Allow-Origin', '*');
    $response->header('Access-Control-Max-Age', '360');

    $response->write(json_encode($data));
}

function authentication($role = 'user') {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $auth = $app->request()->headers()->get('Authorization');

    if($auth != NULL) {
        $q = new UserQuery();
        $result = $q->findOneByToken($auth);
        if($result != NULL) {
            if($result->getRole()->getName() == $role) {
                if($result->getExpire()->format('Y-m-d H:i:s') > date('Y-m-d H:i:s')) {
                    $result->setExpire(date('Y-m-d H:i:s', strtotime('+1 hour')));
                    $result->save();
                    return true;
                }
            }
        }
    }

    return false;
}

/**
 * GET all travels
 */
$app->get('/travels', function() {

    $response = array();

    $result = TravelQuery::create()->find();

    $response["travel"] = array();

    foreach($result as $travel) {
        array_push($response["travel"], Helper::travelToArray($travel));
    }

    echoResponse(200, $response);
});

/**
 * Create a new travel
 */
$app->post('/travels', function() {

    $response = array();

    if(authentication('admin')) {
        $app = \Slim\Slim::getInstance();
        $r = json_decode($app->request->getBody());

        $title = $r->title;
        $description = $r->description;
        $date = $r->date;
        $length = $r->length;

        $travel = new Travel();
        $travel->setTitle($title)->setDate($date)->setDescription($description)->setLength($length)->setUserId(1);

        $result = $travel->save();

        if ($result != NULL) {
            $response["message"] = "Travel created successfully";
            echoResponse(201, $response);
        } else {
            $response["message"] = "Failed to create Travel. Please try again";
            echoResponse(409, $response);
        }
    } else {
        $response["message"] = "Unauthorized. Please log in with admin rights.";
        echoResponse(401, $response);
    }
});

/**
 * GET travel with id
 */
$app->get('/travels/:id', function($travel_id) {
    $response = array();

    $q = new TravelQuery();
    $result = $q->findPK($travel_id);

    if ($result != NULL) {
        $response = Helper::travelToArray($result);
        echoResponse(200, $response);
    } else {
        $response["message"] = "The requested resource doesn't exists";
        echoResponse(404, $response);
    }
});

/**
 * DELETE travel with id
 */
$app->delete('/travels/:id', function($travel_id) {
    $response = array();

    if(authentication('admin')) {
        $q = new TravelQuery();
        $result = $q->findPK($travel_id);

        if ($result != NULL) {
            $result = $result->delete();
            if ($result == NULL) {
                $response["message"] = "Travel successfully deleted";
                echoResponse(200, $response);
            } else {
                $response["message"] = "Failed to delete travel. Please try again";
                echoResponse(409, $response);
            }
        } else {
            $response["message"] = "The requested resource doesn't exists";
            echoResponse(404, $response);
        }
    } else {
        $response["message"] = "Unauthorized. Please log in with admin rights.";
        echoResponse(401, $response);
    }
});

/**
 * DELETE entry with id
 */
$app->delete('/travels/:id/entries/:entryId', function($travel_id, $entry_id) {
    $response = array();

    if(authentication('admin')) {
        $q = new EntryQuery();
        $result = $q->findPK($entry_id);

        if ($result != NULL) {
            $result = $result->delete();
            if ($result == NULL) {
                $response["message"] = "Entry successfully deleted";
                echoResponse(200, $response);
            } else {
                $response["message"] = "Failed to delete entry. Please try again";
                echoResponse(409, $response);
            }
        } else {
            $response["message"] = "The requested resource doesn't exists" . $entry_id;
            echoResponse(404, $response);
        }
    } else {
        $response["message"] = "Unauthorized. Please log in with admin rights.";
        echoResponse(401, $response);
    }
});

/**
 * GET travel with id
 */
$app->get('/travels/:id/entries', function($travel_id) {
    $response = array();

    $q = new EntryQuery();
    $result = $q->orderByDate()->findByTravelid($travel_id);

    if ($result != NULL) {
        $response["entry"] = array();

        foreach($result as $travel) {
            array_push($response["entry"], Helper::entryToArray($travel));
        }
        echoResponse(200, $response);
    } else {
        $response["message"] = "The requested resource doesn't exists";
        echoResponse(404, $response);
    }
});

/**
 * Create a new entry
 */
$app->post('/travels/:id/entries', function($travel_id) {

    $response = array();


    if(authentication('admin')) {
        $app = \Slim\Slim::getInstance();
        $r = json_decode($app->request->getBody());

        $title = $r->title;
        $content = $r->content;
        $image = $r->image;
        $location = $r->location;

        $entry = new Entry();
        $entry->setTitle($title)->setText($content)->setTravelid($travel_id)->setUserid(1)->setDate(new DateTime('now'))->setTime(new DateTime('now'))->setImage($image)->setLocation($location);

        $result = $entry->save();

        if ($result != NULL) {
            $response["message"] = "Entry created successfully";
            echoResponse(201, $response);
        } else {
            $response["message"] = "Failed to create entry. Please try again";
            echoResponse(409, $response);
        }
    } else {
        $response["message"] = "Unauthorized. Please log in with admin rights.";
        echoResponse(401, $response);
    }

});

/**
 * GET travel with id
 */
$app->get('/users/:id', function($user_id) {
    $response = array();

    $q = new UserQuery();
    $result = $q->findPK($user_id);

    if ($result != NULL) {
        $response = Helper::userToArray($result);
        echoResponse(200, $response);
    } else {
        $response["message"] = "The requested resource doesn't exists";
        echoResponse(404, $response);
    }
});

$app->post('/login', function()
{
    $app = \Slim\Slim::getInstance();
    $response = array();
    $r = json_decode($app->request->getBody());

    require_once __DIR__ . '/include/PasswordHash.php';

    $username = $r->username;
    $password = $r->password;

    $q = new UserQuery();
    $user = $q->findOneByUsername($username);

    if($user != NULL) {
        if (PasswordHash::checkPassword($password, $user->getPassword())) {
            $user->setToken(bin2hex(openssl_random_pseudo_bytes(16)))->setExpire(date('Y-m-d H:i:s', strtotime('+1 hour')));
            $result = $user->save();
            if ($result != NULL) {
                $response["message"] = "Successfully logged in";
                $response["token"] = $user->getToken();
                $response["isAdmin"] = $user->getRole()->getName() == 'admin';
                echoResponse(200, $response);
            } else {
                $response["message"] = "Failed to create token. Please try again";
                echoResponse(409, $response);
            }
        } else {
            $response["message"] = "Wrong password.";
            echoResponse(401, $response);
        }
    }else{
        $response["message"] = "Username not found.";
        echoResponse(404, $response);
    }
});


$app->post('/register', function() {
    $app = \Slim\Slim::getInstance();
    $response = array();
    $r = json_decode($app->request->getBody());
    require_once __DIR__ . '/include/PasswordHash.php';
    $firstname = $r->firstname;
    $lastname = $r->lastname;
    $username = $r->username;
    $password = $r->password;
    $q = new UserQuery();
    $result = $q->findOneByUsername($username);
    if($result == NULL){
        $password = PasswordHash::createHash($password);
        $user = new User();
        $user->setFirstname($firstname)->setLastname($lastname)->setUsername($username)->setPassword($password)->setRoleid(2);
        $result = $user->save();
        if ($result != NULL) {
            $response["message"] = "User account created successfully";
            $response["id"] = $result;
            echoResponse(200, $response);
        } else {
            $response["message"] = "Failed to create user. Please try again";
            echoResponse(201, $response);
        }
    }else{
        $response["message"] = "Username already taken!";
        echoResponse(201, $response);
    }
});

$app->run();