<?php
/**
* This is Users class, deals with finding, updating, creating user
*/
class Users
{

	private $host   = DB_HOST;
	private $user   = DB_USERNAME;
	private $pass   = DB_PASSWORD;
	private $dbname = DB_NAME;

	private $conn;
	private $stmt;
	public  $error;

	function __construct()
	{
		$dsn = 'mysql:host='.$this->host.';dbname='.$this->dbname.';charset=utf8';
		$options = array(
			PDO::ATTR_EMULATE_PREPARES  => false,
			PDO::ATTR_PERSISTENT        => true,
			PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION
		);
		try {
			$this->conn = new PDO($dsn,$this->user,$this->pass,$options);
		} catch (PDOException $e) {
			$this->error = $e->getMessage();
		}
	}

	private function mysql_execute_query($sql,$params)
	{
		$this->stmt = $this->conn->prepare($sql);
		$this->stmt->execute($params);
		return $this->stmt;
	}

	public function find_user_by_provider_uid($provider,$provider_uid)
	{
		$sql = 'SELECT * FROM users WHERE provider = :provider AND provider_uid = :provider_uid LIMIT 1';
		$params = array(
			':provider'     => $provider,
			':provider_uid' => $provider_uid
		);
		$result = $this->mysql_execute_query($sql,$params);
		return $result->fetch(PDO::FETCH_ASSOC);
	}

	public function find_user_by_email($email)
	{
		$sql = 'SELECT * FROM users WHERE email = :email LIMIT 1';
		$params = array(
			':email' => $email
		);
		$result = $this->mysql_execute_query($sql,$params);
		return $result->fetch(PDO::FETCH_ASSOC);
	}

	public function find_user_by_id($id)
	{
		$sql = 'SELECT * FROM users WHERE id = :id LIMIT 1';
		$params = array(
			':id' => $id
		);
		$result = $this->mysql_execute_query($sql,$params);
		return $result->fetch(PDO::FETCH_ASSOC);
	}

	public function create_user($provider,$provider_uid,$email,$display_name,$photo_url)
	{
		$sql = 'INSERT INTO users
							(provider,provider_uid,email,display_name,photo_url)
					 VALUES (:provider,:provider_uid,:email,:display_name,:photo_url)';
		$params = array(
			':provider'     => $provider,
			':provider_uid' => $provider_uid,
			':email'        => $email,
			':display_name' => $display_name,
			':photo_url'    => $photo_url
		);
		$result = $this->mysql_execute_query($sql,$params);
		return $this->conn->lastInsertId();
	}

	public function update_user($user_id,$provider,$provider_uid,$email,$display_name,$photo_url)
	{
		$sql = 'UPDATE users SET provider = :provider,
								 provider_uid = :provider_uid,
								 email = :email,
								 display_name = :display_name,
								 photo_url = :photo_url
						   WHERE id = :id';
		$params = array(
			':provider'     => $provider,
			':provider_uid' => $provider_uid,
			':email'        => $email,
			':display_name' => $display_name,
			':photo_url'    => $photo_url,
			':id'           => $user_id
		);
		$result = $this->mysql_execute_query($sql,$params);
	}

	public function update_usermeta($user_id,$user_profile,$access_token)
	{
		$user_profile->accessToken = $access_token;
		
		unset($user_profile->identifier);
		unset($user_profile->photoURL);
		unset($user_profile->displayName);
		unset($user_profile->email);

		$sql_select = 'SELECT EXISTS(SELECT 1 FROM users_meta WHERE user_id = :user_id AND meta_key = :meta_key) AS row_exists';

		$sql_insert = 'INSERT INTO users_meta
								   (user_id,meta_key,meta_value)
					 		VALUES (:user_id,:meta_key,:meta_value)';

		$sql_update = 'UPDATE users_meta SET meta_value = :meta_value
									   WHERE user_id = :user_id
										 AND meta_key = :meta_key';
					 
		foreach ($user_profile as $meta_key => $meta_value) {
			$params_select = array(
				':meta_key'   => $meta_key,
				':user_id'    => $user_id
			);
			$params = array(
				':meta_key'   => $meta_key,
				':meta_value' => $meta_value,
				':user_id'    => $user_id
			);

			$result = $this->mysql_execute_query($sql_select,$params_select);
			$row = $result->fetch(PDO::FETCH_ASSOC);

			if ($row["row_exists"] === 1) {
				$this->mysql_execute_query($sql_update,$params);
			} else if ($row["row_exists"] === 0) {
				$this->mysql_execute_query($sql_insert,$params);
			}
		}
	}

	public function recently_registered_users($max)
	{
		$sql = 'SELECT * FROM users ORDER BY created DESC LIMIT :max';
		$params = array(
			':max' => $max
		);
		$result = $this->mysql_execute_query($sql,$params);
		return $result->fetchAll(PDO::FETCH_ASSOC);
	}

}
?>