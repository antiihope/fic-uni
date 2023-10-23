<?php


function attrs_to_string($attrs)
{
    $string = '';
    foreach ($attrs as $key => $value) {
        $string .= " $key=\"$value\"";
    }
    return $string;
}

function html_out(
    $type,
    $attrs = [],
    $content = ''
) {

    $attrs_string = attrs_to_string($attrs);
    if (empty($content)) {
        return "<$type>";
    }

    echo "<$type " . $attrs_string . ">$content</$type>";
}

function get_html(
    $type,
    $attrs = [],
    $content = ''
) {
    $attrs_string = attrs_to_string($attrs);
    if ($content == '') {
        return "<$type />";
    }
    return "<$type " . $attrs_string . ">$content</$type>";
}


// html_out("h2", [], "Hello World"); // echos <h2>Hello World</h2>
// get_html("h2", [], "Hello World"); // returns <h2>Hello World</h2>
