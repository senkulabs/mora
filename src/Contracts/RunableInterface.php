<?php

namespace SenkuLabs\Mora\Contracts;

interface RunableInterface
{
    /**
     * Run the specified command.
     */
    public function run(string $command);
}
