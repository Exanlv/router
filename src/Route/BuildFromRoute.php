<?php

namespace Exan\Router\Route;

interface BuildFromRoute
{
    public static function buildFromRoute(string $rawValue): static;
}
