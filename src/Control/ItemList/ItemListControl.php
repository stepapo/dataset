<?php

declare(strict_types=1);

namespace Stepapo\Dataset\Control\ItemList;

use Nette\Application\UI\Multiplier;
use Nette\ComponentModel\IComponent;
use Nette\InvalidArgumentException;
use Nextras\Orm\Entity\IEntity;
use Nextras\Orm\Relationships\HasMany;
use Stepapo\Data\Control\DataControl;
use Stepapo\Data\Control\MainComponent;
use Stepapo\Dataset\Control\Item\ItemControl;
use Stepapo\Data\Link;


/**
 * @property-read ItemListTemplate $template
 */
class ItemListControl extends DataControl
{
	public function __construct(
		private MainComponent $main,
		private array $columns,
		private string $idColumnName,
		private ?int $itemsPerPage,
		private ?string $itemListClass,
		private $itemClassCallback,
		private ?Link $itemLink,
	) {}


	public function render()
	{
		if ($this->main->shouldRetrieveItems) {
			$this->template->items = $this->getItems();
		}
		$this->template->main = $this->main;
		$this->template->idColumnName = $this->idColumnName;
		$this->template->itemListClass = $this->itemListClass;
		$this->template->render($this->main->getView()->itemListTemplate);
	}


	public function createComponentItem()
	{
		return new Multiplier(function ($id): IComponent {
			$entity = $this->template->items[$id] ?? $this->getRepository()->getById($id);
			$control = $this->main->getView()->itemFactoryCallback
				? ($this->main->getView()->itemFactoryCallback)($entity)
				: new ItemControl($this->main, $entity, $this->columns, $this->itemClassCallback, $this->itemLink);
			if (property_exists($control, 'onChange')) {
				$control->onChange[] = function (IComponent $control, IEntity $entity) {
					if (!$this->main->getAlwaysRetrieveItems() && $this->presenter->isAjax()) {
						$this->main->shouldRetrieveItems = false;
					}
					$this->main->onItemChange($this->main, $entity);
				};
			}
			if (property_exists($control, 'onRemove')) {
				$control->onRemove[] = function (IComponent $control) {
					$this->redrawControl();
					if ($this->itemsPerPage) {
						$this->main->getComponent('pagination')->redrawControl();
					}
					$this->main->onItemChange($this->main);
				};
			}
			return $control;
		});
	}


	public function getValue(IEntity $entity, $columnName)
	{
		$columnNames = explode('.', $columnName);
		$value = $entity;
		if ($value instanceof HasMany) {
			throw new InvalidArgumentException("Value is a collection.");
		} else {
			foreach ($columnNames as $columnName) {
				if (!$value?->getMetadata()->hasProperty($columnName)) {
					return ItemControl::UNDEFINED_VALUE;
				}
				$value = $value->{$columnName};
			}
		}
		return $value;
	}


	private function getItems()
	{
		$items = $this->main->getCollectionItems()->fetchPairs($this->idColumnName);
		if ($this->itemsPerPage && $this->main->getCurrentCount() > $this->itemsPerPage) {
			array_pop($items);
		}
		return $items;
	}
}
