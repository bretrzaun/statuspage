<?php

namespace BretRZaun\StatusPage;

interface StatusCheckerInterface
{
    public function check();
    public function getResults();
    public function hasErrors();
}
