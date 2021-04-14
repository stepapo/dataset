<?php

declare(strict_types=1);

namespace Stepapo\Data\UI\Dataset\Value;

use Stepapo\Data\Column;
use Stepapo\Data\UI\Dataset\DatasetControl;
use Nextras\Orm\Entity\IEntity;
use Nextras\Orm\Relationships\HasMany;


/**
 * @property-read ValueTemplate $template
 */
class ValueControl extends DatasetControl
{
	private IEntity $entity;

	private Column $column;


	public function __construct(
		IEntity $entity,
		Column $column
	) {
		$this->entity = $entity;
		$this->column = $column;
	}


	public function render()
	{
		parent::render();
		$this->template->entity = $this->entity;
		$this->template->value = $this->getEntityValue();
		$this->template->column = $this->column;
		$this->template->linkArgs = $this->column->link && $this->column->link->args ? array_map(fn($a) => $this->getEntityValue($a), $this->column->link->args) : null;
		$this->template->render($this->column->valueTemplateFile ?: __DIR__ . '/value.latte');
	}


	public function getEntityValue(?string $columnName = null): ?array
	{
		$columnNames = explode('.', $columnName ?: $this->column->columnName);
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
