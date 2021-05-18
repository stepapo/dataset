<?php

declare(strict_types=1);

namespace Stepapo\Dataset;

use Nextras\Orm\Collection\ICollection;


class Text
{
    public function __construct(
        public string $search = 'Hledat',
        public string $sort = 'Seřadit',
        public string $display = 'Zobrazit',
        public string $previous = 'Předchozí',
        public string $next = 'Další',
        public string $noResults = 'Nic nenalezeno.',
        public string $searchResults = 'Výsledky vyhledávání podle výrazu',
        public string $didYouMean = 'Mysleli jste',
    ) {}


    public static function createFromArray(?array $config): Text
    {
        $text = new self();
        if (array_key_exists('search', $config)) {
            $text->setSearch($config['search']);
        }
        if (array_key_exists('sort', $config)) {
            $text->setSort($config['search']);
        }
        if (array_key_exists('display', $config)) {
            $text->setDisplay($config['display']);
        }
        if (array_key_exists('previous', $config)) {
            $text->setPrevious($config['previous']);
        }
        if (array_key_exists('next', $config)) {
            $text->setNext($config['next']);
        }
        if (array_key_exists('noResults', $config)) {
            $text->setNoResults($config['noResults']);
        }
        if (array_key_exists('searchResults', $config)) {
            $text->setSearchResults($config['searchResults']);
        }
        if (array_key_exists('didYouMean', $config)) {
            $text->setDidYouMean($config['didYouMean']);
        }
        return $text;
    }


    public function setSearch(string $search): Text
    {
        $this->search = $search;
        return $this;
    }


    public function setSort(string $sort): Text
    {
        $this->sort = $sort;
        return $this;
    }


    public function setDisplay(string $display): Text
    {
        $this->display = $display;
        return $this;
    }


    public function setPrevious(string $previous): Text
    {
        $this->previous = $previous;
        return $this;
    }


    public function setNext(string $next): Text
    {
        $this->next = $next;
        return $this;
    }


    public function setNoResults(string $noResults): Text
    {
        $this->noResults = $noResults;
        return $this;
    }


    public function setSearchResults(string $searchResults): Text
    {
        $this->searchResults = $searchResults;
        return $this;
    }


    public function setDidYouMean(string $didYouMean): Text
    {
        $this->didYouMean = $didYouMean;
        return $this;
    }
}
