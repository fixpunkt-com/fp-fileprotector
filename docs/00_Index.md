# Seiten komfortabel sperren
Mit *fp_sitelock* können Seiten komfortabel gesperrt werden. Benutzer:innen können den Inhalt nur noch einsehen wenn Sie...
* ...im Backend angemeldet sind.
* ...sich mit einem Passwort im Frontend authentifiziert haben.

Aus Sicherheitsgründen wird die Authentifzierung standardmäßig nur verlangt, wenn die Seite im Kontext ``Development`` oder ``Testing`` befindet. Der Kontext ``Produktion`` kann auf Wunsch ebenfalls gesperrt werden.

Ist die Seite nicht entsperrt, wird ein `401 Unauthorized` HTTP-Statuscode zurückgegeben.

## Einrichtung
Alle Informationen zur Einrichtung finden Sie unter [Konfiguration](01_Konfiguration.md).

## Ansicht im Frontend
### Ohne Passwort
Wenn im Backend kein Passwort zum Entsperren der Seite festgelegt ist, wird den Besucher:innen ein Hinweis angzeigt, sich im Backend anzumelden.

![alt_text][error]

### Mit Passwort
Wenn im Backend ein Passwort zum Entsperren der Seite festgelegt ist, wird den Benutzer:innen ein Login-Formular angezeigt.

![alt_text][form]

[error]: images/error.png "Eine Fehlermeldung, dass auf die Seite nicht zugegriffen werden kann."
[form]: images/login.png "Eine Login-Formular um die Seite zu entsperren."
[be]: images/be.png "Konfigration der Extension im Backend."