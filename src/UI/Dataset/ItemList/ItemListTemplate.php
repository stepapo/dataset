<?php

declare(strict_types=1);

namespace Stepapo\Data\UI\Dataset\ItemList;

use Nextras\Orm\Entity\IEntity;
use Stepapo\Data\UI\Dataset\DatasetControlTemplate;


class ItemListTemplate extends DatasetControlTemplate
{
	/** @var IEntity[] */
	public array $items;
}
