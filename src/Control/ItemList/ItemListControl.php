<?php

declare(strict_types=1);

namespace Stepapo\Dataset\Control\ItemList;

use Nette\Application\UI\Multiplier;
use Nette\ComponentModel\IComponent;
use Nette\InvalidArgumentException;
use Nextras\Orm\Entity\IEntity;
use Nextras\Orm\Relationships\HasMany;
use Stepapo\Dataset\Control\BaseControl;
use Stepapo\Dataset\Control\Item\ItemControl;
use Stepapo\Dataset\Link;


/**
 * @property-read ItemListTemplate $template
 */
class ItemListControl extends BaseControl
{
	public function __construct(
		private string $idColumnName,
		private ?int $itemsPerPage,
		private ?string $itemListClass,
		private $itemClassCallback,
		private ?Link $itemLink,
	) {}


	public function render()
	{
		parent::render();
		if ($this->getDatasetControl()->shouldRetrieveItems) {
			$this->template->items = $this->getItems();
		}
		$this->template->idColumnName = $this->idColumnName;
		$this->template->itemListClass = $this->itemListClass;
		$this->template->render($this->getSelectedView()->itemListTemplate);
	}


	public function createComponentItem()
	{
		return new Multiplier(function ($id): IComponent {
			$entity = $this->template->items[$id] ?? $this->getRepository()->getById($id);
			$control = $this->getDatasetControl()->getSelectedView()->itemFactoryCallback
				? ($this->getDatasetControl()->getSelectedView()->itemFactoryCallback)($entity)
				: new ItemControl($entity, $this->itemClassCallback, $this->itemLink);
			if (property_exists($control, 'onChange')) {
				$control->onChange[] = function (IComponent $control, IEntity $entity) {
					if (!$this->getDatasetControl()->getAlwaysRetrieveItems() && $this->presenter->isAjax()) {
						$this->getDatasetControl()->shouldRetrieveItems = false;
					}
					$this->getDatasetControl()->onItemChange($this->getDatasetControl(), $entity);
				};
			}
			if (property_exists($control, 'onRemove')) {
				$control->onRemove[] = function (IComponent $control) {
					$this->redrawControl();
					if ($this->itemsPerPage) {
						$this->getDatasetControl()->getComponent('pagination')->redrawControl();
					}
					$this->getDatasetControl()->onItemChange($this->getDatasetControl());
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
		$items = $this->getDatasetControl()->getCollectionItems()->fetchPairs($this->idColumnName);
		if ($this->itemsPerPage && $this->getCurrentCount() > $this->itemsPerPage) {
			array_pop($items);
		}
		return $items;
	}
}
