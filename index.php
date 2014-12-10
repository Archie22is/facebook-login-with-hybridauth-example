<?php
  session_start();
  print_r($_SESSION);

  // 
  require_once('config.php');
  require_once('users.class.php');

  $error = null;
  $Users = new Users;

  // start login with facebook ?
  if (isset($_GET["login"]) AND $_GET["login"] == "facebook") {
    try {
      $ha = new Hybrid_Auth(HA_CONFIG);
      $f = $ha->authenticate("facebook");
      if ($f->isUserConnected()) {

        $user_profile = $f->getUserProfile();
        $access_token = $f->getAccessToken();
        //$contacts = $f->getUserContacts();

        if ($user_profile) {
          $provider = "facebook";
          $provider_uid = $user_profile->identifier;
          $email = $user_profile->email;
          $display_name = $user_profile->displayName;
          $photo_url = $user_profile->photoURL;

          if ($email) {
            $user_data = $Users->find_user_by_email($email);
          } else if ($provider_uid) {
            $user_data = $Users->find_user_by_provider_uid($provider,$provider_uid);
          }

          if ($user_data) {
            $user_id = $user_data["id"];
            $Users->update_user($user_id,$provider,$provider_uid,$email,$display_name,$photo_url);
            $Users->update_usermeta($user_id,$user_profile,$access_token["access_token"]);
          } else {
            $user_id = $Users->create_user($provider,$provider_uid,$email,$display_name,$photo_url);
            $Users->update_usermeta($user_id,$user_profile,$access_token["access_token"]);
          }

          $_SESSION["user_id"] = $user_id;
          session_write_close();
          $ha->redirect(BASE_URL);
        } else {
          $error = "unable to retrieve user profile";
        }

      } else {
        $error = "unable to authenticate";
      }
    }
    catch (Exception $e) {
      $error = "<b>got an error!</b> " . $e->getMessage(); 
    }
  }

  // logout user
  if (isset($_GET["logout"]) AND $_GET["logout"] == true) {
    try {
      $ha = new Hybrid_Auth(HA_CONFIG);
      $f = $ha->getAdapter("facebook");
      $f->logout();
      $_SESSION = array();
      session_destroy();
      $ha->redirect(BASE_URL);
    }
    catch (Exception $e) {
      $error = "<b>got an error!</b> " . $e->getMessage();
    }
  }

  // logged in ? User data
  if (isset($_SESSION["user_id"])) {
    $login_status = true;
    $user_id = $_SESSION["user_id"];
    session_write_close();
    $user_data = $Users->find_user_by_id($user_id);
    if (!$user_data) {
      // User does not exist in DB, log user out
      header("Location: ". BASE_URL ."?logout=true");
    }
  } else {
    $login_status = false;
  }

  echo "login status: " . $login_status;

?>