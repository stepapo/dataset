<?php

declare(strict_types=1);

namespace Stepapo\Data\UI\Dataset;

use Stepapo\Data\Column;
use Stepapo\Data\Factory;
use Stepapo\Data\UI\Dataset\Attribute\SimpleAttribute;
use Stepapo\Data\UI\Dataset\Attribute\Attribute;
use Stepapo\Data\UI\Dataset\Display\Display;
use Stepapo\Data\UI\Dataset\Display\SimpleDisplay;
use Stepapo\Data\UI\Dataset\Filter\Filter;
use Stepapo\Data\UI\Dataset\Filter\SimpleFilter;
use Stepapo\Data\UI\Dataset\Filtering\Filtering;
use Stepapo\Data\UI\Dataset\Filtering\SimpleFiltering;
use Stepapo\Data\UI\Dataset\Item\Item;
use Stepapo\Data\UI\Dataset\Item\SimpleItem;
use Stepapo\Data\UI\Dataset\ItemList\ItemList;
use Stepapo\Data\UI\Dataset\ItemList\SimpleItemList;
use Stepapo\Data\UI\Dataset\Pagination\Pagination;
use Stepapo\Data\UI\Dataset\Pagination\SimplePagination;
use Stepapo\Data\UI\Dataset\SearchForm\SearchForm;
use Stepapo\Data\UI\Dataset\SearchForm\SimpleSearchForm;
use Stepapo\Data\UI\Dataset\Sorting\SimpleSorting;
use Stepapo\Data\UI\Dataset\Sorting\Sorting;
use Stepapo\Data\UI\Dataset\Value\SimpleValue;
use Stepapo\Data\UI\Dataset\Value\Value;
use Nette\Utils\Paginator;
use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Entity\IEntity;


class DatasetFactory implements Factory
{
    public string $itemListClass;

    public string $itemClass;

    public string $attributeClass;

    public string $valueClass;

    public string $filteringClass;

    public string $filterClass;

    public string $paginationClass;

    public string $sortingClass;

    public string $displayClass;
    
    public string $searchFormClass;


    public function __construct(
        string $itemListClass = SimpleItemList::class,
        string $itemClass = SimpleItem::class,
        string $attributeClass = SimpleAttribute::class,
        string $valueClass = SimpleValue::class,
        string $filteringClass = SimpleFiltering::class,
        string $filterClass = SimpleFilter::class,
        string $paginationClass = SimplePagination::class,
        string $sortingClass = SimpleSorting::class,
        string $displayClass = SimpleDisplay::class,
        string $searchFormClass = SimpleSearchForm::class
    ) {
        $this->itemListClass = $itemListClass;
        $this->itemClass = $itemClass;
        $this->attributeClass = $attributeClass;
        $this->valueClass = $valueClass;
        $this->filteringClass = $filteringClass;
        $this->filterClass = $filterClass;
        $this->paginationClass = $paginationClass;
        $this->sortingClass = $sortingClass;
        $this->displayClass = $displayClass;
        $this->searchFormClass = $searchFormClass;
    }


    public static function createFromArray(array $config): DatasetFactory
    {
        $factory = new self();
        if (isset($config['itemListClass'])) {
            $factory->setItemListClass($config['itemListClass']);
        }
        if (isset($config['itemClass'])) {
            $factory->setItemClass($config['itemClass']);
        }
        if (isset($config['attributeClass'])) {
            $factory->setAttributeClass($config['attributeClass']);
        }
        if (isset($config['valueClass'])) {
            $factory->setValueClass($config['valueClass']);
        }
        if (isset($config['filteringClass'])) {
            $factory->setFilteringClass($config['filteringClass']);
        }
        if (isset($config['filterClass'])) {
            $factory->setFilterClass($config['filterClass']);
        }
        if (isset($config['paginationClass'])) {
            $factory->setPaginationClass($config['paginationClass']);
        }
        if (isset($config['sortingClass'])) {
            $factory->setSortingClass($config['sortingClass']);
        }
        if (isset($config['displayClass'])) {
            $factory->setDisplayClass($config['displayClass']);
        }
        if (isset($config['searchFormClass'])) {
            $factory->setSearchFormClass($config['searchFormClass']);
        }
        return $factory;
    }


    public static function createDefault(): DatasetFactory
    {
        return new self();
    }


    public function createItemList(ICollection $items): ItemList
    {
        return new $this->itemListClass($items);
    }


    public function createItem(IEntity $entity): Item
    {
        return new $this->itemClass($entity);
    }


    public function createAttribute(IEntity $entity, Column $column): Attribute
    {
        return new $this->attributeClass($entity, $column);
    }


    public function createValue(IEntity $entity, Column $column): Value
    {
        return new $this->valueClass($entity, $column);
    }


    public function createFiltering(): Filtering
    {
        return new $this->filteringClass();
    }


    public function createFilter(Column $column): Filter
    {
        return new $this->filterClass($column);
    }


    public function createPagination(Paginator $paginator): Pagination
    {
        return new $this->paginationClass($paginator);
    }


    public function createSorting(): Sorting
    {
        return new $this->sortingClass();
    }


    public function createDisplay(): Display
    {
        return new $this->displayClass();
    }


    public function createSearchForm(?string $placeholder = null): SearchForm
    {
        return new $this->searchFormClass($placeholder);
    }


    public function setItemListClass(string $itemListClass): DatasetFactory
    {
        $this->itemListClass = $itemListClass;
        return $this;
    }


    public function setItemClass(string $itemClass): DatasetFactory
    {
        $this->itemClass = $itemClass;
        return $this;
    }


    public function setAttributeClass(string $attributeClass): DatasetFactory
    {
        $this->attributeClass = $attributeClass;
        return $this;
    }


    public function setValueClass(string $valueClass): DatasetFactory
    {
        $this->valueClass = $valueClass;
        return $this;
    }


    public function setFilteringClass(string $filteringClass): DatasetFactory
    {
        $this->filteringClass = $filteringClass;
        return $this;
    }


    public function setFilterClass(string $filterClass): DatasetFactory
    {
        $this->filterClass = $filterClass;
        return $this;
    }


    public function setPaginationClass(string $paginationClass): DatasetFactory
    {
        $this->paginationClass = $paginationClass;
        return $this;
    }


    public function setSortingClass(string $sortingClass): DatasetFactory
    {
        $this->sortingClass = $sortingClass;
        return $this;
    }


    public function setDisplayClass(string $displayClass): DatasetFactory
    {
        $this->displayClass = $displayClass;
        return $this;
    }


    public function setSearchFormClass(string $searchFormClass): DatasetFactory
    {
        $this->searchFormClass = $searchFormClass;
        return $this;
    }
}
