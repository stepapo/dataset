<?php

declare(strict_types=1);

namespace Stepapo\Dataset;

use Nette\InvalidArgumentException;


class View
{
	public const DEFAULT_VIEW = self::VIEWS['table'];

	public const VIEWS = [
		'table' => [
			'name' => 'table',
			'label' => 'Tabulka',
			'datasetTemplate' => __DIR__ . '/UI/Dataset/table.latte',
			'itemListTemplate' => __DIR__ . '/UI/ItemList/table.latte',
			'itemTemplate' => __DIR__ . '/UI/Item/table.latte',
			'attributeTemplate' => __DIR__ . '/UI/Attribute/table.latte',
			'valueTemplate' => __DIR__ . '/UI/Value/value.latte',
			'filterListTemplate' => __DIR__ . '/UI/FilterList/list.latte',
			'filterTemplate' => __DIR__ . '/UI/Filter/list.latte',
			'paginationTemplate' => __DIR__ . '/UI/Pagination/list.latte',
			'sortingTemplate' => __DIR__ . '/UI/Sorting/table.latte',
			'displayTemplate' => __DIR__ . '/UI/Display/list.latte',
			'searchTemplate' => __DIR__ . '/UI/SearchForm/list.latte',
		],
		'list' => [
			'name' => 'list',
			'label' => 'Seznam',
			'datasetTemplate' => __DIR__ . '/UI/Dataset/list.latte',
			'itemListTemplate' => __DIR__ . '/UI/ItemList/list.latte',
			'itemTemplate' => __DIR__ . '/UI/Item/list.latte',
			'attributeTemplate' => __DIR__ . '/UI/Attribute/list.latte',
			'valueTemplate' => __DIR__ . '/UI/Value/value.latte',
			'filterListTemplate' => __DIR__ . '/UI/FilterList/list.latte',
			'filterTemplate' => __DIR__ . '/UI/Filter/list.latte',
			'paginationTemplate' => __DIR__ . '/UI/Pagination/list.latte',
			'sortingTemplate' => __DIR__ . '/UI/Sorting/list.latte',
			'displayTemplate' => __DIR__ . '/UI/Display/list.latte',
			'searchTemplate' => __DIR__ . '/UI/SearchForm/list.latte',
		],
		'grid' => [
			'name' => 'grid',
			'label' => 'Mřížka',
			'datasetTemplate' => __DIR__ . '/UI/Dataset/list.latte',
			'itemListTemplate' => __DIR__ . '/UI/ItemList/grid.latte',
			'itemTemplate' => __DIR__ . '/UI/Item/grid.latte',
			'attributeTemplate' => __DIR__ . '/UI/Attribute/list.latte',
			'valueTemplate' => __DIR__ . '/UI/Value/value.latte',
			'filterListTemplate' => __DIR__ . '/UI/FilterList/list.latte',
			'filterTemplate' => __DIR__ . '/UI/Filter/list.latte',
			'paginationTemplate' => __DIR__ . '/UI/Pagination/list.latte',
			'sortingTemplate' => __DIR__ . '/UI/Sorting/list.latte',
			'displayTemplate' => __DIR__ . '/UI/Display/list.latte',
			'searchTemplate' => __DIR__ . '/UI/SearchForm/list.latte',
		],
	];


	public function __construct(
		public string $name,
		public ?string $label = null,
		public string $datasetTemplate = self::VIEWS['list']['datasetTemplate'],
		public string $itemListTemplate = self::VIEWS['list']['itemListTemplate'],
		public string $itemTemplate = self::VIEWS['list']['itemTemplate'],
		public string $attributeTemplate = self::VIEWS['list']['attributeTemplate'],
		public string $valueTemplate = self::VIEWS['list']['valueTemplate'],
		public string $filterListTemplate = self::VIEWS['list']['filterListTemplate'],
		public string $filterTemplate = self::VIEWS['list']['filterTemplate'],
		public string $paginationTemplate = self::VIEWS['list']['paginationTemplate'],
		public string $sortingTemplate = self::VIEWS['list']['sortingTemplate'],
		public string $displayTemplate = self::VIEWS['list']['displayTemplate'],
		public string $searchTemplate = self::VIEWS['list']['searchTemplate'],
		public $itemFactoryCallback = null,
		public bool $isDefault = false
	) {}


	public static function createFromArray(array $config, string $name): View
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
		if (isset($config['filterListTemplate'])) {
			$view->setFilterListTemplate($config['filterListTemplate']);
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
		if (array_key_exists('itemFactoryCallback', $config)) {
			$view->setItemFactoryCallback($config['itemFactoryCallback']);
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


	public function setName(string $name): View
	{
		$this->name = $name;
		return $this;
	}


	public function setLabel(string $label): View
	{
		$this->label = $label;
		return $this;
	}


	public function setDatasetTemplate(string $datasetTemplate): View
	{
		$this->datasetTemplate = $datasetTemplate;
		return $this;
	}


	public function setItemListTemplate(string $itemListTemplate): View
	{
		$this->itemListTemplate = $itemListTemplate;
		return $this;
	}


	public function setItemTemplate(string $itemTemplate): View
	{
		$this->itemTemplate = $itemTemplate;
		return $this;
	}


	public function setAttributeTemplate(string $attributeTemplate): View
	{
		$this->attributeTemplate = $attributeTemplate;
		return $this;
	}


	public function setValueTemplate(string $valueTemplate): View
	{
		$this->valueTemplate = $valueTemplate;
		return $this;
	}


	public function setFilterListTemplate(string $filterListTemplate): View
	{
		$this->filterListTemplate = $filterListTemplate;
		return $this;
	}


	public function setFilterTemplate(string $filterTemplate): View
	{
		$this->filterTemplate = $filterTemplate;
		return $this;
	}


	public function setPaginationTemplate(string $paginationTemplate): View
	{
		$this->paginationTemplate = $paginationTemplate;
		return $this;
	}


	public function setSortingTemplate(string $sortingTemplate): View
	{
		$this->sortingTemplate = $sortingTemplate;
		return $this;
	}


	public function setDisplayTemplate(string $displayTemplate): View
	{
		$this->displayTemplate = $displayTemplate;
		return $this;
	}


	public function setSearchTemplate(string $searchTemplate): View
	{
		$this->searchTemplate = $searchTemplate;
		return $this;
	}


	public function setItemFactoryCallback(?callable $itemFactoryCallback): View
	{
		$this->itemFactoryCallback = $itemFactoryCallback;
		return $this;
	}


	public function setIsDefault(bool $isDefault): View
	{
		$this->isDefault = $isDefault;
		return $this;
	}


	public static function createDefault(): View
	{
		return new self(...array_merge(array_values(self::DEFAULT_VIEW), [null, true]));
	}


	public static function createView(string $name, bool $isDefault = false): View
	{
		if (!isset(self::VIEWS[$name])) {
			throw new InvalidArgumentException();
		}
		return new self(...array_merge(array_values(self::VIEWS[$name]), [null, $isDefault]));
	}
}
