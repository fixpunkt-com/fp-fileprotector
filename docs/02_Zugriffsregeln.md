# Zugriffsregeln
Jeder Ordner innerhalb eines geschützen Storages kann mit Zugriffsregeln versehen werden, welche steuern, welche Benutzer:innen auf die Dateien innerhalb dieses Ordners zugreifen dürfen und welche nicht.

Bitte beachte, dass Zugriffsregeln an Unterordner weitervererbt werden - es sei denn - diese Ordner haben eigene Zugriffsregeln. Dabei ergänzen die Zugriffsregeln untergeordneter Ordner die bereits vorhandenen Regeln nicht, sondern überschreiben sie komplett.

## Übersicht über alle Zugriffsregeln
Wenn du das Backendmodul "Dateischutz" unter "Dateien" öffnest, erhältst du eine Übersicht über alle Storages.

![alt_text][storage_show]

Klicke hier auf das Auge (im Bild orange umkreist) um eine Übersicht aller Ordner und Zugriffsregeln zu erhalten.

![alt_text][folder_list]

Die Namen der Ordner sind dabei je nach Zugriffsmöglichkeiten eingefärbt:
* grün, wenn jede:r auf den Ordner zugreifen kann.
* orange, wenn der Zugriff durch eine Zugriffsregel beschänkt ist.
* rot, wenn niemand auf den Ordner zugreifen kann (beispielsweise, wenn der Zugriff des Storages standardmäßig verboten ist und keine Zugriffsregel angelegt worden ist).

Ordner mit Zugriffsregeln können anhand eines sich neben dem Namen befindlichen Schloss' erkannt werden:
* Ein **orangenes Schloss* neben einem Ordnernamen zeigt an, dass für diesen Ordner eine Zugriffsregel definiert ist.
* Ein **geöffentes grünes Schloss** neben einem Ordnernamen zeigt an, dass für diesen Ordner zwar eine Zugriffsregel definiert ist, diese aber keine Regeln enthält.
* Das Wort **(geerbt)** zeigt an, dass die Zugriffsregel von einem höherliegenden Ordner geerbt wurde.

## Zugriffsregeln anlegen

Um eine Zugriffsregel anzulegen klicke entweder in den Listenansicht auf den Ordnernamen oder wähle ihn links im Seitenbaum aus.

![alt_text][folder_show]

Daraufhin bekommst du eine Übersicht über den Ordner und kannst eine neue Zugriffsregel anlegen oder eine bestehende bearbeiten. Hier können auch existierende Zugriffsregeln gelöscht werden.

Klicke hier auf **Zugriffsschutz anlegen** oder **Zugriffsschutz bearbeiten**.

![alt_text][protection]

Hier kannst du nun auswählen, wer alles auf den Inhalt des jeweiligen Ordners (sowie Unterordner) zugreifen darf.


[storage_show]: Images/storage_show.jpg "Listenansicht aller Storages."
[folder_list]: Images/folder_list.png "Auflistung aller Ordner und Zugriffsregeln eines Storages."
[folder_show]: Images/folder_show.png "Zeigt den aktuellen Status eines einzelnen Ordners an."
[protection]: Images/protection.png "Einstellungen, mit denen der Ordner geschützt werden kann."