<?php

declare(strict_types=1);

namespace Stepapo\Data\UI\Dataset\ItemList;

use Nette\ComponentModel\IComponent;
use Stepapo\Data\UI\Dataset\DatasetControl;
use Stepapo\Data\UI\Dataset\Item\ItemControl;
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
		if ($this->getMainComponent()->shouldRetrieveItems) {
			$this->template->items = $this->getItems();
		}
		$this->template->idColumnName = $this->getMainComponent()->getIdColumnName();
		$this->template->render($this->getSelectedView()->itemListTemplate);
	}


	public function createComponentItem()
	{
		return new Multiplier(function ($id): IComponent {
			$entity = $this->template->items[$id] ?? $this->getRepository()->getById($id);
			$control = $this->getMainComponent()->getSelectedView()->itemFactoryCallback
                ? ($this->getMainComponent()->getSelectedView()->itemFactoryCallback)($entity)
                : new ItemControl($entity);
			if (property_exists($control, 'onChange')) {
                $control->onChange[] = function (IComponent $control, IEntity $entity) {
                    $this->getMainComponent()->shouldRetrieveItems = false;
                    $this->getMainComponent()->onItemChange($this->getMainComponent(), $entity);
                };
            }
            if (property_exists($control, 'onRemove')) {
                $control->onRemove[] = function (IComponent $control) {
                    $this->redrawControl();
                    if ($this->getMainComponent()->getItemsPerPage()) {
                        $this->getMainComponent()->getComponent('pagination')->redrawControl();
                    }
                    $this->getMainComponent()->onItemChange($this->getMainComponent());
                };
            }
			return $control;
		});
	}


	public function getValue(IEntity $entity, $columnName)
	{
		$columnNames = explode('.', $columnName);
		$values = [$entity];
		foreach ($columnNames as $columnName) {
			$newValues = [];
			foreach ($values as $value) {
				if ($value instanceof HasMany) {
					foreach ($value as $v) {
						if (!isset($v->{$columnName})) {
							return null;
						}
						$newValues[] = $v->{$columnName};
					}
				} else {
					if (!isset($value->{$columnName})) {
						return null;
					}
					$newValues[] = $value->{$columnName};
				}
			}
			$values = $newValues;
		}
		return $values;
	}


	private function getItems()
	{
		return $this->getMainComponent()->getCollectionItems()->fetchPairs($this->getMainComponent()->getIdColumnName());
	}
}
