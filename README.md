# TYPO3 Extension: fp-fileprotector

Die Extension **fp-fileprotector** ermöglicht es, den Zugriff auf File Storages (Dateispeicher) in TYPO3 einzuschränken und granulare Zugriffsregeln für einzelne Dateien und Ordner zu definieren.

## Inhaltsverzeichnis
1. [Funktionsweise](#funktionsweise)
2. [Einen Storage schützen](#einen-storage-schützen)
3. [Zugriffsregeln für Ordner](#zugriffsregeln-für-ordner)
    - [Vererbung](#vererbung)
    - [Übersicht & Statusfarben](#übersicht--statusfarben)
    - [Regeln anlegen](#regeln-anlegen)
4. [Fehlerbehebung](#fehlerbehebung)

---

## Funktionsweise
Mit *fp-fileprotector* kannst du den Zugriff auf Dateien an Bedingungen knüpfen:
* **Frontend-Status:** Ist ein:e Benutzer:in eingeloggt?
  * Prüfung auf spezifische Benutzergruppen.
  * Prüfung auf spezifische Einzelbenutzer:innen.
* **Backend-Status:** Ist ein:e Benutzer:in im Backend angemeldet?

---

## Einen Storage schützen

Um die Funktionen zu nutzen, musst du zuerst einen Storage im Backendmodul **Dateischutz** (unter dem Hauptmodul „Dateien“) aktivieren.

![alt_text][storage_list]

Klicke auf den **Bearbeiten-Knopf**, um folgende Optionen festzulegen:

* **Geschützter File Storage:** Aktiviert den Schutz grundsätzlich. Ohne diesen Haken ist der Storage öffentlich zugänglich.
* **Bei fehlender Freigaberegel Zugriff verweigern:**
    * **Deaktiviert (Whitelist-Prinzip):** Grundsätzlich erlaubt, nur markierte Ordner werden gesperrt.
    * **Aktiviert (Blacklist-Prinzip):** Grundsätzlich gesperrt, Zugriff muss explizit pro Ordner erlaubt werden.

![alt_text][storage_edit]

---

## Zugriffsregeln für Ordner

Jeder Ordner innerhalb eines geschützten Storages kann individuelle Regeln erhalten.

### Vererbung
* **Weitergabe:** Regeln werden automatisch an alle Unterordner vererbt.
* **Überschreiben:** Hat ein Unterordner eigene Regeln, ergänzen diese die übergeordneten Regeln **nicht**, sondern überschreiben sie komplett.

### Übersicht & Statusfarben
In der Listenansicht des Moduls (Klick auf das **Auge-Symbol**) siehst du den Status aller Ordner:

![alt_text][storage_show]
![alt_text][folder_list]

* **Grün:** Zugriff für jede:n erlaubt.
* **Orange:** Zugriff ist durch eine Regel beschränkt.
* **Rot:** Niemand hat Zugriff (z. B. wenn der Storage standardmäßig sperrt und keine Regel existiert).

**Symbole:**
* 🔒 **Orangenes Schloss:** Eine spezifische Zugriffsregel ist definiert.
* 🔓 **Grünes offenes Schloss:** Eine Regel ist definiert, enthält aber keine Einschränkungen (Zugriff offen).
* **(geerbt):** Die Regel stammt von einem übergeordneten Ordner.

### Regeln anlegen
1. Wähle einen Ordner im Seitenbaum oder klicke in der Liste auf den Namen.
2. Klicke auf **Zugriffsschutz anlegen** oder **bearbeiten**.
3. Wähle die gewünschten Kriterien (Gruppen, Benutzer:innen oder Backend-Login).

![alt_text][folder_show]
![alt_text][protection]

> **Hinweis zur Logik:** Die Auswahl von Gruppen und Benutzern ist eine **ODER-Verknüpfung**. Sowohl Mitglieder der Gruppen als auch die einzeln gewählten Personen erhalten Zugriff.

---

## Fehlerbehebung

### Trotz Schutz sind alle Dateien öffentlich aufrufbar
Prüfe, ob im Hauptverzeichnis des Storages eine `.htaccess`-Datei liegt, die Anfragen an den *fp_fileprotector* umleitet.
* Eine Vorlage findest du unter: `Resources/Private/htacces.txt`.
* Stelle sicher, dass der Webserver `.htaccess`-Dateien verarbeiten darf (`AllowOverride All`).

### Benutzergruppen werden ignoriert
Prüfe, ob in der Zugriffsregel die Option **"Muss im Frontend eingeloggt sein"** aktiviert ist. Ohne diesen Haken greifen die Gruppen-Einschränkungen nicht.

### Wie hebe ich eine geerbte Sperre wieder auf?
Lege für den betroffenen Unterordner einfach eine neue Zugriffsregel an, ohne Bedingungen auszuwählen. Damit wird der Zugriff für diesen Zweig wieder für alle freigegeben.

---

[storage_show]: /docs/Images/storage_show.jpg "Listenansicht aller Storages."
[folder_list]: /docs/Images/folder_list.png "Auflistung aller Ordner und Zugriffsregeln eines Storages."
[folder_show]: /docs/Images/folder_show.png "Zeigt den aktuellen Status eines einzelnen Ordners an."
[protection]: /docs/Images/protection.png "Einstellungen, mit denen der Ordner geschützt werden kann."
[storage_list]: /docs/Images/storage_list.png "Listenansicht aller Storages."
[storage_edit]: /docs/Images/storage_edit.png "Einstellungen eines Storages."
