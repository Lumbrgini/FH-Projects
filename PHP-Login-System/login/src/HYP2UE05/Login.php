<?php

namespace HYP2UE05;

use Fhooe\Router\Router;
use PDO;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * This class enables users to log in to the system with a provided username and password. Both items are matched with
 * stored credentials. If they match, a login hash is stored in the session that acts as a token for a successful login.
 * Other pages can then use check for this token before the site is initialized and perform a redirect to prevent
 * accessing the page
 * @package HYP2UE05
 * @author Wolfgang Hochleitner <wolfgang.hochleitner@fh-hagenberg.at>
 * @version 2024
 */
final class Login
{
    /**
     * @var PDO The PDO object.
     */
    private PDO $dbh;

    /**
     * @var array<string, string> This array is used to store error and status messages
     * after a form was sent and validated.
     */
    private array $messages = [];

    /**
     * @var Environment Provides a Twig object to display HTML templates.
     */
    private Environment $twig;

    /**
     * @var Router The Router object for redirecting the user to the main page after a successful login.
     */
    private Router $router;

    /**
     * Creates a new Login object. It takes a Twig Environment object that is used to display a response (output).
     * The constructor needs to initialize the database for reading and updating user information.
     * @param Environment $twig The Twig object for displaying a response.
     * @param Router $router The Router object for redirecting the user to the main page after a successful login.
     */
    public function __construct(Environment $twig, Router $router)
    {
        $this->twig = $twig;
        $this->router = $router;
        $this->initDB();
    }

    /**
     * Initializes the database connection. Connects to the database "login_example".
     * @return void Returns nothing.
     */
    private function initDB(): void
    {
        $charsetAttr = "SET NAMES utf8 COLLATE utf8_general_ci";
        $dsn = "mysql:host=db;port=3306;dbname=ue04_05_login";
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
     * Validates the user input. If errors are detected, they are stored in the messages array.
     * If no errors are found, the method business() is invoked which continues the login process and forwards the
     * logged-in user to the protected content.
     * @return void Returns nothing.
     */
    private $email;
    private $message = [];
    private $password;


    public function isValid(): void
    {
        // TODO: Login-Daten validieren und für jeden Fehler die entsprechende Meldung im Array $this->messages speichern.
        // TODO: 1. Die E-Mail-Adresse darf nicht leer sein.
        if(empty($_POST['email'])){
            $this -> messages = ["Invalid E-Mail"];
        }

        // TODO: 2. Die E-Mail-Adresse muss ein gültiges Format haben.
        $pattern = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
        if (!preg_match($pattern, $_POST['email'])) {
            $this->messages = ["Invalid format for Email"];
        }

        // TODO: 3. Das Passwort darf nicht leer sein.
        if(empty($_POST['password'])){
            $this->messages = ["Invalid password"];
        }

        // TODO: Nun die Logik implementieren, wie es weitergeht, nachdem die Eingaben überprüft wurden.
        // TODO: Überprüfen, ob $this->>messages leer ist. Wenn ja, dann sind es korrekte Daten und dann ..
        if(empty($this->messages)){
            if($this->authenticateUser()){
                $this->business();
            } else{
                $this->messages = ["Authentication failed"];
            }
        }

        // TODO: ... die Methode authenticateUser() aufrufen, um zu prüfen, ob die E-Mail-Adresse in dieser Form in der Datenbank existieren.
        // TODO: Gibt authenticateUser() true zurück, dann stimmen die Daten und es geht weiter mit der Methode business().
        // TODO: Gib authenticateUser() false zurück, dann ist die E-Mail-Adresse oder das Passwort falsch und es wird eine Fehlermeldung gespeichert.
        // TODO: Egal ob Fehlermeldungen durch die 3 ersten Validierungen oder bei authenticateUser() aufgetreten sind,
        // TODO: wir müssen die Login-Seite erneut anzeigen. Nach isValid() wird ohnehin displayOutput() aufgerufen, dort passiert das.
    }

    /**
     * This method is only called when the form input was validated successfully.
     * It stores the e-mail address in the session for further use (e.g. in the template) and a hash value to identify a
     * successful login.
     * It then forwards to the protected main page.
     * @return void Returns nothing.
     */
    protected function business(): void
    {
        // TODO: Speichere die E-Mail-Adresse in der Session, damit sie in der Template-Datei verwendet werden kann.
        $_SESSION['email'] = $_POST['email'];

        // TODO: Generiere einen Login-Hash mit der Klasse Utilities und speichere ihn in der Session unter dem Schlüssel "isLoggedIn".
        $loginHash = Utilities::generateLoginHash();
        $_SESSION['isLoggedIn'] = $loginHash;

        // TODO: Leite mit dem Router-Objekt auf die geschützte Hauptseite "/main" weiter.
        $this->router->redirectTo("/main");
    }

    /**
     * Authenticates a user by matching the entered e-mail address (username) and password with the stored records.
     * If the username is present and the entered password matches the stored password, a valid login is assumed and
     * stored in $_SESSION. After a successful login, the current password encryption is checked and if necessary, a
     * rehash is performed and the updated password is stored in the database.
     * @return bool Returns true if the combination of username and password is valid, otherwise false.
     */
    private function authenticateUser(): bool
    {
        // TODO: Schreibe eine SQL-Query (SELECT), um die ID, E-Mail-Adresse und das Passwort des Users zu holen, der*die die eingegebene E-Mail-Adresse hat.
        $query = "SELECT iduser, email, password FROM user WHERE email = :email";

        // TODO: Ein prepared Statement wird benötigt, um SQL-Injections zu verhindern. Verwende :email als Platzhalter für die E-Mail-Adresse.
        $statement = $this->dbh->prepare($query);
        $statement->bindParam(':email', $_POST['email']);
        $statement->execute();

        // TODO: Führe das Statement aus und frage alle Zeilen ab.
        $user = $statement->fetch(PDO::FETCH_ASSOC);

        // TODO: Wenn genau eine Zeile zurückgegeben wird und das eingegebene Passwort mit dem gespeicherten Passwort übereinstimmt, dann gibt die Methode true zurück.
        // TODO: Die Überprüfung des Passworts erfolgt mit password_verify().
        if ($user && $statement->rowCount() == 1 && password_verify($_POST['password'], $user['password'])) {

        // TODO: Wenn das Passwort ein Rehash benötigt (password_needs_rehash()), dann wird das Passwort neu gehasht und in der Datenbank gespeichert. Dies wird mit der Methode updateUser() durchgeführt.
        if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
            $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $this->updateUser($user['id'], $hashedPassword);
        }
        return true;

        // TODO: Wird kein passender Datensatz gefunden oder das Passwort stimmt nicht überein, dann gibt die Methode false zurück.
        } else {
            // This method just returns false for now. It needs to be fully implemented (see TODOs above).
            return false;
        }
    }
    /**
     * Replaces a user's password with a new one if an outdated hashing algorithm has been detected.
     * @param string $iduser The user ID.
     * @param string $password The new password.
     * @return void Returns nothing.
     */
    private function updateUser(string $iduser, string $password): void
    {
        // TODO: Schreibe eine SQL-Query (UPDATE), um das Passwort des Users mit der ID $iduser zu aktualisieren.
        $sql = "UPDATE user SET password = :password WHERE id = :iduser";

        // TODO: Ein prepared Statement wird benötigt, um SQL-Injections zu verhindern. Verwende :password und :iduser als Platzhalter für das Passwort und die ID.
        $statement = $this->dbh->prepare($sql);

        // TODO: Führe das Statement aus und übergebe die Parameter.
        $statement->bindParam(':password', $password, PDO::PARAM_STR);
        $statement->bindParam(':iduser', $iduser, PDO::PARAM_STR);
        $statement->execute();

        // TODO: Der Parameter $password enthält das neue, bereits gehashte Passwort, das in der Datenbank gespeichert werden soll.
    }

    /**
     * Renders the login form and displays it.
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function displayOutput(): void
    {
        //Commit Versuch
        $etwas = 0;

        $email = isset($_POST['email']) ? $_POST['email'] : '';
        // TODO: Rufe die Methode display() des Twig-Objekts auf, um das Template "login.html.twig" anzuzeigen.
        echo $this->twig->render('login.html.twig', [

        // TODO: Übergebe die E-Mail-Adresse aus dem Formular mit dem Schlüssel "email" (damit es vorausgefüllt ist) ...
        'email' => $email,


        // TODO: ... und das Array $this->messages, das alle Fehlermeldungen enthält, mit dem Schlüssel "messages".
        'messages' => $this->messages
        ]);
    }
}
