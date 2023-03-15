<?php

namespace Fixpunkt\FpFileprotector\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;

class StartController extends ActionController {
    /**
     * Je nachdem ob das Module am Start aufgerufen wird oder ob ein Ordner im Ordnerbaum ausgewählt wird, auf die richtige Action weiterleiten.
     * @return void
     * @throws StopActionException
     */
    public function startAction() : void {
        if(key_exists("id", $_GET) && $_GET["id"]) {
            // Irgendein Ordner ist ausgewählt
            $this -> redirect('show', 'Folder', null, ['combinedIdentifier' => $_GET["id"]]);
        } else {
            // Kein Ordner ist ausgewählt
            $this -> redirect('list', 'FileStorage');
        }
    }
}