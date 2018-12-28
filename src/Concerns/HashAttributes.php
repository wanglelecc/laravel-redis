<?php

namespace Wanglelecc\Redis\Concerns;
use Illuminate\Support\Str;

trait HashAttributes
{
    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * 填充值
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function fill($attributes)
    {
        foreach ($this->fillableFromArray($attributes) as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    public function getAttribute($key)
    {
        if (! $key) {
            return;
        }
        
        if (array_key_exists($key, $this->attributes) ||
            $this->hasGetMutator($key)) {
            return $this->getAttributeValue($key);
        }
    
        if (method_exists(self::class, $key)) {
            return;
        }
        
        return;
    }

    /**
     * Get a plain attribute (not a relationship).
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttributeValue($key)
    {
        $value = $this->getAttributeFromArray($key);
        
        if ($this->hasGetMutator($key)) {
            return $this->mutateAttribute($key, $value);
        }
    
        if ($this->hasJson($value)) {
            return json_decode($value, true);
        }

        return $value;
    }
    
    
    protected function hasJson($value){
        if( !is_string($value) ){ return false; }
        
        $json = json_decode($value);
        
        return JSON_ERROR_NONE === json_last_error();
    }

    /**
     * Get an attribute from the $attributes array.
     *
     * @param  string  $key
     * @return mixed
     */
    protected function getAttributeFromArray($key)
    {
        if (isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }
        
        return;
    }

    /**
     * Determine if a get mutator exists for an attribute.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasGetMutator($key)
    {
        return method_exists($this, 'get'.Str::studly($key).'Attribute');
    }

    /**
     * Get the value of an attribute using its mutator.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function mutateAttribute($key, $value)
    {
        return $this->{'get'.Str::studly($key).'Attribute'}($value);
    }
    
    /**
     * Set a given attribute on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        if ($this->hasSetMutator($key)) {
            return $this->setMutatedAttributeValue($key, $value);
        }
        
        $this->attributes[$key] = $value;

        return $this;
    }
    
    protected function setMutatedAttributeValue($key, $value)
    {
        return $this->{'set'.Str::studly($key).'Attribute'}($value);
    }

    /**
     * Determine if a set mutator exists for an attribute.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasSetMutator($key)
    {
        return method_exists($this, 'set'.Str::studly($key).'Attribute');
    }
    
    public function attributesToArray()
    {
        return $this->attributes;
    }
    
    /**
     * Encode the given value as JSON.
     *
     * @param  mixed  $value
     * @return string
     */
    protected function asJson($value)
    {
        return json_encode($value);
    }
    
    public function toArray()
    {
        return $this->attributesToArray();
    }
    
    public function __toString()
    {
        return $this->toJson();
    }
    
    public function toJson($options = 0)
    {
        $json = json_encode($this->jsonSerialize(), $options);
        
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw JsonEncodingException::forModel($this, json_last_error_msg());
        }
        
        return $json;
    }
    
    /**
     * Decode the given JSON back into an array or object.
     *
     * @param  string  $value
     * @param  bool  $asObject
     * @return mixed
     */
    public function fromJson($value, $asObject = false)
    {
        return json_decode($value, ! $asObject);
    }
    
    /**
     * Get all of the current attributes on the model.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
    

    public function jsonSerialize()
    {
        return $this->toArray();
    }
    
    public function getFillable()
    {
        return $this->fillable;
    }
    
    protected function fillableFromArray(array $attributes)
    {
        if (count($this->getFillable()) > 0 && ! static::$unguarded) {
            return array_intersect_key($attributes, array_flip($this->getFillable()));
        }
        
        return $attributes;
    }

    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }

    public function __isset($key)
    {
        return isset($this->attributes[$key]);
    }

    public function __unset($key)
    {
        unset($this->attributes[$key]);
    }

    public function __call($method, $parameters)
    {
        return $this->{$method}(...$parameters);
    }

    public static function __callStatic($method, $parameters)
    {
        return (new static)->$method(...$parameters);
    }

    public function __wakeup()
    {

    }
}
