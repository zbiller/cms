<?php

namespace App\Traits;

use Exception;
use ReflectionMethod;

trait ChecksTrait
{
    /**
     * Verifies if the given method for setting the trait options exists and is public and static.
     * This method should be called from inside the boot method of the traits using this trait.
     * This method should be called only on traits that have "options" logic.
     *
     * @param string $method
     * @throws Exception
     */
    protected static function checkOptionsMethodDeclaration($method)
    {
        if (!method_exists(self::class, $method)) {
            throw new Exception(
                'The "' . self::class . '" must define the public static "' . $method . '" method.'
            );
        }

        $reflection = new ReflectionMethod(self::class, $method);

        if (!$reflection->isPublic() || !$reflection->isStatic()) {
            throw new Exception(
                'The method "' . $method . '" from the class "' . self::class . '" must be declared as both "public" and "static".'
            );
        }
    }
}