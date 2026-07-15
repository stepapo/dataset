<?php

declare(strict_types=1);

namespace Stepapo\Dataset\Control\Item;

use Nette\Application\IPresenter;
use Nextras\Orm\Entity\IEntity;
use Stepapo\Data\Control\DataTemplate;
use Stepapo\Data\Control\MainComponent;


class ItemTemplate extends DataTemplate
{
	public MainComponent $main;
	public ItemControl $control;
	public IEntity $item;
	/** @var \Closure(IEntity): string|null */ public ?\Closure $itemClassCallback;
	/** @var \Closure(IEntity, IPresenter): string|null */ public ?\Closure $itemLinkCallback;
	public ?array $linkArgs;
	public array $columns;
	/** @var \Closure(string, array): mixed */ public \Closure $invokeFilter;
}
