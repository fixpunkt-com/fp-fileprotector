<?php

namespace Fixpunkt\FpFileprotector\Utility;

use Fixpunkt\FpFileprotector\Resource\ResourceStorage;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class HtaccessUtility {
    /** @var ResourceStorage  */
    protected ResourceStorage $resourceStorage;

    /**
     * @param ResourceStorage $resourceStorage
     */
    public function __construct(ResourceStorage $resourceStorage) {
        $this -> resourceStorage = $resourceStorage;
    }

    /**
     * Prüft ob der Storage durch eine .htaccess-Datei geschützt ist.
     * @return bool
     */
    public function hasHtaccess() : bool {
        return $this -> getHtaccessPosition() >= 0;
    }

    /**
     * Fügt einer .htaccess-Datei die Schutzregeln hinzu, wenn diese noch nicht existieren.
     * @return void
     */
    public function addHtaccess() : void {
        if($this -> hasHtaccess()) {
            return;
        }

        $handle = fopen($this -> getHtaccessPath(), 'a');
        foreach($this -> getHtaccessTemplate() as $templateLine) {
            fwrite($handle, "\n".$templateLine);
        }
        fclose($handle);
    }


    /**
     * Entfernt die Schutzregel aus einer .htaccess-Datei.
     * @return void
     */
    public function removeHtaccess() : void {
        if(!$this -> hasHtaccess()) {
            return;
        }

        // Position ermitteln
        $position = $this -> getHtaccessPosition();
        $templateLines = count($this -> getHtaccessTemplate());

        // Richtigen Inhalt zwischenspeichern
        $newFile = [];
        $handle = @fopen($this -> getHtaccessPath(), "r");
        if ($handle) {
            $i = 0;
            while (!feof($handle))
            {
                $buffer = fgets($handle);
                if($i < $position || $i > $position + $templateLines - 1) {
                    $newFile[] = $buffer;
                }
                $i++;
            }
            fclose($handle);
        }

        // Richtigen Inhalt in Datei schreiben
        $handle = @fopen($this -> getHtaccessPath(), "w");
        foreach($newFile as $line) {
            fwrite($handle, $line);
        }
    }

    /**
     * Gibt zurück, ob die .htaccess-Datei existiert.
     * @return bool
     */
    private function htaccessExists() : bool {
        return file_exists($this -> getHtaccessPath());
    }

    /**
     * Gibt den möglichen Pfad einer .htaccess Datei zurück.
     * @return string
     */
    private function getHtaccessPath() : string {
        return Environment::getPublicPath()."/".$this -> resourceStorage -> getRootLevelFolder() -> getPublicUrl().".htaccess";
    }

    /**
     * Gibt das Template für den Verzeichnisschutz zurück.
     * @return array
     */
    private function getHtaccessTemplate() : array {
        $templatePath = Environment::getPublicPath()."/typo3conf/ext/fp_fileprotector/Resources/Private/htaccess.txt";
        $lines = [];
        $handle = @fopen($templatePath, "r");
        if ($handle)
        {
            while (!feof($handle))
            {
                $lines[] = trim(fgets($handle));
            }
            fclose($handle);
        }
        return $lines;
    }

    private function getHtaccessPosition() : int {
        if(!$this -> htaccessExists()) {
            return -1;
        }

        $template = $this -> getHtaccessTemplate();
        $templateLine = -1;
        $firstLine = -1;

        $handle = @fopen($this -> getHtaccessPath(), "r");
        if ($handle)
        {
            $i = 0;
            while (!feof($handle))
            {
                $buffer = trim(fgets($handle));

                // Alle Zeilen wurden eingelesen
                if($templateLine >= count($template)) {
                    return $firstLine;

                }
                // Wir befinden uns mitten in einer begutachtung
                if($templateLine >= 0) {
                    if($buffer == $template[$templateLine]) {
                        $templateLine++;
                    } else {
                        $templateLine = -1;
                        $firstLine = -1;
                    }
                }
                // Ist diese Zeile der Start?
                if($templateLine < 0 && $buffer == $template[0]) {
                    $firstLine = $i;
                    $templateLine = 1;
                }

                $i++;
            }
            fclose($handle);
        }

        return $firstLine;
    }
}