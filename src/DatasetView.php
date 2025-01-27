<?php

declare(strict_types=1);

namespace Stepapo\Dataset;

use Stepapo\Data\View;
use Stepapo\Utils\Attribute\KeyProperty;
use Stepapo\Utils\Config;


class DatasetView extends Config implements View
{
	public const array DEFAULT_VIEW = self::VIEWS['table'];
	public const array VIEWS = [
		'table' => [
			'name' => 'table',
			'label' => 'Tabulka',
			'datasetTemplate' => __DIR__ . '/Control/Dataset/table.latte',
			'itemListTemplate' => __DIR__ . '/Control/ItemList/table.latte',
			'itemTemplate' => __DIR__ . '/Control/Item/table.latte',
			'attributeTemplate' => __DIR__ . '/Control/Attribute/table.latte',
			'valueTemplate' => __DIR__ . '/../../data/src/Control/Value/value.latte',
			'filterListTemplate' => __DIR__ . '/../../data/src/Control/FilterList/list.latte',
			'filterTemplate' => __DIR__ . '/../../data/src/Control/Filter/list.latte',
			'paginationTemplate' => __DIR__ . '/Control/Pagination/list.latte',
			'sortingTemplate' => __DIR__ . '/Control/Sorting/table.latte',
			'displayTemplate' => __DIR__ . '/Control/Display/list.latte',
			'searchTemplate' => __DIR__ . '/Control/SearchForm/list.latte',
		],
		'list' => [
			'name' => 'list',
			'label' => 'Seznam',
			'datasetTemplate' => __DIR__ . '/Control/Dataset/list.latte',
			'itemListTemplate' => __DIR__ . '/Control/ItemList/list.latte',
			'itemTemplate' => __DIR__ . '/Control/Item/list.latte',
			'attributeTemplate' => __DIR__ . '/Control/Attribute/list.latte',
			'valueTemplate' => __DIR__ . '/../../data/src/Control/Value/value.latte',
			'filterListTemplate' => __DIR__ . '/../../data/src/Control/FilterList/list.latte',
			'filterTemplate' => __DIR__ . '/../../data/src/Control/Filter/list.latte',
			'paginationTemplate' => __DIR__ . '/Control/Pagination/list.latte',
			'sortingTemplate' => __DIR__ . '/Control/Sorting/list.latte',
			'displayTemplate' => __DIR__ . '/Control/Display/list.latte',
			'searchTemplate' => __DIR__ . '/Control/SearchForm/list.latte',
		],
		'grid' => [
			'name' => 'grid',
			'label' => 'Mřížka',
			'datasetTemplate' => __DIR__ . '/Control/Dataset/list.latte',
			'itemListTemplate' => __DIR__ . '/Control/ItemList/grid.latte',
			'itemTemplate' => __DIR__ . '/Control/Item/grid.latte',
			'attributeTemplate' => __DIR__ . '/Control/Attribute/list.latte',
			'valueTemplate' => __DIR__ . '/../../data/src/Control/Value/value.latte',
			'filterListTemplate' => __DIR__ . '/../../data/src/Control/FilterList/list.latte',
			'filterTemplate' => __DIR__ . '/../../data/src/Control/Filter/list.latte',
			'paginationTemplate' => __DIR__ . '/Control/Pagination/list.latte',
			'sortingTemplate' => __DIR__ . '/Control/Sorting/list.latte',
			'displayTemplate' => __DIR__ . '/Control/Display/list.latte',
			'searchTemplate' => __DIR__ . '/Control/SearchForm/list.latte',
		],
	];

	#[KeyProperty] public string $name;
	public ?string $label = null;
	public string $datasetTemplate = self::VIEWS['list']['datasetTemplate'];
	public string $itemListTemplate = self::VIEWS['list']['itemListTemplate'];
	public string $itemTemplate = self::VIEWS['list']['itemTemplate'];
	public string $attributeTemplate = self::VIEWS['list']['attributeTemplate'];
	public string $valueTemplate = self::VIEWS['list']['valueTemplate'];
	public string $filterListTemplate = self::VIEWS['list']['filterListTemplate'];
	public string $filterTemplate = self::VIEWS['list']['filterTemplate'];
	public string $paginationTemplate = self::VIEWS['list']['paginationTemplate'];
	public string $sortingTemplate = self::VIEWS['list']['sortingTemplate'];
	public string $displayTemplate = self::VIEWS['list']['displayTemplate'];
	public string $searchTemplate = self::VIEWS['list']['searchTemplate'];
	public ?\Closure $itemFactoryCallback = null;
	public bool $isDefault = false;
	public bool $hide = false;


	public static function createFromArray(mixed $config = [], mixed $key = null, bool $skipDefaults = false, mixed $parentKey = null): static
	{
		return parent::createFromArray(array_merge(self::VIEWS[$key], (array) $config), $key, $skipDefaults, $parentKey);
	}
}
