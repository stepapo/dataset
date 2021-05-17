<?php

declare(strict_types=1);

namespace Stepapo\Dataset\UI\Display;

use Nette\Application\Attributes\Persistent;
use Stepapo\Dataset\UI\DatasetControl;


/**
 * @property-read DisplayTemplate $template
 * @method onDisplay(DisplayControl $control)
 */
class DisplayControl extends DatasetControl
{
	#[Persistent]
	public ?string $viewName = null;

	public array $onDisplay;


	public function render()
	{
		if (count($this->getViews()) < 2) {
			return;
		}
		parent::render();
		$this->template->viewName = $this->viewName;
		$this->template->render($this->getSelectedView()->displayTemplate);
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
