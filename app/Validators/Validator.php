<?php
namespace App\Validators;

use PDO;
use Exception;

/**
 * Simple Builder-style Validator
 * Usage example:
 * $v = (new Validator($_POST))
 *     ->field('age')->numeric()->between(1,120)
 *     ->field('status')->in(['active','inactive'])
 *     ->field('email')->unique($pdo, 'users', 'email')
 *     ->validate();
 *
 * Note: this is a minimal implementation — adapt to framework/controller.
 */
class Validator
{
    protected $data = [];
    protected $errors = [];
    protected $currentField = null;
    protected $rules = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    // start rule chain for a field
    public function field(string $name)
    {
        $this->currentField = $name;
        if (!isset($this->rules[$name])) {
            $this->rules[$name] = [];
        }
        return $this;
    }

    // numeric() - int or float
    public function numeric()
    {
        $this->rules[$this->currentField][] = function($value) {
            if ($value === null || $value === '') return true; // allow null; use required separately
            return is_numeric($value) ? true : "The field must be numeric.";
        };
        return $this;
    }

    // between($min, $max)
    public function between($min, $max)
    {
        $this->rules[$this->currentField][] = function($value) use ($min, $max) {
            if ($value === null || $value === '') return true;
            if (!is_numeric($value)) return "The field must be numeric for between() check.";
            $num = $value + 0;
            return ($num >= $min && $num <= $max) ? true : "The field must be between {$min} and {$max}.";
        };
        return $this;
    }

    // in($array) - whitelist
    public function in(array $array)
    {
        $this->rules[$this->currentField][] = function($value) use ($array) {
            if ($value === null || $value === '') return true;
            return in_array($value, $array, true) ? true : "The field value is not allowed.";
        };
        return $this;
    }

    // unique($pdo, $table, $column, $exceptId = null)
    public function unique(PDO $pdo, string $table, string $column, $exceptId = null)
    {
        $this->rules[$this->currentField][] = function($value) use ($pdo, $table, $column, $exceptId) {
            if ($value === null || $value === '') return true;
            $sql = "SELECT COUNT(*) FROM {$table} WHERE {$column} = :value";
            $params = [':value' => $value];
            if ($exceptId !== null) {
                $sql .= " AND id != :id";
                $params[':id'] = $exceptId;
            }
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $count = (int) $stmt->fetchColumn();
            return $count === 0 ? true : "The field must be unique in {$table}.";
        };
        return $this;
    }

    // confirmed($field) - check companion field {field}_confirmation
    public function confirmed(string $otherField)
    {
        $this->rules[$this->currentField][] = function($value) use ($otherField) {
            $confirmKey = $otherField . '_confirmation';
            $confirm = $this->data[$confirmKey] ?? null;
            return ($value === $confirm) ? true : "The field confirmation does not match.";
        };
        return $this;
    }

    // dateFormat($format) - validate date format using DateTime::createFromFormat
    public function dateFormat(string $format)
    {
        $this->rules[$this->currentField][] = function($value) use ($format) {
            if ($value === null || $value === '') return true;
            $d = \DateTime::createFromFormat($format, $value);
            $errors = \DateTime::getLastErrors();
            if ($d && $errors['warning_count'] == 0 && $errors['error_count'] == 0) {
                return true;
            }
            return "The field does not match date format {$format}.";
        };
        return $this;
    }

    // run validation
    public function validate(): bool
    {
        $this->errors = [];
        foreach ($this->rules as $field => $ruleList) {
            $value = $this->data[$field] ?? null;
            foreach ($ruleList as $rule) {
                $result = $rule($value);
                if ($result !== true) {
                    $this->errors[$field][] = $result;
                }
            }
        }
        return empty($this->errors);
    }

    public function fails(): bool
    {
        return !$this->validate();
    }

    public function errors(): array
    {
        return $this->errors;
    }

    // helper to get first error message per field
    public function first(string $field)
    {
        return $this->errors[$field][0] ?? null;
    }
}