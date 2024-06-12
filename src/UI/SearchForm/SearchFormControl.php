<?php

declare(strict_types=1);

namespace Stepapo\Dataset\UI\SearchForm;

use Nette\Application\Attributes\Persistent;
use Stepapo\Dataset\UI\DatasetControl;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;


/**
 * @property SearchFormTemplate $template
 * @method onSearch(SearchFormControl $control)
 */
class SearchFormControl extends DatasetControl
{
	/** @var callable[] */
	public array $onSearch;

	#[Persistent]
	public ?string $term = null;


	public function __construct(
		private ?string $placeholder = null
	) {}


	public function render(): void
	{
		parent::render();
		$this->template->term = $this->term;
		$this->template->render($this->getSelectedView()->searchTemplate);
	}


	public function createComponentForm(): Form
	{
		$form = new Form();

		$form->addText('term')
			->setHtmlAttribute('placeholder', $this->placeholder ? ($this->getTranslator() ? $this->getTranslator()->translate($this->placeholder) : $this->placeholder) . '...' : null);

		$form->addSubmit('send', $this->getTranslator() ? $this->getTranslator()->translate('Hledat') : 'Hledat');

		$form->onSuccess[] = [$this, 'formSucceeded'];

		$form['term']->setDefaultValue($this->term);

		return $form;
	}


	public function formSucceeded(Form $form, ArrayHash $values)
	{
		$this->redirect('search!', $values->term);
	}


	public function handleSearch(?string $term = null): void
	{
		$this->term = $term;
		if ($this->presenter->isAjax()) {
			$this->onSearch($this);
			$this->redrawControl();
		}
	}
}
