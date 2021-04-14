<?php

declare(strict_types=1);

namespace Stepapo\Data\UI\Dataset\Button;

use Nette\Application\ForbiddenRequestException;
use Stepapo\Data\Button;
use Stepapo\Data\UI\Dataset\DatasetControl;
use Nextras\Orm\Entity\IEntity;


/**
 * @property-read ButtonTemplate $template
 * @method onExecute(ButtonControl $control, IEntity $entity)
 * @method onRemove(ButtonControl $control)
 */
class ButtonControl extends DatasetControl
{
	/** @var callable[] */
	public array $onExecute;

	/** @var callable[] */
	public array $onRemove;

	private IEntity $entity;

	private Button $button;


	public function __construct(
		IEntity $entity,
		Button $button
	) {
		$this->button = $button;
		$this->entity = $entity;
	}


	public function render()
	{
		parent::render();
		if ($this->button->hideCallback && ($this->button->hideCallback)($this, $this->entity)) {
			return;
		}
		$this->template->button = $this->button;
		$this->template->render($this->getSelectedView()->buttonTemplate);
	}


	public function handleExecute(): void
	{
		if ($this->button->hideCallback && ($this->button->hideCallback)($this, $this->entity)) {
			throw new ForbiddenRequestException();
		}
		($this->button->handleCallback)($this, $this->entity);
	}
}
