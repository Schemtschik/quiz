<?php
/**
 * @author Yevgeny Shemchik (schemtschik@gmail.com)
 */

fwrite(STDOUT, "Type login\n");

$login = trim(fgets(STDIN));

if (file_exists("data/users/" . $login)) {
    fwrite(STDOUT, "Login is taken\n");
    die();
}

fwrite(STDOUT, "Type password\n");

$password = trim(fgets(STDIN));

fwrite(STDOUT, "Retype password\n");

$password2 = trim(fgets(STDIN));

if ($password != $password2) {
    fwrite(STDOUT, "Different passwords\n");
    die();
}

file_put_contents("data/users/" . $login, hash('sha256', $password));

fwrite(STDOUT, "Registered\n");