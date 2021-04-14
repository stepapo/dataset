<?php

declare(strict_types=1);

namespace Stepapo\Data\UI\Dataset\SearchForm;

use Stepapo\Data\UI\Dataset\DatasetControl;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;


/**
 * @property-read SearchFormTemplate $template
 * @method onSearch(SearchFormControl $control)
 */
class SearchFormControl extends DatasetControl
{
	/** @var callable[] */
	public array $onSearch = [];

	/** @persistent */
	public ?string $term = null;

	private ?string $placeholder;


	public function __construct(
		?string $placeholder = null
	) {
		$this->placeholder = $placeholder;
	}


	public function render(): void
	{
		$this->template->term = $this->term;
		$this->template->render($this->getSelectedView()->searchTemplate);
	}


	public function createComponentF(): Form
	{
		$form = new Form();

		$form->addText('term')
			->setHtmlAttribute('placeholder', $this->placeholder ? $this->getTranslator()->translate($this->placeholder) . '...' : null);

		$form->addSubmit('send', $this->getTranslator()->translate('Hledat'));

		$form->onValidate[] = [$this, 'formValidate'];
		$form->onSuccess[] = [$this, 'formSucceeded'];

		$form['term']->setDefaultValue($this->term);

		return $form;
	}


	public function formValidate(Form $form, ArrayHash $values)
	{
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
