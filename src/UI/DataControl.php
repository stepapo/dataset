<?php

declare(strict_types=1);

namespace Stepapo\Data\UI;

use Nette\Application\UI\ITemplate;
use Nette\Localization\ITranslator;
use Stepapo\Data\Column;
use Stepapo\Data\Factory;
use Stepapo\Data\View;
use Nette\Application\UI\Control;
use Nextras\Orm\Collection\ICollection;


abstract class DataControl extends Control
{
    public function render()
    {
        $this->template->columns = $this->getColumns();
        $this->template->selectedView = $this->getSelectedView();
    }


	protected function createTemplate(): ITemplate
	{
		/** @var \Latte\Runtime\Template $template */
		$template = parent::createTemplate();
		$template->setTranslator($this->getTranslator());
		return $template;
	}


    abstract public function getMainComponent(): ?MainComponent;


    public function getCollection(): ICollection
    {
        return $this->getMainComponent()->getCollection();
    }


	public function getTranslator(): ?ITranslator
	{
		return $this->getMainComponent()->getTranslator();
	}


    /** @var Column[]|null */
    public function getColumns(): ?array
    {
        return $this->getMainComponent()->getColumns();
    }


    public function getSelectedView(): View
    {
        return $this->getMainComponent()->getSelectedView();
    }


    public function getFactory(): Factory
    {
        return $this->getMainComponent()->getFactory();
    }


    public function getFilter(): array
    {
        return $this->getMainComponent()->getFilter();
    }
}
