<?php
// validation.php - Simple input validation helpers for API

function validateInt($value, $min = null, $max = null) {
    if (!is_numeric($value) || intval($value) != $value) {
        return false;
    }
    $int = intval($value);
    if ($min !== null && $int < $min) return false;
    if ($max !== null && $int > $max) return false;
    return $int;
}

function validateString($value, $maxLength = 255) {
    if (!is_string($value)) return false;
    $trim = trim($value);
    if (strlen($trim) === 0) return false;
    if (strlen($trim) > $maxLength) return false;
    return $trim;
}

function validateSlug($slug) {
    // Allow only alphanumeric, hyphens, underscores
    if (!preg_match('/^[a-z0-9_-]+$/', $slug)) return false;
    return $slug;
}

function validateBoolean($value, $default = false) {
    if (is_string($value)) {
        $lower = strtolower($value);
        if (in_array($lower, ['true','1','yes','on'])) return true;
        if (in_array($lower, ['false','0','no','off'])) return false;
    }
    return (bool)$value;
}
?>
