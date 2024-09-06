<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Facades\Log; // For debugging if needed

class CallableCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return   
 callable
     */
    public function get($model, string $key, $value, array $attributes)
    {
        if (is_null($value)) {
            return null;
        }

        // Deserialize the callable from the database representation
        // Adjust this based on your chosen storage format
        // Here, we assume it's stored as "Class@method"
        $parts = explode('@', $value);

        // Log::debug("Deserializing callable: ", $parts); // Uncomment for debugging

        return [new $parts[0], $parts[1]];
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param   
  callable  $value
     * @param  array  $attributes
     * @return string
     */
    public function set($model, string $key, $value, array $attributes)
    {
        if (is_null($value)) {
            return null;
        }

        // Serialize the callable to a format suitable for database storage
        // Here, we store it as "Class@method"
        $serialized = get_class($value[0]) . '@' . $value[1];

        // Log::debug("Serializing callable: ", $serialized); // Uncomment for debugging

        return $serialized;
    }
}