<?php
namespace Fixpunkt\FpFileprotector\Middleware;

use Fixpunkt\FpFileprotector\Domain\Repository\ProtectionRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Http\Stream;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AccessMiddleware implements MiddlewareInterface {
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        // Wenn die Anfrage nicht aus einem geschützten FileAdmin kommt weitermachen
        if(
            !key_exists("tx_fpfileprotector", $request -> getQueryParams()) ||
            !key_exists("check", $request -> getQueryParams()["tx_fpfileprotector"]) ||
            !$request -> getQueryParams()["tx_fpfileprotector"]["check"]) {

            return $handler->handle($request);
        }

        // Ansonsten Zugriffsberechtigung prüfen
        $path = $request -> getUri() -> getPath();
        $pathParts = explode("/", $path);
        array_shift($pathParts);

        // Einzelne Bestandteile ermitteln
        $storageIdentifier = array_shift($pathParts);
        $fileName = array_pop($pathParts);
        $filePath = implode("/", $pathParts);

        // Storage ermitteln
        $storage = $this -> getStorage($storageIdentifier);
        if(!$storage) {
            return $this -> createError("Der Storage konnte nicht gefunden werden.");
        }
        $protected = $storage -> getStorageRecord()["protected"];
        $protectedByDefault = $storage -> getStorageRecord()["protected_by_default"];

        // Wenn der Storage nicht geschützt ist, dann Datei ausgeben.
        if(!$protected) {
            // ToDo: Logausgabe, dass ungeschütztes Verzeichnis htaccess enthält.
            return $this -> releaseFile($storage, $filePath."/".$fileName);
        }

        // Ordner ermitteln
        $folder = $this -> getFolder($storage, $filePath);
        if(!$folder) {
            return $this -> createError("Der Speicherort konnte nicht gefunden werden.");
        }

        // Zugriffsberechtigungen ermitteln
        $protection = ProtectionRepository::getProtectionStatic($folder);
        if(!$protection && !$protectedByDefault || $protection && $protection -> isGranted()) {
            return $this -> releaseFile($storage, $filePath."/".$fileName);
        }
        return $this -> createError("Keine Zugriffsberechtigung.", 500);
    }

    /**
     * Gibt den passenden Storage zurück.
     * @param string $identifier
     * @return ResourceStorage|null
     */
    private function getStorage(string $identifier) : ?ResourceStorage {
        /** @var StorageRepository $storageRepository */
        $storageRepository = GeneralUtility::makeInstance(StorageRepository::class);
        foreach($storageRepository->findAll() as $storage) {
            $basePath = $storage -> getConfiguration()["basePath"];
            if($basePath == $identifier."/") {
                return $storage;
            }
        };
        return null;
    }

    /**
     * Ermittelt den Ordner, in dem die Datei liegt.
     * @param ResourceStorage $storage
     * @param string $path
     * @return Folder|null
     * @throws \TYPO3\CMS\Core\Resource\Exception\InsufficientFolderAccessPermissionsException
     */
    private function getFolder(ResourceStorage $storage, string $path) : ?Folder {
        return $storage -> getFolder($path);
    }

    /**
     * Gibt die gesuchte Datei zurück.
     * @param ResourceStorage $storage
     * @param string $fileIdentifier
     * @return \TYPO3\CMS\Core\Resource\FileInterface
     */
    private function getFile(ResourceStorage $storage, string $fileIdentifier) {
        return $storage -> getFile($fileIdentifier);
    }

    /**
     * Gibt eine Fehlermeldung zurück.
     * @param string $reason
     * @param int $status
     * @return Response
     */
    private function createError(string $reason, int $status = 404) : Response {
        $body = new Stream('php://temp', 'rw');
        $body->write($reason);
        return (new Response())
            ->withBody($body)
            ->withStatus($status);
    }

    /**
     * Gibt die Datei aus.
     * @return void
     */
    private function releaseFile(ResourceStorage $storage, string $fileIdentifier) : Response {
        $file = $this -> getFile($storage, $fileIdentifier);
        if(!$file) {
            return $this -> createError("Die angegebene Datei konnte nicht gefunden werden.");
        }

        $body = new Stream('php://temp', 'rw');
        $body->write($file -> getContents());
        return (new Response())
            ->withHeader('Content-Type', $file -> getMimeType())
            ->withBody($body);
    }
}