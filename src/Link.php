<?php

declare(strict_types=1);

namespace Stepapo\Data;

use Nette\InvalidArgumentException;


class Link
{
    public string $destination;

    public ?array $args;


    public function __construct(
        string $destination,
        ?array $args = null
    ) {
        $this->destination = $destination;
        $this->args = $args;
    }


    public static function createFromArray(array $config): Link
    {
        if (!isset($config['destination'])) {
            throw new InvalidArgumentException('Link destination has to be defined.');
        }
        return new self(
            $config['destination'],
            isset($config['args']) ? (array) $config['args'] : null
        );
    }
}
