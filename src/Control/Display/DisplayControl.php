<?php

declare(strict_types=1);

namespace Stepapo\Dataset\Control\Display;

use Nette\Application\Attributes\Persistent;
use Stepapo\Data\Control\DataControl;
use Stepapo\Data\Control\MainComponent;
use Stepapo\Data\Text;
use Stepapo\Dataset\View;


/**
 * @property-read DisplayTemplate $template
 * @method onDisplay(DisplayControl $control)
 */
class DisplayControl extends DataControl
{
	#[Persistent]
	public ?string $viewName = null;

	public array $onDisplay;


	/** @param View[] $views */
	public function __construct(
		private MainComponent $main,
		public array $views,
		private Text $text,
	) {}


	public function render()
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
		if ($this->presenter->isAjax()) {
			$this->onDisplay($this);
			$this->redrawControl();
		}
	}
}
