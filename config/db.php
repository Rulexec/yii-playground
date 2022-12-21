<?php

// https://stackoverflow.com/questions/1091107/how-to-join-filesystem-path-strings-in-php
function join_paths() {
  $paths = array();

  foreach (func_get_args() as $arg) {
      if ($arg !== '') { $paths[] = $arg; }
  }

  return preg_replace('#/+#','/',join('/', $paths));
}

$path = join_paths(__DIR__, '../storage/db.sqlite');

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'sqlite:' . $path,
    'charset' => 'utf8',
];
