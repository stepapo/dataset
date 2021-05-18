<?php

declare(strict_types=1);

namespace Stepapo\Dataset;

use Nette\InvalidArgumentException;


class Search
{
	public function __construct(
		public OrmFunction $searchFunction,
		public ?string $placeholder = null,
		public $prepareCallback = null,
		public $suggestCallback = null,
		public ?OrmFunction $sortFunction = null
	) {}


	public static function createFromArray(array $config): Search
	{
		if (!isset($config['searchFunction'])) {
			throw new InvalidArgumentException('Search function has to be defined.');
		}
		$searchFunction = OrmFunction::createFromArray((array) $config['searchFunction']);
		$search = new self($searchFunction);
		if (array_key_exists('placeholder', $config)) {
			$search->setPlaceholder($config['placeholder']);
		}
		if (array_key_exists('prepareCallback', $config)) {
			$search->setPrepareCallback($config['prepareCallback']);
		}
		if (array_key_exists('suggestCallback', $config)) {
			$search->setSuggestCallback($config['suggestCallback']);
		}
		if (array_key_exists('sortFunction', $config)) {
			$search->setSortFunction(OrmFunction::createFromArray((array) $config['sortFunction']));
		}
		return $search;
	}


	public function setSearchFunction(OrmFunction $searchFunction): Search
	{
		$this->searchFunction = $searchFunction;
		return $this;
	}


	public function setPlaceholder(?string $placeholder): Search
	{
		$this->placeholder = $placeholder;
		return $this;
	}


	public function setPrepareCallback(?callable $prepareCallback): Search
	{
		$this->prepareCallback = $prepareCallback;
		return $this;
	}


	public function setSuggestCallback(?callable $suggestCallback): Search
	{
		$this->suggestCallback = $suggestCallback;
		return $this;
	}


	public function setSortFunction(?OrmFunction $sortFunction): Search
	{
		$this->sortFunction = $sortFunction;
		return $this;
	}
}
