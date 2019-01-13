<?php

namespace SveWap\A21glossary\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Glossary extends AbstractEntity
{
    /**
     * @var string
     */
    protected $short;

    /**
     * @var string
     */
    protected $shortcut;

    /**
     * @var string
     */
    protected $longversion;

    /**
     * @var string
     */
    protected $shorttype;

    /**
     * @var string
     */
    protected $language;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $link;

    /**
     * @return string
     */
    public function getShort(): string
    {
        return $this->short;
    }

    /**
     * @return string
     */
    public function getShortcut(): string
    {
        return $this->shortcut;
    }

    /**
     * @return string
     */
    public function getLongversion(): string
    {
        return $this->longversion;
    }

    /**
     * @return string
     */
    public function getShorttype(): string
    {
        return $this->shorttype;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @param string $short
     */
    public function setShort(string $short): void
    {
        $this->short = $short;
    }

    /**
     * @param string $shortcut
     */
    public function setShortcut(string $shortcut): void
    {
        $this->shortcut = $shortcut;
    }

    /**
     * @param string $longversion
     */
    public function setLongversion(string $longversion): void
    {
        $this->longversion = $longversion;
    }

    /**
     * @param string $shorttype
     */
    public function setShorttype(string $shorttype): void
    {
        $this->shorttype = $shorttype;
    }

    /**
     * @param string $language
     */
    public function setLanguage(string $language): void
    {
        $this->language = $language;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @param string $link
     */
    public function setLink(string $link): void
    {
        $this->link = $link;
    }
}
