<?php

declare(strict_types=1);

namespace Stepapo\Dataset\Control\Item;

use Stepapo\Dataset\Link;
use Stepapo\Dataset\Control\BaseTemplate;
use Nextras\Orm\Entity\IEntity;


class ItemTemplate extends BaseTemplate
{
	public ItemControl $control;

	public IEntity $item;

	/** @var callable|null */
	public $itemClassCallback;

	public ?Link $itemLink;

	public ?array $linkArgs;
}
