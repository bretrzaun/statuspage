<?php

namespace BretRZaun\StatusPage;

interface StatusCheckerInterface
{
    public function check(): void;
    public function getResults(): array;
    public function hasErrors(): bool;
}
