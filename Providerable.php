<?php declare(strict_types = 1);


namespace src\Integration;

/**
 * Interface Providerable
 *
 * @package src\Integration
 */
interface Providerable
{
    public function get(array $request);
}