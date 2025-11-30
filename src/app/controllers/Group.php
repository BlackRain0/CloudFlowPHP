<?php
namespace app\controllers;
use app\utils\Connect;


class Group{

    public static function createGroup($data){
        $conn = Connect::connect();
        $title = $data['groupTitle'];
        $groupCode = hash( 'crc32' ,$title.time());
        $queryAdd = "INSERT INTO `groups`(`id`, `title`, `group_code`) VALUES (null,?,?)";
        $stmt = mysqli_prepare($conn,$queryAdd);
        mysqli_stmt_bind_param($stmt, "ss",$title, $groupCode );
       
        if( mysqli_stmt_execute($stmt)){
            $groupId = mysqli_insert_id($conn);
            
            $currentUser = User::getUserByEmail($_SESSION['email']);
            if($currentUser){
                $queryAddUser = "INSERT INTO `user_group`(`user_id`, `group_id`, `user_role`) 
                            VALUES (?,?, '2')";

                $stmt2 = mysqli_prepare($conn, $queryAddUser);
                if($stmt2){
                mysqli_stmt_bind_param($stmt2, "ii",$currentUser['id'], $groupId);
                mysqli_stmt_execute($stmt2);        
                mysqli_stmt_close($stmt2);    
                }
            }
            mysqli_stmt_close($stmt);
            header("Location: ../");
            exit;

        }else {
            die("error: added failed" . mysqli_stmt_error($stmt));
        }
    }
       public static function getGroupById($id){
    $id = $id['id'];
    $queryGet = "SELECT * FROM `groups` WHERE `id`='$id'";
    $get = mysqli_query(Connect::connect(), $queryGet);
     if(!$get){
            die("error: getting failed");
        }else{
            header("Location: ../");
        }
   }

   public static function getGroupByCode($code){
    $queryGet = "SELECT * FROM `groups` WHERE group_code = '$code'";
    $get = mysqli_query(Connect::connect(), $queryGet);
    return mysqli_fetch_assoc($get);
   }


  public static function getGroupByUser($data){
    $conn = Connect::connect();
    $userId = $data['userId'];
    $queryGet = "SELECT g.*
     FROM `groups` g
     INNER JOIN `user_group` ug ON g.id = ug.group_id
     WHERE ug.user_id = ?";

    $stmt = mysqli_prepare($conn,$queryGet);
    mysqli_stmt_bind_param($stmt,"i",$userId);
        if(!mysqli_stmt_execute($stmt)){
        error_log("Execute failed: " . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        return[];
    }
    $result = mysqli_stmt_get_result($stmt);

    if(!$result){
        error_log('Error: Failed to get user groups' . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        return[];
    }

    $groups = [];
    while($row = mysqli_fetch_assoc($result)){
        $groups[] = $row;
    }
    mysqli_stmt_close($stmt);
    return $groups;

   }

    public static function redactGroupName($data){
        $id = $data['groupId'];
        $title =  $data['groupTitle'];
        $qeuryRedact = "UPDATE `groups` SET `title`='$title' WHERE `id`='$id'";
        $redact = mysqli_query(Connect::connect(), $qeuryRedact);
        if(!$redact){
            die("error: redact failed");
        }else{
            header("Location: ../");
        }
    }

    public static function deleteGroup($id){
        $id = $id['id'];
        $queryDelete = "DELETE FROM `groups` WHERE `id`=$id";
        $delete = mysqli_query(Connect::connect(), $queryDelete);
        if(!$delete){
            die("error: delete failed");
        }else{
            header("Location: ../");
        }
    }

    public static function addUserToGroup($data){
        $userId = $data['userId'];
        $groupId = $data['groupId'];
        $userRole = $data['userRole'];

        $queryAdd = "INSERT INTO `user_group`(`id`, `user_id`, `group_id`, `user_role`) 
        VALUES (null,'$userId','$groupId','$userRole')";
        $add = mysqli_query(Connect::connect(), $queryAdd);
         if(!$add){
            die("error: added failed");
        }else{
            header("Location: ../");
        }
    }

    public static function redactUserRole($data){
        $userId = $data['userId'];
        $groupId = $data['groupId'];
        $newRole = $data['role'];
        $queryRedact = "UPDATE `user_group` 
        SET `user_role`='$newRole' WHERE `user_id` ='$userId' AND `group_id`='$groupId'";
        $redact = mysqli_query(Connect::connect(), $queryRedact);
        if(!$redact){
            die("error: redact failed");
        }else{
            header("Location: ../");
        }
    }

    public static function deleteUserFromGroup($data){
        $userId = $data['userId'];
        $groupId = $data['groupId'];
        $queryDelete = "DELETE FROM `user_group` WHERE `user_id` ='$userId' AND `group_id`='$groupId'";
        $delete = mysqli_query(Connect::connect(), $queryDelete);
        if(!$delete){
            die("error: delete failed");
        }else{
            header("Location: ../");
        }
    }


}

?>