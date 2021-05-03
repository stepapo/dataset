<?php

namespace Stepapo\Data\UI\Dataset\Item;

use Stepapo\Data\Button;
use Stepapo\Data\Part;
use Stepapo\Data\UI\Dataset\DatasetControlTemplate;
use Nextras\Orm\Entity\IEntity;


class ItemTemplate extends DatasetControlTemplate
{
	public IEntity $item;

	/** @var Button[]|null  */
	public ?array $buttons;

	/** @var Part[]|null  */
	public ?array $parts;

	/** @var callable|null */
	public $itemClassCallback;
}
