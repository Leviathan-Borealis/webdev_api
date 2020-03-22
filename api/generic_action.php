<?php
// get database connection
include_once './config/database.php';
header("Access-Control-Allow-Origin: *");
if(isset($_POST["action"])) {

    $database = new Database();
    $conn = $database->getConnection();

    $returnJson = null;

    switch ($_POST["action"]){
        case "registerUser":{
            $returnJson = $database->registerUser($_POST["username"],$_POST["password"],$_POST["mail"],$_POST["rights"],true);
            break;
        }
        case "addAtmosphereByUser":{
            $returnJson = $database->addAtmosphereByUser($_POST["atmosphere_name"],$_POST["user_id"],true);
        break;
        }
        case "getSongsByAtmosphere":{
            $returnJson = $database->getSongsByAtmosphere($_POST["atmosphere_id"],true);
            break;
        }
        case "resetPassword":{
            $returnJson = $database->resetPassword($_POST["user_id"],$_POST["username"],$_POST["mail"],true);
            break;
        }
        case "logOnUser":{
            $returnJson = $database->logOnUser($_POST["username"],$_POST["password"],true);
            break;
        }
        case "deleteSongFromAtmosphereByUser":{
            $returnJson = $database->deleteSongFromAtmosphereByUser($_POST["user_id"],$_POST["atmosphere_id"],$_POST["song_id"],true);
            break;
        }
        case "getAtmosphereByIdAndUser":{
            $returnJson = $database->getAtmosphereByIdAndUser($_POST["atmosphere_id"],$_POST["user_id"],true);
            break;
        }
        case "getAtmosphereByUser":{
            $returnJson = $database->getAtmosphereByUser($_POST["user_id"],true);
            break;
        }
        case "getUser":{
            $returnJson = $database->getUser($_POST["user_id"],true);
            break;
        }
        case "addSongToAtmosphere":{
            $returnJson = $database->addSongToAtmosphere($_POST["atmosphere_id"],$_POST["song_title"],$_POST["song_link"],true);
            break;
        }
        case "deleteSongFromAllAtmosphereByAdmin":{
            $returnJson = $database->deleteSongFromAllAtmosphereByAdmin($_POST["user_id"],$_POST["song_id"],true);
        }
        case "updateSongTitleByUser":{
            $returnJson = $database->updateSongTitleByUser($_POST["song_id"],$_POST["new_song_title"],true);
        }
        case "deleteSongByAdmin":{
            $returnJson = $database->deleteSongByAdmin($_POST["user_id"],$_POST["song_id"],true);
        }
    }
    print_r($returnJson);

    /*
     Completed - User kan Lägga till låtar till en atmosfär (Ska räcka med en länk då vi hittat den andra häftiga)
     Completed - User tar bort en låt från sin atmosfär
     Completed - Admin tar bort låtar som även tar bort alla låtar kopplingar mot respektiv atmosfär
     Not completed - Admin uppdatera titel på respektiv låt
     Completed - Log in
     Completed - Register user
     Completed - Forgot password
     Completed - User lägg till atmosfär
     */
} else {
    print_r(json_encode(array("errorCode" => "Invalid_query","supplied_data" => $_POST)));
}