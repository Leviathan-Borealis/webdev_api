<?php

class Database{
    private $host;
    private $port;
    private $db_name;
    private $username;
    private $password;
    public $conn;
    public $credentials;

    public function __construct(){
        $this->credentials = json_decode(file_get_contents("C:\\xampp\\credentials.txt"), true);
        $this->host = $this->credentials["host"];
        $this->port = $this->credentials["port"];
        $this->db_name = $this->credentials["db_name"];
        $this->username = $this->credentials["username"];
        $this->password = strval($this->credentials["password"]);
    }
 
    // get the database connection
    public function getConnection(){
 
        $this->conn = null;
        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8mb4_swedish_ci");
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
 
        return $this->conn;
    }

    public function getUser($user_id,$asString){
        $returnToCallerObj = [];

        $query = "SELECT * FROM users WHERE users.id=:user_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_id", $user_id);

        if($stmt->execute()){
            $result = $stmt->fetchAll();
            array_push($returnToCallerObj,array(
                    "method" => "getUser",
                    "success" => 1,
                    "result" => array(
                        "db_result" => $result
                    ))
            );

            if($asString) {
                return json_encode($returnToCallerObj);
            }
            return $returnToCallerObj;
        } else {
            array_push($returnToCallerObj,
                array(
                    "method" => "getUser",
                    "success" => 0,
                    "result" => array(
                        "db_result" => array(
                            "errorCode" => $stmt->errorCode(),
                            "errorInfo" => $stmt->errorInfo()
                        )
                    )
                )
            );

            if($asString) {
                return json_encode($returnToCallerObj);
            }
            return $returnToCallerObj;
        }
    }

    public function getAtmosphereByUser($user_id,$asString){
        $returnToCallerObj = [];
        // prepare query
        $query = "SELECT * FROM dnd_atmosphere WHERE dnd_atmosphere.user_id=:user_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_id", $user_id);


        if($stmt->execute()){
            $result = $stmt->fetchAll();
            array_push($returnToCallerObj,array(
                    "method" => "getAtmosphereByUser",
                    "success" => 1,
                    "result" => array(
                        "db_result" => $result
                    ))
            );

            if($asString) {
                return json_encode($returnToCallerObj);
            }
            return $returnToCallerObj;
        } else {
            array_push($returnToCallerObj,
                array(
                    "method" => "getAtmosphereByUser",
                    "success" => 0,
                    "result" => array(
                        "db_result" => array(
                            "errorCode" => $stmt->errorCode(),
                            "errorInfo" => $stmt->errorInfo()
                        )
                    )
                )
            );

            if($asString) {
                return json_encode($returnToCallerObj);
            }
            return $returnToCallerObj;
        }
    }

    public function getAtmosphereByIdAndUser($atmosphere_id, $user_id,$asString){
        $returnToCallerObj = [];
        // prepare query
        $query = "SELECT * FROM dnd_atmosphere WHERE dnd_atmosphere.id=:atmosphere_id AND dnd_atmosphere.user_id=:user_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":atmosphere_id", $atmosphere_id);
        $stmt->bindParam(":user_id", $user_id);


        if($stmt->execute()){
            $result = $stmt->fetchAll();
            array_push($returnToCallerObj,array(
                    "method" => "getAtmosphereByIdAndUser",
                    "success" => 1,
                    "result" => array(
                        "db_result" => $result
                    ))
            );

            if($asString) {
                return json_encode($returnToCallerObj);
            }
            return $returnToCallerObj;
        } else {
            array_push($returnToCallerObj,
                array(
                    "method" => "getAtmosphereByIdAndUser",
                    "success" => 0,
                    "result" => array(
                        "db_result" => array(
                            "errorCode" => $stmt->errorCode(),
                            "errorInfo" => $stmt->errorInfo()
                        )
                    )
                )
            );

            if($asString) {
                return json_encode($returnToCallerObj);
            }
            return $returnToCallerObj;
        }
    }

    public function getAtmosphereById($atmosphere_id,$asString){
        $returnToCallerObj = [];
        // prepare query
        $query = "SELECT * FROM dnd_atmosphere WHERE dnd_atmosphere.id=:atmosphere_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":atmosphere_id", $atmosphere_id);

        if ($stmt->execute()) {
            $result = $stmt->fetchAll();
            array_push($returnToCallerObj,array(
                    "method" => "getAtmosphereById",
                    "success" => 1,
                    "result" => array(
                        "db_result" => $result
                    ))
            );

            if($asString) {
                return json_encode($returnToCallerObj);
            }
            return $returnToCallerObj;
        } else {
            array_push($returnToCallerObj,
                array(
                    "method" => "getAtmosphereById",
                    "success" => 0,
                    "result" => array(
                        "db_result" => array(
                            "errorCode" => $stmt->errorCode(),
                            "errorInfo" => $stmt->errorInfo()
                        )
                    )
                )
            );

            if($asString) {
                return json_encode($returnToCallerObj);
            }
            return $returnToCallerObj;
        }
    }

    public function addSongToAtmosphere($atmosphere_id,$song_title,$song_link,$asString){
        $returnToCallerObj = [];

        $resultObj = $this->insertSong($song_title, $song_link, false)[0];

        if ($resultObj["success"] == 0 && ($resultObj["result"]["result"]["isPresent"] == 0 || $resultObj["result"]["result"]["isPresent"] == -1)) {
            array_push($returnToCallerObj, array("method" => "addSongToAtmosphere", "success" => 0, "result" => $resultObj));
            if ($asString) {
                return json_encode($returnToCallerObj);
            }
            return $returnToCallerObj;
        }

        if($resultObj["success"] == 1) {
            $result = $resultObj["song_id"];
        } else {
            $result = $resultObj["result"]["result"]["db_result"][0]["id"];
        }

        if (count($this->getAtmosphereById($atmosphere_id,false)[0]["result"]["db_result"]) > 0) {

            $query = "INSERT INTO dnd_atmosphere_has_songs(dnd_atmosphere_id, songs_id) 
                    VALUES (:atmosphere_id,:song_id)";

            // prepare query
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":atmosphere_id", $atmosphere_id);
            $stmt->bindParam(":song_id", $result);

            if ($stmt->execute()) {
                $result = $stmt->fetchAll();
                array_push($returnToCallerObj,array(
                        "method" => "addSongToAtmosphere",
                        "success" => 1,
                        "result" => array(
                            "db_result" => $result
                        ))
                );

                if($asString) {
                    return json_encode($returnToCallerObj);
                }
                return $returnToCallerObj;
            } else {
                array_push($returnToCallerObj,
                    array(
                        "method" => "addSongToAtmosphere",
                        "success" => 0,
                        "result" => array(
                            "db_result" => array(
                                "errorCode" => $stmt->errorCode(),
                                "errorInfo" => $stmt->errorInfo()
                            )
                        )
                    )
                );

                if($asString) {
                    return json_encode($returnToCallerObj);
                }
                return $returnToCallerObj;
            }
        }
        array_push($returnToCallerObj,array("method" => "addSongToAtmosphere","success" => 0, "result" => $resultObj));
        if($asString) {
            return json_encode($returnToCallerObj);
        }
        return $returnToCallerObj;
    }

    public function deleteSongFromAtmosphereByUser($user_id, $atmosphere_id, $song_id,$asString){
        $returnToCallerObj = [];

        $user = $this->getUser($user_id,false)[0];

        array_push($returnToCallerObj,array("debug" => $user["result"]["db_result"][0]["rights"]));

        if($user["result"]["db_result"][0]["rights"] == "ADM"){
            $jsonObject = $this->getAtmosphereById($atmosphere_id,false)[0];
        } else {
            $jsonObject = $this->getAtmosphereByIdAndUser($atmosphere_id, $user_id, false)[0];
        }

        if ($jsonObject["success"] == 0) {
            array_push($returnToCallerObj,array("method" => "deleteSongFromAtmosphereByUser","success" => 0, "result" => $jsonObject));
            if($asString) {
                return json_encode($returnToCallerObj);
            }
            return $returnToCallerObj;
        }

        $result = $jsonObject["result"]["db_result"];

        if(count($result) > 0) {

            $query = "DELETE FROM dnd_atmosphere_has_songs WHERE dnd_atmosphere_id=:atmosphere_id AND songs_id=:song_id";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":atmosphere_id", $atmosphere_id);
            $stmt->bindParam(":song_id", $song_id);

            if ($stmt->execute()) {
                $result = $stmt->fetchAll();
                array_push($returnToCallerObj,array(
                        "method" => "deleteSongFromAtmosphereByUser",
                        "success" => 1,
                        "result" => array(
                            "db_result" => $result
                        ))
                );

                if($asString) {
                    return json_encode($returnToCallerObj);
                }
                return $returnToCallerObj;
            } else {
                array_push($returnToCallerObj,
                    array(
                        "method" => "deleteSongFromAtmosphereByUser",
                        "success" => 0,
                        "result" => array(
                            "db_result" => array(
                                "errorCode" => $stmt->errorCode(),
                                "errorInfo" => $stmt->errorInfo()
                            )
                        )
                    )
                );

                if($asString) {
                    return json_encode($returnToCallerObj);
                }
                return $returnToCallerObj;
            }
        } else {
            array_push($returnToCallerObj,array("method" => "deleteSongFromAtmosphereByUser","success" => 0, "result" => $jsonObject));
            if($asString) {
                return json_encode($returnToCallerObj);
            }
            return $returnToCallerObj;
        }
    }

    public function deleteSongFromAllAtmosphereByAdmin($user_id, $song_id,$asString){
        $returnToCallerObj = [];

        $user = $this->getUser($user_id,false)[0];

        if($user["result"]["db_result"][0]["rights"] == "ADM"){
            $query = "DELETE FROM dnd_atmosphere_has_songs WHERE songs_id=:song_id";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":song_id", $song_id);

            if ($stmt->execute()) {
                $result = $stmt->fetchAll();
                array_push($returnToCallerObj,array(
                        "method" => "deleteSongFromAllAtmosphereByAdmin",
                        "success" => 1,
                        "result" => array(
                            "db_result" => $result
                        ))
                );

                if($asString) {
                    return json_encode($returnToCallerObj);
                }
                return $returnToCallerObj;
            } else {
                array_push($returnToCallerObj,
                    array(
                        "method" => "deleteSongFromAllAtmosphereByAdmin",
                        "success" => 0,
                        "result" => array(
                            "db_result" => array(
                                "errorCode" => $stmt->errorCode(),
                                "errorInfo" => $stmt->errorInfo()
                            )
                        )
                    )
                );

                if($asString) {
                    return json_encode($returnToCallerObj);
                }
                return $returnToCallerObj;
            }
        } else {
            array_push($returnToCallerObj,
                array(
                    "method" => "deleteSongFromAllAtmosphereByAdmin",
                    "success" => 0,
                    "result" => array(
                        "db_result" => array(
                            $user
                        )
                    )
                )
            );
        }
        if($asString) {
            return json_encode($returnToCallerObj);
        }
        return $returnToCallerObj;
    }

    public function resetPassword($user_id,$username,$mail,$asString){

        $returnToCallerObj = [];

        if((isset($user_id) || isset($username)) && isset($mail)){
            $query = "SELECT * FROM users WHERE (users.id=:user_id OR users.username=:username) AND users.mail=:mail";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->bindParam(":username", $username);
            $stmt->bindParam(":mail", $mail);
            $stmt->execute();

            if($stmt->rowCount() == 1){
                $results = $stmt->fetchAll();
                $user_id = $results[0]["id"];
                $new_password = substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(32))), 0, 32); // 32 characters, without /=+
                $query = "UPDATE users SET password=:new_password WHERE id=:user_id ";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(":user_id", $user_id);
                $stmt->bindParam(":new_password", $new_password);
                if($stmt->execute()){
                    $result = $stmt->fetchAll();
                    array_push($returnToCallerObj,array(
                            "method" => "resetPassword",
                            "success" => 1,
                            "result" => array(
                                "db_result" => $result
                            ))
                    );

                    if($asString) {
                        return json_encode($returnToCallerObj);
                    }
                    return $returnToCallerObj;
                } else {
                    array_push($returnToCallerObj,
                        array(
                            "method" => "resetPassword",
                            "success" => 0,
                            "result" => array(
                                "db_result" => array(
                                    "errorCode" => $stmt->errorCode(),
                                    "errorInfo" => $stmt->errorInfo()
                                )
                            )
                        )
                    );

                    if($asString) {
                        return json_encode($returnToCallerObj);
                    }
                    return $returnToCallerObj;
                }
            } else {
                array_push($returnToCallerObj,
                    array(
                        "method" => "resetPassword",
                        "success" => 0,
                        "result" => array(
                            "db_result" => array(
                                "status" => false,
                                "message" => "User does not exist in database",
                                "errorCode" => $stmt->errorCode(),
                                "errorInfo" => $stmt->errorInfo()
                            )
                        )
                    )
                );

                if($asString) {
                    return json_encode($returnToCallerObj);
                }
                return $returnToCallerObj;
            }

        } else {
            array_push($returnToCallerObj,
                array(
                    "method" => "resetPassword",
                    "success" => 0,
                    "result" => array(
                        "db_result" => array(
                            "status" => false,
                            "message" => "User does not exist in database",
                            "errorCode" => "Missing arguments",
                            "errorInfo" => "user_id OR username must be supplied"
                        )
                    )
                )
            );

            if($asString) {
                return json_encode($returnToCallerObj);
            }
            return $returnToCallerObj;
        }
    }

    public function logOnUser($username,$password,$asString){
        include_once "./objects/user.php";

        $returnToCallerObj = [];

        // prepare user object
        $user = new User($this->conn);
        // set ID property of user to be edited
        if(isset($username) && isset($password)) {
            $user->username = $username;
            $user->password = base64_encode($password);
            // read the details of user to be edited
            $stmt = $user->login();
            if ($stmt->rowCount() > 0) {
                // get retrieved row
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                // create array
                $user_arr = array(
                    "status" => true,
                    "message" => "Successfully Login!",
                    "id" => $row['id'],
                    "username" => $row['username'],
                    "mail" => $row['mail'],
                    "rights" => $row['rights']
                );
                array_push($returnToCallerObj,array(
                        "method" => "logOnUser",
                        "success" => 1,
                        "result" => array(
                            "db_result" => $user_arr
                        ))
                );

                if($asString) {
                    return json_encode($returnToCallerObj);
                }
                return $returnToCallerObj;
            } else {
                $user_arr = array(
                    "status" => false,
                    "message" => "Invalid Username or Password!",
                    "password" => $user->password,
                    "errorCode" => $stmt->errorCode(),
                    "errorInfo" => $stmt->errorInfo()
                );
                array_push($returnToCallerObj,
                    array(
                        "method" => "logOnUser",
                        "success" => 0,
                        "result" => array(
                            "db_result" => $user_arr
                        )
                    )
                );

                if($asString) {
                    return json_encode($returnToCallerObj);
                }
                return $returnToCallerObj;
            }
        } else {
            array_push($returnToCallerObj,
                array(
                    "method" => "logOnUser",
                    "success" => 0,
                    "result" => array(
                        "db_result" => array("errorCode" => "Missing arguments","errorInfo" => "username AND password must be supplied")
                    )
                )
            );
            if($asString) {
                return json_encode($returnToCallerObj);
            }
            return $returnToCallerObj;
        }
    }

    public function getSongsByAtmosphere($atmosphere_id,$asString){
        $returnToCallerObj = [];

        $query = "SELECT songs.id, songs.title, songs.href_link FROM songs inner join dnd_atmosphere_has_songs dahs on songs.id = dahs.songs_id where dnd_atmosphere_id=:atmosphere_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":atmosphere_id", $atmosphere_id);

        if($stmt->execute()){
            $result = $stmt->fetchAll();
            array_push($returnToCallerObj,array(
                    "method" => "getSongsByAtmosphere",
                    "success" => 1,
                    "result" => array(
                        "db_result" => $result
                    ))
            );

            if($asString) {
                return json_encode($returnToCallerObj);
            }
            return $returnToCallerObj;
        } else {
            array_push($returnToCallerObj,
                array(
                    "method" => "getSongsByAtmosphere",
                    "success" => 0,
                    "result" => array(
                        "db_result" => array(
                            "errorCode" => $stmt->errorCode(),
                            "errorInfo" => $stmt->errorInfo()
                        )
                    )
                )
            );

            if($asString) {
                return json_encode($returnToCallerObj);
            }
            return $returnToCallerObj;
        }
    }

    public function insertSong($song_title,$song_link,$asString){
        $returnToCallerObj = [];
        $songPresentResultObj = $this->isSongPresent($song_link,false)[0];

        if($songPresentResultObj["success"] == 1 && $songPresentResultObj["result"]["isPresent"] == 0){
            $query = "INSERT INTO songs(title,href_link) 
                            VALUES (:title,:href_link)";

            // prepare query
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":title", utf8_encode(preg_replace('/[^A-Za-z0-9\- ]/', '', $song_title)));
            $stmt->bindParam(":href_link", $song_link);

            if($stmt->execute()){
                $song_id = $this->conn->lastInsertId();
                $result = $stmt->fetchAll();
                array_push($returnToCallerObj,array(
                    "method" => "insertSong",
                    "success" => 1,
                    "song_id" => $song_id,
                    "result" => array(
                        "db_result" => $result
                    ))
                );

                if($asString) {
                    return json_encode($returnToCallerObj);
                }
                return $returnToCallerObj;
            } else {
                array_push($returnToCallerObj,
                    array(
                        "method" => "insertSong",
                        "success" => 0,
                        "result" => array(
                            "db_result" => array(
                                "errorCode" => $stmt->errorCode(),
                                "errorInfo" => $stmt->errorInfo()
                            )
                        )
                    )
                );

                if($asString) {
                    return json_encode($returnToCallerObj);
                }
                return $returnToCallerObj;
            }
        } else {
            array_push($returnToCallerObj,array("method" => "insertSong","success" => 0, "result" => $songPresentResultObj));
            if($asString){
                return json_encode($returnToCallerObj);
            }
            return $returnToCallerObj;
        }
    }

    public function isSongPresent($song_link,$asString){
        $returnToCallerObj = [];
        $query = "SELECT songs.id FROM songs WHERE href_link=:href_link";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":href_link", $song_link);

        if($stmt->execute()){
            $result = $stmt->fetchAll();
            if(count($result) > 0){
                array_push($returnToCallerObj,array("method" => "isSongPresent","success" => 1,"result" => array("isPresent" => 1, "db_result" => $result)));
                if($asString) {
                    return json_encode($returnToCallerObj);
                }
                return $returnToCallerObj;
            }
            array_push($returnToCallerObj,array("method" => "isSongPresent","success" => 1,"result" => array("isPresent" => 0)));
            if($asString) {
                return json_encode($returnToCallerObj);
            }
            return $returnToCallerObj;
        }
        array_push($returnToCallerObj,array(
            "method" => "isSongPresent",
            "success" => 0,
            "result" => array(
                "isPresent" => -1,
                "db_result" => array("errorCode" => $stmt->errorCode(), "errorInfo" => $stmt->errorInfo())
                )
            ));

        if($asString) {
            return json_encode($returnToCallerObj);
        }
        return $returnToCallerObj;
    }

    public function addAtmosphereByUser($atmosphere_name,$user_id,$asString){
        $returnToCallerObj = [];
        $query = "INSERT INTO dnd_atmosphere(atmosphere_name,user_id) VALUES (:atmosphere_name,(select users.id from users where id=:user_id))";

        // prepare query
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":atmosphere_name", $atmosphere_name);
        $stmt->bindParam(":user_id", $user_id);

        if($stmt->execute()){
            $result = $stmt->fetchAll();
            array_push($returnToCallerObj,array(
                    "method" => "addAtmosphereByUser",
                    "success" => 1,
                    "result" => array(
                        "db_result" => $result
                    ))
            );

            if($asString) {
                return json_encode($returnToCallerObj);
            }
            return $returnToCallerObj;
        } else {
            array_push($returnToCallerObj,
                array(
                    "method" => "addAtmosphereByUser",
                    "success" => 0,
                    "result" => array(
                        "db_result" => array(
                            "errorCode" => $stmt->errorCode(),
                            "errorInfo" => $stmt->errorInfo()
                        )
                    )
                )
            );

            if($asString) {
                return json_encode($returnToCallerObj);
            }
            return $returnToCallerObj;
        }
    }

    public function registerUser($username,$password,$mail,$rights,$asString){
        include_once "./objects/user.php";

        $returnToCallerObj = [];

        // prepare user object
        $user = new User($this->conn);
        // set ID property of user to be edited
        if(isset($username) && isset($password)) {
            $user->username = $username;
            $user->password = base64_encode($password);
            $user->mail = $mail ?? "";
            $user->rights = $rights ?? "USER";
            // read the details of user to be edited
            if($user->signup()) {
                $stmt = $user->login();
                if ($stmt->rowCount() > 0) {
                    // get retrieved row
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    // create array
                    $user_arr = array(
                        "status" => true,
                        "message" => "Successfully Login!",
                        "id" => $row['id'],
                        "username" => $row['username'],
                        "mail" => $row['mail'],
                        "rights" => $row['rights']
                    );
                    array_push($returnToCallerObj, array(
                            "method" => "registerUser",
                            "success" => 1,
                            "result" => array(
                                "db_result" => $user_arr
                            ))
                    );

                    if ($asString) {
                        return json_encode($returnToCallerObj);
                    }
                    return $returnToCallerObj;
                } else {
                    $user_arr = array(
                        "status" => false,
                        "message" => "Invalid Username or Password!",
                        "password" => $user->password,
                        "errorCode" => $stmt->errorCode(),
                        "errorInfo" => $stmt->errorInfo()
                    );
                    array_push($returnToCallerObj,
                        array(
                            "method" => "registerUser",
                            "success" => 0,
                            "result" => array(
                                "db_result" => $user_arr
                            )
                        )
                    );

                    if ($asString) {
                        return json_encode($returnToCallerObj);
                    }
                    return $returnToCallerObj;
                }
            }
        } else {
            array_push($returnToCallerObj,
                array(
                    "method" => "registerUser",
                    "success" => 0,
                    "result" => array(
                        "db_result" => array("errorCode" => "Missing arguments","errorInfo" => "username AND password must be supplied")
                    )
                )
            );
            if($asString) {
                return json_encode($returnToCallerObj);
            }
            return $returnToCallerObj;
        }
    }

    public function updateSongTitleByUser($song_id,$new_song_title,$asString){
        $returnToCallerObj = [];

        $query = "UPDATE songs set songs.title = :new_song_title where songs.id = :song_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":song_id", $song_id);
        $stmt->bindParam(":new_song_title", $new_song_title);

        if($stmt->execute()){
            $result = $stmt->fetchAll();
            array_push($returnToCallerObj,array(
                    "method" => "updateSongTitleByUser",
                    "success" => 1,
                    "result" => array(
                        "db_result" => $result
                    ))
            );

            if($asString) {
                return json_encode($returnToCallerObj);
            }
            return $returnToCallerObj;
        } else {
            array_push($returnToCallerObj,
                array(
                    "method" => "updateSongTitleByUser",
                    "success" => 0,
                    "result" => array(
                        "db_result" => array(
                            "errorCode" => $stmt->errorCode(),
                            "errorInfo" => $stmt->errorInfo()
                        )
                    )
                )
            );

            if($asString) {
                return json_encode($returnToCallerObj);
            }
            return $returnToCallerObj;
        }
    }

    public function deleteSongByAdmin($user_id, $song_id,$asString){
        $returnToCallerObj = [];

        $user = $this->getUser($user_id,false)[0];

        if($user["result"]["db_result"][0]["rights"] == "ADM"){
            $query = "DELETE FROM songs WHERE songs.id=:song_id";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":song_id", $song_id);

            if ($stmt->execute()) {
                $result = $stmt->fetchAll();
                array_push($returnToCallerObj,array(
                        "method" => "deleteSongByAdmin",
                        "success" => 1,
                        "result" => array(
                            "db_result" => $result
                        ))
                );

                if($asString) {
                    return json_encode($returnToCallerObj);
                }
                return $returnToCallerObj;
            } else {
                array_push($returnToCallerObj,
                    array(
                        "method" => "deleteSongByAdmin",
                        "success" => 0,
                        "result" => array(
                            "db_result" => array(
                                "errorCode" => $stmt->errorCode(),
                                "errorInfo" => $stmt->errorInfo()
                            )
                        )
                    )
                );

                if($asString) {
                    return json_encode($returnToCallerObj);
                }
                return $returnToCallerObj;
            }
        } else {
            array_push($returnToCallerObj,
                array(
                    "method" => "deleteSongByAdmin",
                    "success" => 0,
                    "result" => array(
                        "db_result" => array(
                            $user
                        )
                    )
                )
            );
        }
        if($asString) {
            return json_encode($returnToCallerObj);
        }
        return $returnToCallerObj;
    }
}
?>