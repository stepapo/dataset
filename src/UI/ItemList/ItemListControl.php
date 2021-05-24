<?php

declare(strict_types=1);

namespace Stepapo\Dataset\UI\ItemList;

use Nette\ComponentModel\IComponent;
use Nette\InvalidArgumentException;
use Stepapo\Dataset\UI\DatasetControl;
use Stepapo\Dataset\UI\Item\ItemControl;
use Nette\Application\UI\Multiplier;
use Nextras\Orm\Entity\IEntity;
use Nextras\Orm\Relationships\HasMany;


/**
 * @property-read ItemListTemplate $template
 */
class ItemListControl extends DatasetControl
{
	public function render()
	{
		parent::render();
		if ($this->getDataset()->shouldRetrieveItems) {
			$this->template->items = $this->getItems();
		}
		$this->template->idColumnName = $this->getDataset()->getIdColumnName();
		$this->template->render($this->getSelectedView()->itemListTemplate);
	}


	public function createComponentItem()
	{
		return new Multiplier(function ($id): IComponent {
			$entity = $this->template->items[$id] ?? $this->getRepository()->getById($id);
			$control = $this->getDataset()->getSelectedView()->itemFactoryCallback
				? ($this->getDataset()->getSelectedView()->itemFactoryCallback)($entity)
				: new ItemControl($entity);
			if (property_exists($control, 'onChange')) {
				$control->onChange[] = function (IComponent $control, IEntity $entity) {
					if (!$this->getDataset()->getAlwaysRetrieveItems() && $this->presenter->isAjax()) {
						$this->getDataset()->shouldRetrieveItems = false;
					}
					$this->getDataset()->onItemChange($this->getDataset(), $entity);
				};
			}
			if (property_exists($control, 'onRemove')) {
				$control->onRemove[] = function (IComponent $control) {
					$this->redrawControl();
					if ($this->getDataset()->getItemsPerPage()) {
						$this->getDataset()->getComponent('pagination')->redrawControl();
					}
					$this->getDataset()->onItemChange($this->getDataset());
				};
			}
			return $control;
		});
	}


	public function getValue(IEntity $entity, $columnName)
	{
		$columnNames = explode('.', $columnName);
		$value = $entity;
		foreach ($columnNames as $columnName) {
			if ($value instanceof HasMany) {
				throw new InvalidArgumentException();
			} else {
				if (!isset($value->{$columnName})) {
					return null;
				}
				$value = $value->{$columnName};
			}
		}
		return $value;
	}


	private function getItems()
	{
		return $this->getDataset()->getCollectionItems()->fetchPairs($this->getDataset()->getIdColumnName());
	}
}
