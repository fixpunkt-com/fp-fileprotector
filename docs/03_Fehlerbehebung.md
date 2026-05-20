# Fehlerbehebung

## Schützen von Storages
### Obwohl ich einen Storage als geschützt markiert habe, kann ich auf alle Dateien zugreifen.
Bitte überprüfe ob im Storage-Verzeichnis eine ``.htaccess``-Datei vorhanden ist und diese die Anfragen an fp-fileprotector weiterleitet. Eine Vorlage der ``.htaccess``-Datei findest du in den Resourcen dieser Extension (``Resources/Private/htacces.txt``). Soltle die Datei fehlen, lege die ``.htaccess``-Datei bitte selber an.

Prüfe außerdem, ob die ``.htaccess``-Datei richtig ausgeführt werden kann (Zugriffsberechtigungen, Dateibesitzer:in...)

## Zugriffsregel

### Die angegebenen Benutzer:innen-Gruppen oder Benutzer:innen werden nicht berücksichtigt.
Bitte prüfe, ob du bei der Zugriffsregel die Option ``Muss im Frontend eingeloggt sein`` aktiviert ist, da ansonsten die Angaben für Benutzer:innen-Gruppen und Benutzer:innen keine Anwendung finden.

### Ein:e Benutzer:in hat Zugriff auf die Dateien, obwohl sie nicht Mitglied einer der ausgewählten Benutzer:inne-Gruppen ist.
Bitte beachte, dass die Auswahl der Gruppen und einzelnen Benutzer:innen eine **ODER** Konjunktion ist. Sowohl alle Mitglieder der ausgewählten Benutzer:innen-Gruppen **sowie zusätzlich** alle ausgewählten Benutzer:innen können auf die Dateien zugreifen.

### Kann ich eine Zugriffsregel für tiefer gelegene Ordner auch wieder aufheben?
Ja, bitte lege einfach eine Zugriffsregel an und wähle keine Bedingungen aus. Der Zugriff wird dann wieder für alle gestattet.