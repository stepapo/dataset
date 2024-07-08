<?php

declare(strict_types=1);

namespace Stepapo\Dataset\Control;

use Contributte\ImageStorage\ImageStorage;
use Latte\Engine;
use Latte\Essential\RawPhpExtension;
use Nette\Application\UI\Control;
use Nette\Application\UI\Template;
use Nette\Localization\Translator;
use Stepapo\Dataset\Column;
use Stepapo\Dataset\Control\Dataset\DatasetControl;
use Stepapo\Dataset\Text;
use Stepapo\Dataset\View;
use Stepapo\Utils\Latte\Filters;


abstract class BaseControl extends Control
{
	public function render()
	{
		$this->template->text = $this->getText();
		$this->template->columns = $this->getColumns();
		$this->template->views = $this->getViews();
		$this->template->selectedView = $this->getSelectedView();
		$this->template->imageStorage = $this->getImageStorage();
	}


	protected function createTemplate(?string $class = null): Template
	{
		$template = parent::createTemplate($class);
		$template->setTranslator($this->getTranslator());
		$template->addFilter('intlDate', [Filters::class, 'intlDate']);		
		$template->addFilter('plural', [Filters::class, 'plural']);
		if (version_compare(Engine::VERSION, '3', '>=')) {
			$template->getLatte()->addExtension(new RawPhpExtension);
		}
		return $template;
	}

	
	public function getDatasetControl(): ?DatasetControl
	{
		return $this->lookup(DatasetControl::class, false);
	}


//	public function getCollection(): ICollection
//	{
//		return $this->getDatasetControl()->getCollection();
//	}


	public function getCurrentCount(): int
	{
		return $this->getDatasetControl()->getCurrentCount();
	}


	public function getText(): Text
	{
		return $this->getDatasetControl()->getText();
	}


	public function getTranslator(): ?Translator
	{
		return $this->getDatasetControl()->getTranslator();
	}


	public function getImageStorage(): ?ImageStorage
	{
		return $this->getDatasetControl()->getImageStorage();
	}


	/** @var Column[] */
	public function getColumns(): array
	{
		return $this->getDatasetControl()->getColumns();
	}


	/** @var View[] */
	public function getViews(): array
	{
		return $this->getDatasetControl()->getViews();
	}


	public function getSelectedView(): View
	{
		return $this->getDatasetControl()->getSelectedView();
	}
}
