<?php

declare(strict_types=1);

namespace Stepapo\Dataset\Control\Item;

use Nextras\Orm\Entity\IEntity;
use Stepapo\Data\Control\DataTemplate;
use Stepapo\Data\Control\MainComponent;
use Stepapo\Data\Link;


class ItemTemplate extends DataTemplate
{
	public MainComponent $main;

	public ItemControl $control;

	public IEntity $item;

	/** @var callable|null */
	public $itemClassCallback;

	public ?Link $itemLink;

	public ?array $linkArgs;

	public array $columns;
}
