<?php

namespace App\Contracts;

interface LabeledEnum extends \BackedEnum
{
    public function label(): string;
}
