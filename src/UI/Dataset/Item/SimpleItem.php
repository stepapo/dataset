<?php

declare(strict_types=1);

namespace Stepapo\Data\UI\Dataset\Item;

use Stepapo\Data\UI\Dataset\Attribute\Attribute;
use Stepapo\Data\UI\Dataset\DatasetControl;
use Nette\Application\UI\Multiplier;
use Nextras\Orm\Entity\IEntity;
use Nextras\Orm\Relationships\HasMany;


/**
 * @property-read ItemTemplate $template
 */
class SimpleItem extends DatasetControl implements Item
{
	private IEntity $entity;


	public function __construct(
		IEntity $entity
	) {
		$this->entity = $entity;
	}


	public function render()
	{
		parent::render();
		$this->template->item = $this->entity;
		$this->template->render($this->getSelectedView()->itemTemplate);
	}


	public function createComponentAttribute()
	{
		return new Multiplier(function(string $columnName): Attribute {
			return $this->getFactory()->createAttribute(
				$this->entity,
				$this->getColumns()[$columnName],
			);
		});
	}


	public function getValue($columnName)
	{
		$columnNames = explode('.', $columnName);
		$values = [$this->entity];
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
