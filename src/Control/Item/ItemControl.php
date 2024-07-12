<?php

declare(strict_types=1);

namespace Stepapo\Dataset\Control\Item;

use Nette\InvalidArgumentException;
use Nextras\Orm\Entity\IEntity;
use Nextras\Orm\Relationships\HasMany;
use Stepapo\Data\Control\DataControl;
use Stepapo\Data\Link;
use Stepapo\Dataset\Control\Dataset\DatasetControl;


/**
 * @property-read ItemTemplate $template
 * @method onChange(ItemControl $control, IEntity $entity)
 * @method onRemove(ItemControl $control)
 */
class ItemControl extends DataControl
{
	public const string UNDEFINED_VALUE = 'undefined_value';
	/** @var callable[] */ public array $onChange;
	/** @var callable[] */ public array $onRemove;


	public function __construct(
		private DatasetControl $main,
		private IEntity $entity,
		private array $columns,
		private $itemClassCallback,
		private ?Link $itemLink,
	) {}


	public function render(): void
	{
		$this->template->itemClassCallback = $this->itemClassCallback;
		$this->template->itemLink = $this->itemLink;
		$this->template->linkArgs = $this->itemLink && $this->itemLink->args ? array_map(fn($a) => $this->getValue($a) === self::UNDEFINED_VALUE ? $a : $this->getValue($a), $this->itemLink->args) : null;
		$this->template->item = $this->entity;
		$this->template->main = $this->main;
		$this->template->columns = $this->columns;
		$this->template->render($this->main->getView()->itemTemplate);
	}


	public function getValue($columnName)
	{
		$columnNames = explode('.', $columnName);
		$value = $this->entity;
		if ($value instanceof HasMany) {
			throw new InvalidArgumentException("Value is a collection.");
		} else {
			foreach ($columnNames as $columnName) {
				if (!$value?->getMetadata()->hasProperty($columnName)) {
					return self::UNDEFINED_VALUE;
				}
				$value = $value->{$columnName};
			}
		}
		return $value;
	}
}
