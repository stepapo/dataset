<?php

declare(strict_types=1);

namespace Stepapo\Dataset\Control\ItemList;

use Nextras\Orm\Entity\IEntity;
use Stepapo\Dataset\Control\BaseTemplate;


class ItemListTemplate extends BaseTemplate
{
	public ItemListControl $control;

	/** @var IEntity[] */
	public array $items;

	public string $idColumnName;

	public ?string $itemListClass;
}
