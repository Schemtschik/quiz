<?php
    if (isset($_POST['login']) && isset($_POST['password'])) {
        if (file_exists("data/users/" . $_POST['login']) && file_get_contents("data/users/" . $_POST['login']) == hash('sha256', $_POST['password'])) {
            $error = false;
            setcookie("login", $_POST['login']);
            setcookie("password", hash('sha256', $_POST['password']));
            echo "<script>location.href = 'index.php'</script>";
        } else {
            $error = true;
        }
    }

    if (isset($_COOKIE['login']) && isset($_COOKIE['password'])) {
        if (file_exists("data/users/" . $_COOKIE['login']) && file_get_contents("data/users/" . $_COOKIE['login']) == $_COOKIE['password']) {
            echo "<script>location.href = 'index.php'</script>";
        }
    }
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quiz</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<script src="js/jquery.min.js"></script>
<script src="js/jquery-json.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>

<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php">Quiz</a>
        </div>
</nav>

<br><br>

<div class="container">
    <br><br>
    <div class="row">
        <div class="col-lg-12">
            <div class="bs-component">
                <form class="form-horizontal" method="post" action="login.php">
                    <fieldset>
                        <legend>Вход</legend>
                        <div class="form-group">
                            <label for="login" class="col-lg-2 control-label">Логин</label>
                            <div class="col-lg-10">
                                <input type="text" class="form-control" id="login" name="login">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="password" class="col-lg-2 control-label">Пароль</label>
                            <div class="col-lg-10">
                                <input type="password" class="form-control" name="password" id="password">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="password" class="col-lg-2 control-label"></label>
                            <div class="col-lg-10">
                                <button type="submit" class="btn btn-primary">Войти</button>
                            </div>
                        </div>

                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    <?php if ($error) echo 'alert("Неправильные логин/пароль")' ?>
</script>

</body>
</html>

