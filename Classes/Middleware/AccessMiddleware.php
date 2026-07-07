<?php

declare(strict_types=1);

namespace Fixpunkt\FpFileprotector\Middleware;

use Fixpunkt\FpFileprotector\Domain\Repository\ProtectionRepository;
use Fixpunkt\FpFileprotector\Service\AccessService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Http\Stream;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AccessMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly AccessService $accessService) {}

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $queryParams = $request->getQueryParams();
        if (
            empty($queryParams['tx_fpfileprotector'])
            || empty($queryParams['tx_fpfileprotector']['check'])
        ) {
            return $handler->handle($request);
        }

        $path = $request->getUri()->getPath();
        $pathParts = explode('/', $path);
        // remove first slash so we can get the storage
        array_shift($pathParts);

        $storageIdentifier = (string)array_shift($pathParts);
        $filePath = implode('/', $pathParts);
        // add the slash again, because all identifiers start with a slash
        $filePath = '/' . $filePath;

        $storage = $this->getStorage($storageIdentifier);
        if (!$storage) {
            return $this->createError('The storage could not be found.');
        }
        $protected = $storage->getStorageRecord()['protected'];
        $protectedByDefault = $storage->getStorageRecord()['protected_by_default'];

        try {
            $file = $storage->getFile($filePath);
        } catch (\Exception) {
            $file = null;
        }

        if (
            !$file
            || (
                $file instanceof File
                && (
                    $file->isMissing()
                    || $file->isDeleted()
                )
            )
        ) {
            return $this->createError('The file could not be found.');
        }
        $originalFile = $file;
        if ($originalFile instanceof ProcessedFile) {
            $originalFile = $file->getOriginalFile();
        }

        if (!$protected) {
            return $this->releaseFile($storage, $filePath);
        }

        $folder = $originalFile->getParentFolder();
        if (!$folder) {
            return $this->createError('The storage location could not be found.');
        }

        $protection = ProtectionRepository::getProtectionStatic($folder);
        if ((!$protection && !$protectedByDefault) || ($protection && $this->accessService->isGranted($protection))) {
            return $this->releaseFile($storage, $filePath);
        }
        return $this->createError('You do not have permission to access this file.', 500);
    }

    /**
     * Returns the matching storage.
     *
     * @param string $identifier
     * @return ResourceStorage|null
     */
    private function getStorage(string $identifier): ?ResourceStorage
    {
        /** @var StorageRepository $storageRepository */
        $storageRepository = GeneralUtility::makeInstance(StorageRepository::class);
        foreach ($storageRepository->findAll() as $storage) {
            $basePath = $storage->getConfiguration()['basePath'];
            if ($basePath === $identifier . '/') {
                return $storage;
            }
        }
        return null;
    }

    /**
     * Returns an error response.
     *
     * @param string $reason
     * @param int $status
     * @return Response
     */
    private function createError(string $reason, int $status = 404): Response
    {
        $body = new Stream('php://temp', 'rw');
        $body->write($reason);
        return (new Response())
            ->withBody($body)
            ->withStatus($status);
    }

    /**
     * Releases the file contents.
     *
     * @param ResourceStorage $storage
     * @param string $fileIdentifier
     * @return Response
     */
    private function releaseFile(ResourceStorage $storage, string $fileIdentifier): Response
    {
        $file = $storage->getFile($fileIdentifier);
        if (!$file) {
            return $this->createError('The requested file could not be found.');
        }

        $body = new Stream('php://temp', 'rw');
        $body->write($file->getContents());
        return (new Response())
            ->withHeader('Content-Type', $file->getMimeType())
            ->withBody($body);
    }
}