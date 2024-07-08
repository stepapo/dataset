<?php

declare(strict_types=1);

namespace Stepapo\Dataset\Control\Item;

use Nextras\Orm\Entity\IEntity;
use Stepapo\Dataset\Control\BaseTemplate;
use Stepapo\Dataset\Link;


class ItemTemplate extends BaseTemplate
{
	public ItemControl $control;

	public IEntity $item;

	/** @var callable|null */
	public $itemClassCallback;

	public ?Link $itemLink;

	public ?array $linkArgs;
}
