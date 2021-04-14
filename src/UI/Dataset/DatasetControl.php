<?php

declare(strict_types=1);

namespace Stepapo\Data\UI\Dataset;

use Nextras\Orm\Collection\ICollection;
use Stepapo\Data\Button;
use Stepapo\Data\UI\DataControl;
use Stepapo\Data\UI\Dataset\Dataset\Dataset;


abstract class DatasetControl extends DataControl
{   
	public function render()
	{
		parent::render();
		$this->template->buttons = $this->getButtons();
	}


	public function getMainComponent(): ?Dataset
	{
		return $this->lookup(Dataset::class, false);
	}


	public function getCollectionItems(): ICollection
	{
		return $this->getMainComponent()->getCollectionItems();
	}


	public function getCollectionCount(): int
	{
		return $this->getMainComponent()->getCollectionCount();
	}


	public function getDatasetCallback(): ?callable
	{
		return $this->getMainComponent()->getDatasetCallback();
	}


	public function getFormCallback(): ?callable
	{
		return $this->getMainComponent()->getFormCallback();
	}


	/** @var Button[]|null */
	public function getButtons(): ?array
	{
		return $this->getMainComponent()->getButtons();
	}
}
