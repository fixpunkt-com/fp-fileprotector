# Einen Storage schützen

Im Backendmodul "Dateischutz" unter "Dateien" erhältst du eine Übersicht über alle Storages deiner TYPO3 Instanz und ob und wenn ja wie diese geschützt sind. 

![alt_text][storage_list]

Möchtest du den Schutz eines Storages bearbeiten klicke auf den "Bearbeiten"-Knopf (hier orange umrandet). Danach hast du folgende Optionen:

![alt_text][storage_edit]

``Geschützter File Storage``
Hier kannst du einstellen, ob der Storage grundsätzlich geschützt werden soll oder nicht. Definierte Zugriffsregeln werden nur angewendet, wenn diese Option aktiviert ist. Ansonsten ist der Storage nicht geschützt.

``Bei fehlender Freigaberegel Zugriff verweigern`` Hier kannst du auswählen, wie sich der Storage standardmäßig verhalten soll.
* Möchtest du, dass auf den Storage **grundsätzlich zugegriffen werden kann** und nur einzelne Ordner geschützt sind, solltest du diese Option deaktivieren. Dateien in Ordnern, für die keine Zugriffsregel definiert sind, können dann immer aufgerufen werden.
* Möchtest du, dass auf den Storage **grundsätzlich nicht zugegriffen werden kann** und du Ordner eher freigeben willst, solltest du diese Option aktivieren. Dateien in Ordnern, für die keine Zugriffsregel definiert sind, können dann nicht angesehen werden.

[storage_list]: Images/storage_list.png "Listenansicht aller Storages."
[storage_edit]: Images/storage_edit.png "Einstellungen eines Storages."