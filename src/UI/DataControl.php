<?php

declare(strict_types=1);

namespace Stepapo\Data\UI;

use Nette\Application\UI\ITemplate;
use Nette\Localization\ITranslator;
use Nextras\Orm\Entity\IEntity;
use Nextras\Orm\Repository\IRepository;
use Stepapo\Data\Column;
use Stepapo\Data\View;
use Nette\Application\UI\Control;
use Nextras\Orm\Collection\ICollection;
use Ublaboo\ImageStorage\ImageStorage;


abstract class DataControl extends Control
{
	public function render()
	{
		$this->template->columns = $this->getColumns();
		$this->template->views = $this->getViews();
		$this->template->selectedView = $this->getSelectedView();
		$this->template->imageStorage = $this->getImageStorage();
	}


	protected function createTemplate(): ITemplate
	{
		$template = parent::createTemplate();
		$template->setTranslator($this->getTranslator());
		return $template;
	}


	abstract public function getMainComponent(): ?MainComponent;


	public function getCollection(): ICollection
	{
		return $this->getMainComponent()->getCollection();
	}


	public function getRepository(): IRepository
	{
		return $this->getMainComponent()->getRepository();
	}


	public function getParentEntity(): ?IEntity
	{
		return $this->getMainComponent()->getParentEntity();
	}


	public function getTranslator(): ?ITranslator
	{
		return $this->getMainComponent()->getTranslator();
	}


	public function getImageStorage(): ?ImageStorage
	{
		return $this->getMainComponent()->getImageStorage();
	}


	/** @var Column[]|null */
	public function getColumns(): ?array
	{
		return $this->getMainComponent()->getColumns();
	}


	/** @var View[]|null */
	public function getViews(): ?array
	{
		return $this->getMainComponent()->getViews();
	}


	public function getSelectedView(): View
	{
		return $this->getMainComponent()->getSelectedView();
	}
}
