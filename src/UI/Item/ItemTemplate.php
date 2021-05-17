<?php

declare(strict_types=1);

namespace Stepapo\Dataset\UI\Item;

use Stepapo\Dataset\UI\DatasetControlTemplate;
use Nextras\Orm\Entity\IEntity;


class ItemTemplate extends DatasetControlTemplate
{
	public ItemControl $control;

	public IEntity $item;

	/** @var callable|null */
	public $itemClassCallback;
}
