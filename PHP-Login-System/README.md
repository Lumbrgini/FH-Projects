[![Review Assignment Due Date](https://classroom.github.com/assets/deadline-readme-button-24ddc0f5d75046c5622901739e7c5dd533143b0c8e959d652212380cedb1ea36.svg)](https://classroom.github.com/a/mMKehb-s)
# Übung 5 – Login

HYP2UE_T1 Hypermedia 2 Serverseitige Programmierung | 15.04.2024 | Wolfgang Hochleitner | Abgabe

Übung 5 macht dort weiter, wo Übung 4 aufgehört hat. Es soll aus dem im Ordner `login` befindlichen Grundgerüst, eine fertige, geroutete Applikation erstellt werden, die es Ermöglicht, die Route "GET /main" nur zu betreten, wenn vorher ein erfolgreicher Login mit User-Daten aus der Datenbank "ue04_05_login" durchgeführt wurde.

## Den Startercode zum Laufen bringen

Zunächst muss der Startercode im Ordner `login` lauffähig gemacht werden. Es handelt sich dabei um ein auf *fhooe/router-skeleton* basierendes Projekt. Folgende Dinge sind zu tun.

1. Basispfad in `public/index.php` in Zeile 54 anpassen.
2. Projekt wie gewohnt aufrufen (zum Ordner `public` navigieren). Kommt eine Übersichtsseite funktioniert alles.
3. Optional: Datenbank und User anlegen. Sind von Übung 4 das Datenbankschema "ue04_05_login" mit Tabelle "User" und beliebigen Accounts vorhanden, können diese verwendet werden oder mit der `register`-Anwendung von Übung 4 auch neue angelegt werden. Alternativ können mit der Route `/createdb` (bereits fertig implementiert) zwei Accounts "user1@email.com" und "user2@email.com", jeweils mit Passwort "geheim" angelegt werden. Fehlen Schema oder Tabelle, werden diese ebenfalls erzeugt.

## Die Routen anlegen

In der Datei `public/index.php` müssen nun die notwendigen Routen angelegt werden (`/` und `/createdb` existieren bereits).

1. `GET /login`: Zeigt das Template `login.html.twig` an, welches das Loginformular beinhaltet. Diese Route soll nur aufrufbar sein, wenn man *nicht* eingeloggt ist (mithilfe von `requireNotLoggedIn()` in `RouteGuard` testbar).
2. `POST /login`: Verarbeitet den Loginprozess. Dies geschieht in der Klasse `Login`. Von ihr soll hier ein Objekt angelegt werden, darauf `isValid()` und im Anschluss `displayOutput()` aufgerufen werden.
3. `GET /main`: Die geschützte Hauptseite. Die Route zeigt `main.html.twig` an. Dies darf aber nur geschehen, wenn man eingeloggt ist (mithilfe von `requireLoggedIn()` in `RouteGuard` testbar).
4. `GET /logout`: Führt einen Logout durch. Dies geschieht durch leeren des Session-Arrays, Löschen des Session-Cookies und Beenden der Session. Danach wird auf `/` weitergeleitet.

## Die Klasse `Login` implementieren

Diese Klasse übernimmt die Abarbeitung des Login-Prozesses. Zunächst werden die Eingaben im Loginformular validiert. Bei gültigen Daten wird der Login verarbeitet, d.h. ein Session-Hash gesetzt und auf die geschützte Seite weitergeleitet. Bei fehlerhaften Eingaben wird das Loginformular erneut angezeigt.

### Überprüfen der Logindaten

Die Eingaben des Loginformulars werden in der Methode `isValid()` überprüft. Folgende Dinge sind dabei zu tun:

1. Überprüfen der Eingaben auf formale Korrektheit. Schlägt eine Überprüfung fehl, wird eine Fehlermeldung ins Array `$this->messages` gespeichert:
   1. Die E-Mail-Adresse darf nicht leer sein.
   2. Die E-Mail-Adresse muss in einem gültigen Format vorliegen.
   3. Das Passwort darf nicht leer sein.
2. Nun wird überprüft, ob diese Checks erfolgreich waren, d.h. ob das Array mit den Fehlermeldungen leer ist. Ist dies der Fall, wird getestet, ob die Logindaten (E-Mail-Adresse und Passwort) vorhanden und gültig sind. Dies geschieht durch Aufrufen von `authenticateUser()`. Diese Methode gibt `true` zurück, wenn die E-Mail-Adresse in der Datenbank existiert und das gespeicherte Passwort übereinstimmt. Ansonsten wird `false` zurückgegeben.
3. Je nachdem, welches Ergebnis von `authenticateUser()` zurückgegeben wurde, wird nun weiterverfahren:
   1. Bei `true`: Der Login war erfolgreich, es wird `business()` aufgerufen und der Login wird verarbeitet.
   2. Bei `false`: Es wird nichts mehr gemacht. Nach `isValid()` kommt ja `displayOutput()` und diese Methode zeigt das Loginformular wieder an (um erneut Eingaben zu machen bzw. diese zu korrigieren).

### Den Login verarbeiten

Ein erfolgreicher Login wird in `business()` verarbeitet. Hier passieren die folgenden Dinge:

1. Die E-Mail-Adresse wird in der Session (im Schlüssel "email") gespeichert. Damit steht sie auf den weiteren Seiten zur Verfügung und kann etwa auf der geschützten Seite angezeigt werden, um darzustellen, dass man eingeloggt ist).
2. Es wird der Login-Hash generiert. Dazu steht in der Klasse `Utiltities` die Methode `generateLoginHash()` zur Verfügung. Dieser Hash wird ebenfalls in der Session, aber unter dem Schlüssel "isLoggedIn" gespeichert. Dieser Wert bestimmt ultimativ, ob man eingeloggt ist oder nicht.
3. Mithilfe des `Router`-Objekts wird nun auf die Route `/main` weitergeleitet.

### Userdaten mit der Datenbank vergleichen

Die Methode `authenticateUser()` überprüft, ob die eingegebenen Daten (E-Mail-Adresse und Passwort) in der Datenbank genauso vorhanden sind.

1. Mithilfe eines SELECT-Statements wird ein Datensatz mit der im Loginformular eingegebenen E-Mail-Adresse gesucht.
2. Ist ein solcher Vorhanden, wird mit der PHP-Funktion `password_verify()` überprüft, ob der beim Eintrag gespeicherte Passwort-Hash das aktuell eingegeben Passwort enthält. Wenn ja, sind die Daten korrekt und es wird `true` zurückgegeben.
3. Bevor jedoch `true` zurückgegeben wird, soll mit `password_needs_rehash()` noch überprüft werden, ob der gespeicherte Passwort-Hash noch sicher ist. Gibt diese Methode `true` zurück, wird ein neues Passwort mit `password_hash` und `PASSWORD_DEFAULT` algorithmus erzeugt und mittels `updateUser()` in der Datenbank aktualisiert.
4. Ist entweder die E-Mail-Adresse gar nicht vorhanden oder das Passwort falsch, wird `false` zurückgegeben.

### Userdaten updaten

Im Falle eines zu aktualisierenden Passworts muss dieses in die Datenbank geschrieben werden. Dies geschieht in `updateUser($iduser, $password)`.

1. Mit einem UPDATE-Statement wird beim Eintrag mit der übergebenen ID das ebenfalls übergebene Passwort gesetzt. Dieses muss bereits gehashed übergeben werden (die Methode setzt prinzipiell nur den String in die Datenbank).

### Erneute Anzeige des Loginformulars

Die Methode `displayOutput()` wird immer dann aufgerufen, wenn beim Loginprozess Fehler aufgetreten sind. Sie zeigt also das Loginformular wieder an, übergibt aber Fehlermeldungen und auch die E-Mail-Adresse, die diese bereits wieder eingetragen ist, wenn das Formular angezeigt wird.

1. Mit Twig das Template `login.html.twig` anzeigen.
2. Den Parameter "email" übergeben und dort aus den POST-Daten die E-Mail-Adresse mitgeben.
3. Den Parameter "messages" übergeben und dabei den Inhalt von `$this->messages` mitgeben.

## Tipps und Richtlinien

- Verwenden Sie eine IDE, die für die Verwendung mit PHP konzipiert wurde (z.B. PhpStorm). Verwenden Sie immer die Autovervollständigung, wenn Sie neue Objekte anlegen, damit der volle Namespace verwendet wird.

- Sie können in PhpStorm eine Verbindung zu Ihrer Datenbank "ue04_05_login" herstellen, um die Datenbank direkt in der IDE inspizieren zu können. Öffnen Sie dazu das Tool Window "Database" (rechts oben) und legen Sie mit "+" eine neue Data Source an. Wählen Sie MariaDB aus und geben Sie die folgenden Verbindungsparameter ein:

  - Host: localhost
  - Port: 6033
  - User: hypermedia
  - Password: geheim
  - Database: ue04_05_login

  Die Verbindungsparameter sind hier unterschiedlich zu denen in der Webapplikation. Denn im Beispiel verbinden Sie sich vom webapp-Container in den db-Container. Hier verbinden Sie sich von ihrem Host-System in den db-Container (daher localhost und Port 6033 anstatt db und 3306).

- Bei Fragen oder Problemen zur Aufgabe eröffnen Sie ein Issue in ihrem Repository.
