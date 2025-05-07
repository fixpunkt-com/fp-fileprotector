<?php

declare(strict_types=1);
namespace Fixpunkt\FpFileprotector\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class FrontendUserGroup extends AbstractEntity
{
    /**
     * Title
     *
     * @var string
     */
    #[Extbase\Validate(['validator' => 'NotEmpty'])]
    #[Extbase\Validate(['validator' => 'StringLength', 'options' => ['maximum' => 50]])]
    protected ?string $title;

    /**
     * Description
     *
     * @var string|null
     */
    protected ?string $description;

    /**
     * Subgroups
     *
     * @var null|ObjectStorage<FrontendUserGroup>
     */
    protected ?ObjectStorage $subgroups;


    /**
     * @param ObjectStorage|null $subgroups
     */
    public function setSubgroups(?ObjectStorage $subgroups): void
    {
        $this->subgroups = $subgroups;
    }

    /**
     * @return ObjectStorage|null
     */
    public function getSubgroups(): ?ObjectStorage
    {
        return $this->subgroups;
    }

    public function initializeObject()
    {
        $this->subgroups = new ObjectStorage();
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }


}
