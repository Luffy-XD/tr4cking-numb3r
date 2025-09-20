<?php

namespace App\Core;

class Validator
{
    private array $data;
    private array $rules;
    private array $errors = [];

    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->validate();
    }

    private function validate(): void
    {
        foreach ($this->rules as $field => $rules) {
            $value = $this->data[$field] ?? null;
            foreach (explode('|', $rules) as $rule) {
                $rule = trim($rule);
                if ($rule === 'required' && ($value === null || $value === '')) {
                    $this->addError($field, 'Field wajib diisi');
                } elseif ($rule === 'email' && $value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, 'Format email tidak valid');
                } elseif ($rule === 'date' && $value && !strtotime($value)) {
                    $this->addError($field, 'Format tanggal tidak valid');
                } elseif (str_starts_with($rule, 'max:')) {
                    $length = (int) substr($rule, 4);
                    if ($value && strlen((string) $value) > $length) {
                        $this->addError($field, "Maksimal {$length} karakter");
                    }
                }
            }
        }
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
