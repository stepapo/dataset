<?php

declare(strict_types=1);

namespace Stepapo\Data\UI\Dataset;

use Nette\InvalidArgumentException;


class DatasetView implements View
{
	public const DEFAULT_VIEW = self::VIEWS['table'];

	public const VIEWS = [
		'table' => [
			'name' => 'table',
			'label' => 'Tabulka',
			'datasetTemplate' => __DIR__ . '/Dataset/table.latte',
			'itemListTemplate' => __DIR__ . '/ItemList/table.latte',
			'itemTemplate' => __DIR__ . '/Item/table.latte',
			'attributeTemplate' => __DIR__ . '/Attribute/table.latte',
			'valueTemplate' => __DIR__ . '/Value/value.latte',
			'filteringTemplate' => __DIR__ . '/Filtering/list.latte',
			'filterTemplate' => __DIR__ . '/Filter/list.latte',
			'paginationTemplate' => __DIR__ . '/Pagination/list.latte',
			'sortingTemplate' => __DIR__ . '/Sorting/table.latte',
			'displayTemplate' => __DIR__ . '/Display/list.latte',
			'searchTemplate' => __DIR__ . '/SearchForm/list.latte'
		],
		'list' => [
			'name' => 'list',
			'label' => 'Seznam',
			'datasetTemplate' => __DIR__ . '/Dataset/list.latte',
			'itemListTemplate' => __DIR__ . '/ItemList/list.latte',
			'itemTemplate' => __DIR__ . '/Item/list.latte',
			'attributeTemplate' => __DIR__ . '/Attribute/list.latte',
			'valueTemplate' => __DIR__ . '/Value/value.latte',
			'filteringTemplate' => __DIR__ . '/Filtering/list.latte',
			'filterTemplate' => __DIR__ . '/Filter/list.latte',
			'paginationTemplate' => __DIR__ . '/Pagination/list.latte',
			'sortingTemplate' => __DIR__ . '/Sorting/list.latte',
			'displayTemplate' => __DIR__ . '/Display/list.latte',
			'searchTemplate' => __DIR__ . '/SearchForm/list.latte'
		],
		'grid' => [
			'name' => 'grid',
			'label' => 'Mřížka',
			'datasetTemplate' => __DIR__ . '/Dataset/list.latte',
			'itemListTemplate' => __DIR__ . '/ItemList/grid.latte',
			'itemTemplate' => __DIR__ . '/Item/grid.latte',
			'attributeTemplate' => __DIR__ . '/Attribute/list.latte',
			'valueTemplate' => __DIR__ . '/Value/value.latte',
			'filteringTemplate' => __DIR__ . '/Filtering/list.latte',
			'filterTemplate' => __DIR__ . '/Filter/list.latte',
			'paginationTemplate' => __DIR__ . '/Pagination/list.latte',
			'sortingTemplate' => __DIR__ . '/Sorting/list.latte',
			'displayTemplate' => __DIR__ . '/Display/list.latte',
			'searchTemplate' => __DIR__ . '/SearchForm/list.latte'
		],
	];

	public string $name;

	public ?string $label;

	public ?string $datasetTemplate;

	public ?string $itemListTemplate;

	public ?string $itemTemplate;

	public ?string $attributeTemplate;

	public ?string $valueTemplate;

	public string $filteringTemplate;

	public string $filterTemplate;

	public string $paginationTemplate;

	public string $sortingTemplate;

	public string $displayTemplate;

	public string $searchTemplate;

	public bool $isDefault;


	public function __construct(
		string $name,
		?string $label = null,
		string $datasetTemplate = self::VIEWS['list']['datasetTemplate'],
		string $itemListTemplate = self::VIEWS['list']['itemListTemplate'],
		string $itemTemplate = self::VIEWS['list']['itemTemplate'],
		string $attributeTemplate = self::VIEWS['list']['attributeTemplate'],
		string $valueTemplate = self::VIEWS['list']['valueTemplate'],
		string $filteringTemplate = self::VIEWS['list']['filteringTemplate'],
		string $filterTemplate = self::VIEWS['list']['filterTemplate'],
		string $paginationTemplate = self::VIEWS['list']['paginationTemplate'],
		string $sortingTemplate = self::VIEWS['list']['sortingTemplate'],
		string $displayTemplate = self::VIEWS['list']['displayTemplate'],
		string $searchTemplate = self::VIEWS['list']['searchTemplate'],
		bool $isDefault = false
	) {
		$this->name = $name;
		$this->label = $label;
		$this->datasetTemplate = $datasetTemplate;
		$this->itemListTemplate = $itemListTemplate;
		$this->itemTemplate = $itemTemplate;
		$this->attributeTemplate = $attributeTemplate;
		$this->valueTemplate = $valueTemplate;
		$this->filteringTemplate = $filteringTemplate;
		$this->filterTemplate = $filterTemplate;
		$this->paginationTemplate = $paginationTemplate;
		$this->sortingTemplate = $sortingTemplate;
		$this->displayTemplate = $displayTemplate;
		$this->searchTemplate = $searchTemplate;
		$this->isDefault = $isDefault;
	}


	public static function createFromArray(?array $config = null, string $name): DatasetView
	{
		$view = array_key_exists($name, self::VIEWS) ? self::createView($name) : new self($name);
		if (isset($config['label'])) {
			$view->setLabel($config['label']);
		}
		if (isset($config['datasetTemplate'])) {
			$view->setDatasetTemplate($config['datasetTemplate']);
		}
		if (isset($config['itemListTemplate'])) {
			$view->setItemListTemplate($config['itemListTemplate']);
		}
		if (isset($config['itemTemplate'])) {
			$view->setItemTemplate($config['itemTemplate']);
		}
		if (isset($config['attributeTemplate'])) {
			$view->setAttributeTemplate($config['attributeTemplate']);
		}
		if (isset($config['valueTemplate'])) {
			$view->setAttributeTemplate($config['valueTemplate']);
		}
		if (isset($config['filteringTemplate'])) {
			$view->setFilteringTemplate($config['filteringTemplate']);
		}
		if (isset($config['filterTemplate'])) {
			$view->setFilterTemplate($config['filterTemplate']);
		}
		if (isset($config['paginationTemplate'])) {
			$view->setPaginationTemplate($config['paginationTemplate']);
		}
		if (isset($config['sortingTemplate'])) {
			$view->setSortingTemplate($config['sortingTemplate']);
		}
		if (isset($config['displayTemplate'])) {
			$view->setDisplayTemplate($config['displayTemplate']);
		}
		if (isset($config['searchTemplate'])) {
			$view->setSearchTemplate($config['searchTemplate']);
		}
		if (isset($config['isDefault'])) {
			$view->setIsDefault($config['isDefault']);
		}
		return $view;
	}


	public function isDefault(): bool
	{
		return $this->isDefault;
	}


	public function setName(string $name): DatasetView
	{
		$this->name = $name;
		return $this;
	}


	public function setLabel(string $label): DatasetView
	{
		$this->label = $label;
		return $this;
	}


	public function setDatasetTemplate(?string $datasetTemplate): DatasetView
	{
		$this->datasetTemplate = $datasetTemplate;
		return $this;
	}


	public function setItemListTemplate(?string $itemListTemplate): DatasetView
	{
		$this->itemListTemplate = $itemListTemplate;
		return $this;
	}


	public function setItemTemplate(?string $itemTemplate): DatasetView
	{
		$this->itemTemplate = $itemTemplate;
		return $this;
	}


	public function setAttributeTemplate(?string $attributeTemplate): DatasetView
	{
		$this->attributeTemplate = $attributeTemplate;
		return $this;
	}


	public function setValueTemplate(?string $valueTemplate): DatasetView
	{
		$this->valueTemplate = $valueTemplate;
		return $this;
	}


	public function setFilteringTemplate(string $filteringTemplate): DatasetView
	{
		$this->filteringTemplate = $filteringTemplate;
		return $this;
	}


	public function setFilterTemplate(string $filterTemplate): DatasetView
	{
		$this->filterTemplate = $filterTemplate;
		return $this;
	}


	public function setPaginationTemplate(string $paginationTemplate): DatasetView
	{
		$this->paginationTemplate = $paginationTemplate;
		return $this;
	}


	public function setSortingTemplate(string $sortingTemplate): DatasetView
	{
		$this->sortingTemplate = $sortingTemplate;
		return $this;
	}


	public function setDisplayTemplate(string $displayTemplate): DatasetView
	{
		$this->displayTemplate = $displayTemplate;
		return $this;
	}


	public function setSearchTemplate(string $searchTemplate): DatasetView
	{
		$this->searchTemplate = $searchTemplate;
		return $this;
	}


	public function setIsDefault(bool $isDefault): DatasetView
	{
		$this->isDefault = $isDefault;
		return $this;
	}


	public static function createDefault(bool $isDefault = false): DatasetView
	{
		return new self(...array_merge(array_values(self::DEFAULT_VIEW), [$isDefault]));
	}


	public static function createView(string $name, bool $isDefault = false): DatasetView
	{
		if (!isset(self::VIEWS[$name])) {
			throw new InvalidArgumentException();
		}
		return new self(...array_merge(array_values(self::VIEWS[$name]), [$isDefault]));
	}
}
