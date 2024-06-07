<?php

declare(strict_types=1);

namespace Stepapo\Dataset\UI\Item;

use Nette\InvalidArgumentException;
use Stepapo\Dataset\UI\DatasetControl;
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


	public function __construct(
		private IEntity $entity
	) {}


	public function render()
	{
		parent::render();
		$this->template->itemClassCallback = $this->getDataset()->getItemClassCallback();
		$itemLink = $this->getDataset()->getItemLink();
		$this->template->itemLink = $itemLink;
		$this->template->linkArgs = $itemLink && $itemLink->args ? array_map(fn($a) => $this->getValue($a) ?: $a, $itemLink->args) : null;
		$this->template->item = $this->entity;
		$this->template->render($this->getSelectedView()->itemTemplate);
	}


	public function getValue($columnName)
	{
		$columnNames = explode('.', $columnName);
		$value = $this->entity;
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
}
