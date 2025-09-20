<?php

namespace App\Core;

class Validator
{
    protected array $data;
    protected array $rules;
    protected array $errors = [];

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->validate();
    }

    public static function make(array $data, array $rules): self
    {
        return new self($data, $rules);
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    protected function validate(): void
    {
        foreach ($this->rules as $field => $rules) {
            $value = $this->data[$field] ?? null;
            foreach (explode('|', $rules) as $rule) {
                if ($rule === 'required' && ($value === null || $value === '')) {
                    $this->errors[$field][] = 'The ' . $field . ' field is required.';
                }
                if ($rule === 'email' && $value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field][] = 'The ' . $field . ' field must be a valid email address.';
                }
                if (str_starts_with($rule, 'max:') && $value !== null) {
                    $max = (int) substr($rule, 4);
                    if (is_string($value) && strlen($value) > $max) {
                        $this->errors[$field][] = 'The ' . $field . ' may not be greater than ' . $max . ' characters.';
                    }
                }
                if ($rule === 'date' && $value && !strtotime($value)) {
                    $this->errors[$field][] = 'The ' . $field . ' must be a valid date.';
                }
            }
        }
    }
}
