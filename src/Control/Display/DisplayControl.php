<?php

declare(strict_types=1);

namespace Stepapo\Dataset\Control\Display;

use Nette\Application\Attributes\Persistent;
use Nette\Application\BadRequestException;
use Stepapo\Data\Control\DataControl;
use Stepapo\Data\Text;
use Stepapo\Dataset\Control\Dataset\DatasetControl;
use Stepapo\Dataset\DatasetView;


/**
 * @property-read DisplayTemplate $template
 * @method onDisplay(DisplayControl $control)
 */
class DisplayControl extends DataControl
{
	#[Persistent] public ?string $viewName = null;
	public array $onDisplay;


	/** @param DatasetView[] $views */
	public function __construct(
		private DatasetControl $main,
		public array $views,
		private Text $text,
	) {}


	public function render(): void
	{
		if (count($this->views) < 2) {
			return;
		}
		$this->template->viewName = $this->viewName;
		$this->template->views = $this->views;
		$this->template->main = $this->main;
		$this->template->text = $this->text;
		$this->template->render($this->main->getView()->displayTemplate);
	}


	public function handleDisplay(?string $viewName = null): void
	{
		$this->viewName = $viewName;
		if (!isset($this->views[$viewName]) || $this->views[$viewName]->hide) {
			throw new BadRequestException;
		}
		if ($this->presenter->isAjax()) {
			$this->onDisplay($this);
			$this->redrawControl();
		}
	}
}
