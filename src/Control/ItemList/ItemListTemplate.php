<?php

declare(strict_types=1);

namespace Stepapo\Dataset\Control\ItemList;

use Nextras\Orm\Entity\IEntity;
use Stepapo\Data\Control\DataTemplate;
use Stepapo\Data\Control\MainComponent;


class ItemListTemplate extends DataTemplate
{
	public ItemListControl $control;
	/** @var IEntity[] */ public array $items;
	public string $idColumnName;
	public ?string $itemListClass;
	public MainComponent $main;
	public ?array $columns;
}
