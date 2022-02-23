<?php

declare(strict_types=1);

namespace Stepapo\Dataset;


class Filter
{
	/** @var Option[] $options */
	public function __construct(
		public ?array $options = [],
		public ?string $prompt = null,
		public ?string $columnName = null,
		public ?string $function = null,
		public ?int $collapse = null,
		public bool $hide = false
	) {}


	public static function createFromArray(array $config, array $params = []): Filter
	{
		$filter = new self();
		if (array_key_exists('options', $config)) {
			foreach ((array) $config['options'] as $name => $optionConfig) {
				$filter->addOption(Option::createFromArray((array) $optionConfig, $name));
			}
		}
		if (array_key_exists('prompt', $config)) {
			$filter->setPrompt($config['prompt']);
		}
		if (array_key_exists('collapse', $config)) {
			$filter->setCollapse($config['collapse']);
		}
		if (array_key_exists('columnName', $config)) {
			$filter->setColumnName($config['columnName']);
		}
		if (array_key_exists('function', $config)) {
			$filter->setFunction($config['function']);
		}
		if (array_key_exists('hide', $config)) {
			$filter->setHide($config['hide']);
		}
		return $filter;
	}


	public function getPrompt(): ?string
	{
		return $this->prompt;
	}


	public function setPrompt(?string $prompt): Filter
	{
		$this->prompt = $prompt;
		return $this;
	}


	public function getCollapse(): ?int
	{
		return $this->collapse;
	}


	public function setCollapse(?int $collapse): Filter
	{
		$this->collapse = $collapse;
		return $this;
	}


	public function getColumnName(): ?string
	{
		return $this->columnName;
	}


	public function setColumnName(?string $columnName): Filter
	{
		$this->columnName = $columnName;
		return $this;
	}


	public function getFunction(): ?string
	{
		return $this->function;
	}


	public function setFunction(?string $function): Filter
	{
		$this->function = $function;
		return $this;
	}


	public function getHide(): bool
	{
		return $this->hide;
	}


	public function setHide(bool $hide): Filter
	{
		$this->hide = $hide;
		return $this;
	}


	public function addOption(Option $option): Filter
	{
		$this->options[$option->name] = $option;
		return $this;
	}


	public function getNextrasName(bool $withThis = true)
	{
		if (strpos($this->columnName, '.') !== false) {
			return str_replace('.', '->', $this->columnName);
		}

		if (strpos($this->columnName, '_') !== false) {
			return str_replace('_', '->', $this->columnName);
		}

		return $this->columnName;
	}

}
