<?php

define('ROOT_DIR', realpath(__DIR__.'/..').'/');

define('CORE_DIR', realpath(__DIR__).'/');
// HOST_DIR : site/host_xx définit dans index.php

define('RRIVATE_REL_DIR', '');
define('CLASS_REL_DIR', 'class/');
define('STORAGE_REL_DIR', 'storage/');
define('TEMPLATE_REL_DIR', 'template/');
define('SQL_REL_DIR', 'sql/');

make_constants('TYPE_', ['NULL', 'PAGE', 'AJAX']);

make_constants('HOST_', ['STRING', 'JOKER', 'REGEX']);

make_constants('S_', ['OFTEN', 'SOMETIMES', 'RARELY']);

make_flags('SQL_', ['CALC_ROWS']);

?>