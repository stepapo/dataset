<?php

declare(strict_types=1);

namespace Stepapo\Dataset\UI\ItemList;

use Nextras\Orm\Entity\IEntity;
use Stepapo\Dataset\UI\DatasetControlTemplate;


class ItemListTemplate extends DatasetControlTemplate
{
	public ItemListControl $control;

	/** @var IEntity[] */
	public array $items;

	public string $idColumnName;
}
