<?php

declare(strict_types=1);

namespace Stepapo\Dataset\UI;

use Nette\Application\UI\Template;
use Nette\Localization\Translator;
use Nextras\Orm\Entity\IEntity;
use Nextras\Orm\Repository\IRepository;
use Stepapo\Dataset\Column;
use Stepapo\Dataset\Text;
use Stepapo\Dataset\UI\Dataset\Dataset;
use Stepapo\Dataset\View;
use Nette\Application\UI\Control;
use Nextras\Orm\Collection\ICollection;
use Ublaboo\ImageStorage\ImageStorage;


abstract class DatasetControl extends Control
{
	public function render()
	{
	    $this->template->text = $this->getText();
		$this->template->columns = $this->getColumns();
		$this->template->views = $this->getViews();
		$this->template->selectedView = $this->getSelectedView();
		$this->template->imageStorage = $this->getImageStorage();
	}


	protected function createTemplate(): Template
	{
		$template = parent::createTemplate();
		$template->setTranslator($this->getTranslator());
		return $template;
	}

	
	public function getDataset(): ?Dataset
	{
		return $this->lookup(Dataset::class, false);
	}


	public function getCollection(): ICollection
	{
		return $this->getDataset()->getCollection();
	}


	public function getRepository(): IRepository
	{
		return $this->getDataset()->getRepository();
	}


    public function getText(): Text
    {
        return $this->getDataset()->getText();
    }


	public function getParentEntity(): ?IEntity
	{
		return $this->getDataset()->getParentEntity();
	}


	public function getTranslator(): ?Translator
	{
		return $this->getDataset()->getTranslator();
	}


	public function getImageStorage(): ?ImageStorage
	{
		return $this->getDataset()->getImageStorage();
	}


	/** @var Column[] */
	public function getColumns(): array
	{
		return $this->getDataset()->getColumns();
	}


	/** @var View[] */
	public function getViews(): array
	{
		return $this->getDataset()->getViews();
	}


	public function getSelectedView(): View
	{
		return $this->getDataset()->getSelectedView();
	}
}
