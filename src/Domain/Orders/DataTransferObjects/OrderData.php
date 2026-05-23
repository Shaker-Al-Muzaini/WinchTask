<?php

namespace Src\Domain\Orders\DataTransferObjects;

readonly class OrderData
{
    public function __construct(
        public array $attributes,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self($data);
    }
}
