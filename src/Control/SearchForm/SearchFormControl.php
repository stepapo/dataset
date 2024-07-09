<?php

declare(strict_types=1);

namespace Stepapo\Dataset\Control\SearchForm;

use Nette\Application\Attributes\Persistent;
use Nette\Application\UI\Form;
use Nette\Localization\Translator;
use Nette\Utils\ArrayHash;
use Stepapo\Data\Control\DataControl;


/**
 * @property SearchFormTemplate $template
 * @method onSearch(SearchFormControl $control)
 */
class SearchFormControl extends DataControl
{
	/** @var callable[] */
	public array $onSearch;

	#[Persistent]
	public ?string $term = null;


	public function __construct(
		private ?Translator $translator,
		private ?string $placeholder = null,
	) {}


	public function render(): void
	{
		$this->template->term = $this->term;
		$this->template->render($this->getView()->searchTemplate);
	}


	public function createComponentForm(): Form
	{
		$form = new Form();

		$form->addText('term')
			->setHtmlAttribute('placeholder', $this->placeholder ? ($this->translator ? $this->translator->translate($this->placeholder) : $this->placeholder) . '...' : null);

		$form->addSubmit('send', $this->translator ? $this->translator->translate('Hledat') : 'Hledat');

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
