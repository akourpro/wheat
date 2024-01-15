<?php

/**
 * A simple CSRF class to protect forms against CSRF attacks. The class uses
 * PHP sessions for storage.
 * 
 * @author Raahul Seshadri
 *
 */
class CSRF_Protect
{
	/**
	 * The namespace for the session variable and form inputs
	 * @var string
	 */
	private $namespace;

	public $workspace;
	/**
	 * Initializes the session variable name, starts the session if not already so,
	 * and initializes the token
	 * 
	 * @param string $namespace
	 */
	public function __construct($namespace = '_csrf', $workspace)
	{
		$this->namespace = $namespace;

		if (session_id() === '') {

			sec_session_start($workspace);
		}

		$this->setToken();
	}

	/**
	 * Return the token from persistent storage
	 * 
	 * @return string
	 */
	public function getToken()
	{
		return $this->readTokenFromStorage();
	}

	/**
	 * Verify if supplied token matches the stored token
	 * 
	 * @param string $userToken
	 * @return boolean
	 */
	public function isTokenValid($userToken)
	{
		return ($userToken === $this->readTokenFromStorage());
	}

	/**
	 * Echoes the HTML input field with the token, and namespace as the
	 * name of the field
	 */
	public function input()
	{
		$token = $this->getToken();
		echo "<input type=\"hidden\" name=\"" . safer($this->namespace) . "\" value=\"" . safer($token) . "\" />";
	}

	/**
	 * Echoes the HTML input field with the token, and namespace as the
	 * name of the field
	 */
	public function header()
	{
		return $this->getToken();
	}

	/**
	 * Verifies whether the post token was set, else dies with error
	 */
	public function verify($type = "form")
	{
		if ($type == "ajax") {
			if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
				header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
				exit;
			}
			$headers = getallheaders();
			if (isset($headers[$this->namespace])) {
				if (!$this->isTokenValid($headers[$this->namespace])) {
					header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
					exit;
				}
			} else {
				header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
				exit;
			}
		} else {
			if (!$this->isTokenValid($_POST[$this->namespace])) {
				header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
				exit;
			}
		}
	}

	/**
	 * Generates a new token value and stores it in persisent storage, or else
	 * does nothing if one already exists in persisent storage
	 */
	private function setToken()
	{
		$storedToken = $this->readTokenFromStorage();

		if ($storedToken === '') {
			$token = bin2hex(random_bytes(32));
			$hashHmac = hash_hmac("sha256", "C98CCF7DAFD898429EFCF879AF67F", $token);
			$this->writeTokenToStorage($hashHmac);
		}
	}

	/**
	 * Reads token from persistent sotrage
	 * @return string
	 */
	private function readTokenFromStorage()
	{
		if (isset($_SESSION[$this->namespace])) {
			return $_SESSION[$this->namespace];
		} else {
			return '';
		}
	}

	/**
	 * Writes token to persistent storage
	 */
	private function writeTokenToStorage($token)
	{
		$_SESSION[$this->namespace] = $token;
	}
}
