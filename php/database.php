<?php 

   function java_log(string $log) 
   {
      ?> 
         <script>
            console.log("<?=$log?>");
         </script>
      <?php
   }

   function phpError_log(string $err)
   {
      $_SESSION["error_log"] = $err;
      java_log($err);
   }
   
   function  mysqli_open() 
   {
      $user = 'apache';
      $password = 'cible123';
      $db = 'social_network';
      $host = 'localhost';
      $port = 7077;
      $link = mysqli_connect($host, $user, $password, $db, $port);
      if (!$link) {
         $err = "Connection failed: " . mysqli_connect_error() ; 
         phpError_log($err);
         die($err);
      }
      return $link;
   }

   function generateID(\mysqli $link, string $table) 
   {
      $tables = array(         
                        "user" => array("UserID", "U"),
                        "post" => array("PostID", "P"),
                        "comment" => array("CommentID", "CM"),
                        "answer" => array("AnswerID", "A"),
                        "media" => array("MediaID", "M"),
                        "conversation" => array("ConversationID", "CV")
                     );
      $sql = "SELECT MAX(REPLACE(".$tables[$table][0].",'".$tables[$table][1]."','') + 1) FROM `".$table."`";
      $result = mysqli_query($link, $sql);
      if(mysqli_num_rows($result) != 1) {
         phpError_log("can't genereate new id for :".$table);
         return "Na".$tables[$table][1];
      } else {
         $row = mysqli_fetch_array($result);
         return ($row[0] == NULL ? $tables[$table][1]."1" : $tables[$table][1].$row[0]); 
      }
   }

   function newUser(array $user) {
      $link = mysqli_open();
      $newId = generateID($link, "user");
      $sql ="INSERT INTO 
            `user`(
                  `UserID`, `Username`, 
                  `Name`, `Firstname`, 
                  `Mail`, `Country`, 
                  `City`, `BirthDate`, 
                  `PhoneNumber`, `Sex`, 
                  `IsAdmin`, `Theme`, `IsPremium`
                  )
            VALUES (
                     '".$newId."', '".$user["userName"]."',
                     '".$user["name"]."', '".$user["firstName"]."',
                     '".$user["mail"]."', '".$user["country"]."',
                     '".$user["city"]."', '".$user["birthDate"]."',
                     '".$user["phoneNumber"]."', '".$user["sex"]."',
                     '0', '0', '0'
                   );";
      $newUser_str = "\\n(".
                     "\\n".$newId.", ".$user["userName"].",".
                     "\\n".$user["name"].", ".$user["firstName"].",".
                     "\\n".$user["mail"].", ".$user["country"].",".
                     "\\n".$user["city"].", ".$user["birthDate"].",".
                     "\\n".$user["phoneNumber"].", ".$user["sex"].
                     "\\n)";
      if(mysqli_query($link, $sql)){
         java_log("User succesfully added :".$newUser_str);
      } else {
         phpError_log("Cannot create user :".$newUser_str);
      }
      mysqli_close($link);
   }

   $user = array(
      "userName" => "Clef Man",
      "name" => "Cassiet",
      "firstName" => "Clement",
      "mail" => "clemDu78@yes.fr",
      "country" => "Venezuela",
      "city" => "Zibaboue",
      "birthDate" => "1999-01-01", 
      "phoneNumber" => "7777777",
      "sex" => "1"
   );
   newUser($user);
   echo $_SESSION["error_log"];


  
?>
