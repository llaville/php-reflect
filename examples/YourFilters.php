<?php
$closure = function ($data) {
    $filterOnKeys = array(
        'namespaces',
        'interfaces',
        'traits',
        'classes', 'abstractClasses', 'concreteClasses',
        'functions', 'namedFunctions', 'anonymousFunctions',
        'classConstants', 'globalConstants', 'magicConstants',
    );

    foreach ($data as $title => &$keys) {
        if (strpos($title, 'StructureAnalyser') === false) {
            continue;
        }
        // looking into Structure Analyser metrics only
        foreach ($keys as $key => $val) {
            if (!in_array($key, $filterOnKeys)) {
                unset($keys[$key]);  // "removed" unsolicited values
                continue;
            }
        }
    }
    return $data;
};

return $closure;
