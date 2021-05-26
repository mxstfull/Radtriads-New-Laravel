<?php
include("../includes/config.php");
include("../includes/functions.php");
include("../includes/db_connect.php");
include("../includes/upload.class.php");

$result = array();

function delete_album($path)
{
    if (is_dir($path) === true)
    {
        $files = array_diff(scandir($path), array('.', '..'));

        foreach ($files as $file)
        {
            delete_album(realpath($path) . '/' . $file);
        }
        
        return rmdir($path);
    }

    else if (is_file($path) === true)
    {
        return unlink($path);
    }

    return false;
}

if(!$_SESSION) {
	exit;
}

$user_id = $_SESSION["USER_ID"];
$result = array();

if(isset($_POST["album_id"])) {

	$album_id = json_decode($_POST["album_id"]);

	// Get infos about the current user...
	$user_infos = $dbh->prepare("SELECT u.id, u.unique_id, u.stripe_plan, p.diskspace AS max_diskspace, p.bandwidth AS max_bandwidth FROM user u, plan p WHERE u.id = :user_id AND u.plan_id = p.id");
	$user_infos->bindParam(":user_id", $user_id);
	$user_infos->execute();
	
	if($user_infos->rowCount() == 0) {
		
		$result["error"] = "There is an issue with your profile. Please try again later.";
		
	} else if($album_id == 0) {
		
		$result["error"] = "Please select a valid album...";
		
	} else {
		
		// Check if the owner of this album is the current user		
		$get_album_query = $dbh->prepare("SELECT id, title, path FROM album WHERE user_id = :user_id AND id = :album_id");
		$get_album_query->bindParam(":user_id", $user_id);
		$get_album_query->bindParam(":album_id", $album_id);
		$get_album_query->execute();
		
		if($get_album_query->rowCount() > 0) {
			
			$album = $get_album_query->fetch();
			$album_path = $album["path"];
			
			// Delete the album and the files inside it
			delete_album("../" . $album_path);
			
			$album_path_like = "%$album_path%";
			
			// Delete from the database
			$delete_albums = $dbh->prepare("DELETE FROM album WHERE path LIKE :album_path AND user_id = :user_id");
			$delete_albums->bindParam(":album_path", $album_path_like);
			$delete_albums->bindParam(":user_id", $user_id);
			$delete_albums->execute();
			
			// Delete from the database
			$delete_albums = $dbh->prepare("DELETE FROM file WHERE folder_path LIKE :album_path AND user_id = :user_id");
			$delete_albums->bindParam(":album_path", $album_path_like);
			$delete_albums->bindParam(":user_id", $user_id);
			$delete_albums->execute();
			
			$result["success"] = 1;
			
		} else {
			
			$result["error"] = "You are not the owner of this album...";
			
		}
		
	}
	
}

echo json_encode($result);