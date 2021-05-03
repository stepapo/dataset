<?php

declare(strict_types=1);

namespace Stepapo\Data\UI\Dataset\Item;

use Nette\ComponentModel\IComponent;
use Stepapo\Data\UI\Dataset\Button\ButtonControl;
use Stepapo\Data\UI\Dataset\Dataset\Dataset;
use Stepapo\Data\UI\Dataset\DatasetControl;
use Nette\Application\UI\Multiplier;
use Nextras\Orm\Entity\IEntity;
use Nextras\Orm\Relationships\HasMany;


/**
 * @property-read ItemTemplate $template
 * @method onChange(ItemControl $control, IEntity $entity)
 * @method onRemove(ItemControl $control)
 */
class ItemControl extends DatasetControl
{
	/** @var callable[] */
	public array $onChange;

	/** @var callable[] */
	public array $onRemove;

	private IEntity $entity;


	public function __construct(
		IEntity $entity
	) {
		$this->entity = $entity;
	}


	public function render()
	{
		parent::render();
		$this->template->buttons = $this->getMainComponent()->getButtons();
		$this->template->parts = $this->getMainComponent()->getItemParts();
		$this->template->itemClassCallback = $this->getMainComponent()->getItemClassCallback();
		$this->template->item = $this->entity;
		$this->template->render($this->getSelectedView()->itemTemplate);
	}


	public function createComponentButton()
	{
		return new Multiplier(function(string $columnName): ButtonControl {
			$button = new ButtonControl(
				$this->entity,
				$this->getMainComponent()->getButtons()[$columnName],
			);
			$button->onExecute[] = function (ButtonControl $control, IEntity $entity) {
				$this->redrawControl('buttons');
				$this->onChange($this, $entity);
			};
			$button->onRemove[] = function (ButtonControl $control) {
				$this->onRemove($this);
			};
			return $button;
		});
	}


	public function createComponentPart()
	{
		return new Multiplier(function(string $name): IComponent {
			$itemPart = $this->getMainComponent()->getItemParts()[$name];
			$control = ($itemPart->callback)($this, $this->entity);
			if ($control instanceof Dataset) {
				$control->onItemChange[] = function (Dataset $control, ?IEntity $entity = null) {
					$this->getMainComponent()->shouldRetrieveItems = false;
					$this->getMainComponent()->onItemChange($this->getMainComponent(), $entity);
				};
			}
			return $control;
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
