<?php

/**
 * Custom blade directive for checkbox
 *
 * @return bool
 *---------------------------------------------------------------- */
Blade::directive('lwCheckboxField', function ($expression) {
    $parameters = explode(', ', $expression);
    $name = array_get($parameters, '0');
    $label = array_get($parameters, '1');
    $value = array_get($parameters, '2');
    $id = array_get($parameters, '3');
    $checkString = ($value == 'true') ? 'checked' : '';

    return <<<EOL
    <?php echo "<input type='hidden' name=e($name) value='false'>" ?>
    <?php echo "<div class='custom-control custom-checkbox custom-control-inline'>"; ?>
    <?php echo "<input type='checkbox' name=$name value='true' class='custom-control-input' id=$id $checkString>"; ?>
    <?php echo "<label class='custom-control-label' for=$id>$label</label>"; ?>
    <?php echo "</div>"; ?>
EOL;
});
