<?php

declare(strict_types=1);

namespace Stepapo\Dataset\Control\Item;

use Nette\Application\IPresenter;
use Nette\InvalidArgumentException;
use Nextras\Orm\Entity\IEntity;
use Nextras\Orm\Relationships\HasMany;
use Stepapo\Data\Control\DataControl;
use Stepapo\Dataset\Control\Dataset\DatasetControl;


/**
 * @property-read ItemTemplate $template
 */
class ItemControl extends DataControl
{
	public const string UNDEFINED_VALUE = 'undefined_value';
	/** @var array<callable(static, IEntity): void> */ public array $onChange = [];
	/** @var array<callable(static): void> */ public array $onRemove = [];


	/**
	 * @param \Closure(IEntity): string|null $itemClassCallback
	 * @param \Closure(IEntity, IPresenter): string|null $itemLinkCallback
	 */
	public function __construct(
		private DatasetControl $main,
		private IEntity $entity,
		private array $columns,
		private ?\Closure $itemClassCallback,
		private ?\Closure $itemLinkCallback,
	) {
	}


	public function render(): void
	{
		$this->template->itemClassCallback = $this->itemClassCallback;
		$this->template->itemLinkCallback = $this->itemLinkCallback;
		$this->template->item = $this->entity;
		$this->template->main = $this->main;
		$this->template->columns = $this->columns;
		$this->template->invokeFilter = $this->template->getLatte()->invokeFilter(...);
		$this->template->render($this->main->getView()->itemTemplate);
	}


	public function getValue(string $columnName): mixed
	{
		$columnNames = explode('.', $columnName);
		$value = $this->entity;
		if ($value instanceof HasMany) {
			throw new InvalidArgumentException('Value is a collection.');
		} else {
			foreach ($columnNames as $columnName) {
				if (!$value?->getMetadata()->hasProperty($columnName)) {
					return self::UNDEFINED_VALUE;
				}
				$value = $value->{$columnName} ?? null;
			}
		}
		return $value;
	}
}
