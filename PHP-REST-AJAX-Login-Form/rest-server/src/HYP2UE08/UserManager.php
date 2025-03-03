<?php

namespace HYP2UE08;

use PDO;

/**
 * This class manages provides a method for adding a new user and generates appropriate JSON responses for a request.
 * @package HYP2UE08
 * @author Wolfgang Hochleitner <wolfgang.hochleitner@fh-hagenberg.at>
 * @version 2024
 */
class UserManager
{
    /**
     * @var PDO The PDO object.
     */
    private PDO $dbh;

    /**
     * @var string Defines the content type that this class unterstands.
     */
    private string $contentType;

    /**
     * Creates a new user manager allows to add a new user.
     */
    public function __construct()
    {
        $this->initDB();
        $this->contentType = "application/json";
    }

    /**
     * Initializes the database connection. Connects to the database "ue08_users".
     * @return void Returns nothing.
     */
    private function initDB(): void
    {
        $charsetAttr = "SET NAMES utf8 COLLATE utf8_general_ci";
        $dsn = "mysql:host=db;port=3306;dbname=ue08_users";
        $mysqlUser = "hypermedia";
        $mysqlPwd = "geheim";
        $options = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::MYSQL_ATTR_INIT_COMMAND => $charsetAttr,
            PDO::MYSQL_ATTR_MULTI_STATEMENTS => false
        ];
        $this->dbh = new PDO($dsn, $mysqlUser, $mysqlPwd, $options);
    }

    /**
     * Checks if a given user already exists in the database.
     * @param string $username The username to check.
     * @return bool Returns true if the user exists, otherwise false.
     */
    private function userExists(string $username): bool
    {
        // TODO: Make a database query and select the entry from the "user" table where the "username" equals the
        //   parameter $username.
        // TODO: If a result (a row) is returned then this method should return true, otherwise false.
        $sql = "SELECT COUNT(*) FROM user WHERE username = :username";

            $stmt = $this->dbh->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            $count = $stmt->fetchColumn();

            return $count > 0;
    }

    /**
     * Inserts a new user into the database and returns if the operation was successful.
     * @param string $username The username.
     * @param string $name The full name.
     * @param string $email The e-mail address.
     * @return bool Returns true if adding the user was successful, otherwise false.
     */
    private function insertUser(string $username, string $name, string $email): bool
    {
        // TODO: Insert a new entry into the "user" table with the provided arguments.
        // TODO: If the insertion was successful, this method should return true, otherwise false.
        $sql = "INSERT INTO user (username, name, email) VALUES (:username, :name, :email)";

            $stmt = $this->dbh->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
    }

    /**
     * Generates the API response for POST /adduser. This method retrieves the username, name and e-mail address from
     * the POST payload and checks if the username is empty or the user already exists. If this is the case, no new user
     * is added, and the respective JSON response is generated. Otherwise, the user is inserted into the database. If
     * this was successful, the method returns the appropriate JSON response; otherwise, a server error message is
     * created.
     * @return void Returns nothing because it generates JSON output.
     */
    public function addUser(): void
    {
        // TODO: Retrieve the values for the username, name and e-mail address from $_POST first.
        //  The field names (name attributes of the input elements) are "username", "name" and "email".
        $username  = $_POST["username"] ?? "";
        $name = $_POST["name"] ?? "";
        $email = $_POST["email"] ?? "";

        // TODO: A username has to be provided to add an entry into the database (name and email can stay empty strings).
        // TODO: Check if the username is empty. If so, create a response with status 400 BAD REQUEST.
        // TODO: Create a JSON response with the entries "result" and "messages" with useful content.
        //   "result" can be something like "User not created.", "message" should explain why (e.g., "No username provided"):
        // TODO: Create an associative array with the message strings and pass it with the status code to showResponse().
        if(empty($username)) {
            $jsonResp = [
                "result" => "User is not created",
                "message" => "Username is not provided"
            ];
                $this->showResponse(400,$jsonResp);
                return;
        }
        // TODO: If a username already exists, a new one must not be added to the database.
        // TODO: Check if the user already exists (userExists()). If so, create a response with status 409 CONFLICT.
        // TODO: Create a JSON response with "result" and "message" with meaningful content.

        if ($this->userExists($username)) {
            $jsonResp = [
                "result" => "User is not created",
                "message" => "This username is already exist"
            ];
            $this->showResponse(409,$jsonResp);
            return;

            //JSON response erzeugen
            // showResponse mitm Status 409
            //return;
        }
        // TODO: Now it's time to insert the user into the database. Call insertUser().
        // TODO: If the operation was successful, create a response with status 201 CREATED.
        // TODO: If the operation failed, create a response with status 500 INTERNAL SERVER ERROR.
        // TODO: Create a JSON response with "result" and "message" with meaningful content for both cases.

        if ($this->insertUser($username, $name, $email)) {
            $jsonResp = [
                "result" => "User is created",
                "message" => "User " . $username ." has been added"
            ];
            $this->showResponse(201,$jsonResp);
            return;
            //JSON response
            //showResponse mitm Status 201
        } else {
            $jsonResp = [
                "result" => "Internal server error",
                "message" => "Unexpected error has occurred"
            ];
            $this->showResponse(500, $jsonResp);
            // JSON Response
            // showResponse mitm Status 500
        }
    }

    /**
     * Generates a response with a given status code and, if provided, with the necessary content. The content has to be
     * provided as an associative array and is transformed into JSON.
     * @param int $statusCode The HTTP/REST status code to be returned.
     * @param array<string>|null $content The content that is transformed into JSON format and returned to the client.
     * @return void Returns nothing because it generates JSON output.
     */
    public function showResponse(int $statusCode, ?array $content = null): void
    {
        // If the request's Accept header contains our content type
        header("Content-Type: " . $this->contentType);
       // if (str_contains($_SERVER["HTTP_ACCEPT"], $this->contentType)) {
            http_response_code($statusCode);
            if (isset($content)) {
                echo json_encode($content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
      /*  } else {
            http_response_code(406);
            echo json_encode([
                "result" => "The provided content type is not supported.",
                "message" => "You provided " . $_SERVER["HTTP_ACCEPT"] . " but this server only serves " . $this->contentType . "."
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }*/
    }
}
