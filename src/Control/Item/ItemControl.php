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
	/** @var \Closure[] */ public array $onChange;
	/** @var \Closure[] */ public array $onRemove;


	public function __construct(
		private DatasetControl $main,
		private IEntity $entity,
		private array $columns,
		private ?\Closure $itemClassCallback,
		private ?\Closure $itemLinkCallback,
	) {}


	public function render(): void
	{
		$this->template->itemClassCallback = $this->itemClassCallback;
		$this->template->itemLinkCallback = $this->itemLinkCallback;
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


	private function getLinkArgs(array $args): array
	{
		$linkArgs = [];
		foreach ($args as $key => $value) {
			if (is_array($value)) {
				$linkArgs[$key] = $this->getLinkArgs($value);
			} else {
				$linkArgs[$key] = $this->getValue($value) === self::UNDEFINED_VALUE ? $value : $this->getValue($value);
			}
		}
		return $linkArgs;
	}
}
