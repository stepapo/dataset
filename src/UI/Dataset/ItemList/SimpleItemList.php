<?php

declare(strict_types=1);

namespace Stepapo\Data\UI\Dataset\ItemList;

use Stepapo\Data\UI\Dataset\DatasetControl;
use Stepapo\Data\UI\Dataset\Item\Item;
use Stepapo\Data\UI\ModePicker\ModePicker;
use Nette\Application\UI\Multiplier;
use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Entity\IEntity;
use Nextras\Orm\Relationships\HasMany;


/**
 * @property-read ItemListTemplate $template
 */
class SimpleItemList extends DatasetControl implements ItemList
{
	private ICollection $items;


	public function __construct(
		ICollection $items
	) {
		$this->items = $items;
	}


	public function render()
	{
		parent::render();
		$this->template->items = $this->items;
		$this->template->render($this->getSelectedView()->itemListTemplate);
	}


	public function createComponentItem()
	{
		return new Multiplier(function ($id): Item {
			$entity = $this->getCollection()->getById($id);
			return $this->getFactory()->createItem(
				$entity,
			);
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
}
