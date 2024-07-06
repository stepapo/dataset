<?php

declare(strict_types=1);

namespace Stepapo\Dataset\Control\Item;

use Nette\InvalidArgumentException;
use Stepapo\Dataset\Link;
use Stepapo\Dataset\Control\BaseControl;
use Nextras\Orm\Entity\IEntity;
use Nextras\Orm\Relationships\HasMany;


/**
 * @property-read ItemTemplate $template
 * @method onChange(ItemControl $control, IEntity $entity)
 * @method onRemove(ItemControl $control)
 */
class ItemControl extends BaseControl
{
	private const UNDEFINED_VALUE = 'undefined_value';

	/** @var callable[] */
	public array $onChange;

	/** @var callable[] */
	public array $onRemove;


	public function __construct(
		private IEntity $entity,
		private $itemClassCallback,
		private ?Link $itemLink,
	) {}


	public function render()
	{
		parent::render();
		$this->template->itemClassCallback = $this->itemClassCallback;
		$this->template->itemLink = $this->itemLink;
		$this->template->linkArgs = $this->itemLink && $this->itemLink->args ? array_map(fn($a) => $this->getValue($a) === self::UNDEFINED_VALUE ? $a : $this->getValue($a), $this->itemLink->args) : null;
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
				if (!$value?->getMetadata()->hasProperty($columnName)) {
					return self::UNDEFINED_VALUE;
				}
				$value = $value->{$columnName};
			}
		}
		return $value;
	}
}
