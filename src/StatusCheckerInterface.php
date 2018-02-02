<?php

namespace BretRZaun\StatusPage;

interface StatusCheckerInterface
{
    public function check();
    public function getResults(): array;
    public function hasErrors();
}
