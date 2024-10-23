<?php

declare(strict_types=1);
namespace Fixpunkt\FpFileprotector\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class FrontendUserGroup extends AbstractEntity
{
    /**
     * Title
     *
     * @var string
     * @Extbase\Validate("NotEmpty")
     * @Extbase\Validate("StringLength", options={"maximum": 50})
     */
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
     * @var ObjectStorage<FrontendUserGroup>
     */
    protected ?ObjectStorage $subgroups;

    public function initializeObject()
    {

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

    public function getSubgroup(): ?ObjectStorage
    {
        return $this->subgroups;
    }

    public function setSubgroup(?ObjectStorage $subgroups): void
    {
        $this->subgroups = $subgroups;
    }



}
