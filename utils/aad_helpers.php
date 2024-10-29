<?php

function aaDRecursiveSanitizeTextField($array)
{
    $filterParameters = [];
    foreach ($array as $key => $value) {

        if ($value === '') {
            $filterParameters[$key] = null;
        } else {
            if (is_array($value)) {
                $filterParameters[$key] = aaDRecursiveSanitizeTextField($value);
            } else {
                if (preg_match("/<[^<]+>/", $value, $m) !== 0) {
                    $filterParameters[$key] = sanitize_text_field($value);
                } else {
                    $filterParameters[$key] = sanitize_text_field($value);
                }
            }
        }

    }

    return $filterParameters;
}

function aaDOption($option_name, $data = [], $type = 'get', $prefix = true) {
    $option = "";

    if ($prefix) {
        $option = AWESOME_ADMIN_PREFIX;
    }

    $option = $option . $option_name;

    if ($type === "get") {
        return get_option($option);
    } elseif ($type === "update") {
        return update_option($option , $data);
    } elseif ($type === "add") {
        return add_option($option , $data);
    } else {
        return [];
    }
}