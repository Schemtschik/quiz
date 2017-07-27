<?php
    session_start();
    if (!isset($_SESSION['login']))
        die("<script>location.href = 'login.php'</script>");
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
          <div id="navbar" class="navbar-collapse collapse">
              <ul class="nav navbar-nav">
                  <li id="competitionsItem"><a href="#competitions" onclick="setPage('competitions')">Соревнования</a></li>
                  <li id="quizzesItem"><a href="#quizzes" onclick="setPage('quizzes')">Блоки</a></li>
                  <li id="questionsItem"><a href="#questions" onclick="setPage('questions')">Вопросы</a></li>
                  <li id="runItem"><a href="#run" onclick="setPage('run');">Запуск</a></li>
                  <li id="tableItem"><a href="#table" onclick="setPage('table')">Таблица</a></li>
                  <li id="playersItem"><a href="#players" onclick="setPage('players')">Игроки</a></li>
              </ul>
              <form class="navbar-form navbar-left" role="search">
                <div type="submit" class="btn btn-default" onclick="saveAll()">Сохранить всё</div>
                  <div type="submit" class="btn btn-default" onclick="loadAndDraw()">Перезагрузить всё</div>
                  <div type="submit" class="btn btn-default" onclick='location.href = "login.php?logout=true";'>Выйти</div>
              </form>
          </div>
      </div>
  </nav>

  <br><br>

  <div class="container" id="alertMessage" hidden></div>

  <div class="container" id="competitions">
      <div class="row">
          <div class="col-lg-12">
              <div class="page-header">
                  <h1>Соревнования</h1>
              </div>
          </div>
      </div>
      <div class="row">
          <div class="col-lg-4">
              <div class="bs-component" align="center">
                  <div class="list-group" id="competitionsList"></div>
                  <div class="btn btn-primary btn-lg" onclick="newCompetition()">Добавить</div>
              </div>
          </div>
          <div class="col-lg-8">
              <div class="bs-component">
                  <form class="form-horizontal">
                      <fieldset>
                          <legend>Информация о соревновании</legend>
                          <div class="form-group">
                              <label for="competitionName" class="col-lg-2 control-label">Название</label>
                              <div class="col-lg-10">
                                  <input type="text" class="form-control" id="competitionName" placeholder="Competition Name" onchange='data.competitions[selectedCompetition].name = $("#competitionName").val()'>
                              </div>
                          </div>
                          <div class="form-group">
                              <label for="competitionDescription" class="col-lg-2 control-label">Описание</label>
                              <div class="col-lg-10">
                                  <textarea class="form-control" rows="3" id="competitionDescription" onchange='data.competitions[selectedCompetition].description = $("#competitionDescription").val()'></textarea>
                              </div>
                          </div>
                          <div class="form-group">
                              <label class="col-lg-2 control-label">Команды</label>
                              <div class="col-lg-10">
                                  <div class="bs-component" align="center">
                                      <div class="list-group" id="teamsList"></div>
                                      <div class="btn btn-primary btn-lg" onclick="newTeam()">Добавить</div>
                                  </div>
                                  <br>
                                  <div class="panel panel-primary">
                                      <div class="panel-heading">
                                          <h3 class="panel-title">Информация о команде</h3>
                                      </div>
                                      <div class="panel-body">
                                          <form class="form-horizontal">
                                              <fieldset>
                                                  <div class="form-group">
                                                      <label for="teamName" class="col-lg-2 control-label">Название</label>
                                                      <div class="col-lg-10">
                                                          <input type="text" class="form-control" id="teamName" placeholder="Team Name" onkeypress="focusOnFilter(event)" onchange='data.competitions[selectedCompetition].teams[selectedTeam].name = $("#teamName").val()'>
                                                      </div>
                                                  </div>
                                                  <div class="form-group">
                                                      <label class="col-lg-2 control-label">Участники</label>
                                                      <div class="col-lg-10">
                                                          <ul id="membersList"></ul>
                                                      </div>
                                                  </div>
                                                  <div class="form-group">
                                                      <label class="col-lg-2 control-label">Добавить участника (начните вводить фамилию)</label>
                                                      <div class="col-lg-10">
                                                          <input multiple="" class="form-control" id="playersFilter" onkeypress="filterPlayers()">
                                                          <select multiple="" class="form-control" id="newMembersList" onchange="addPlayerToTeam(); $('#playersFilter').focus()"></select>
                                                      </div>
                                                  </div>
                                              </fieldset>
                                          </form>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </fieldset>
                  </form>
              </div>
          </div>
      </div>
  </div>

  <div class="container" id="quizzes">
      <div class="row">
          <div class="col-lg-12">
              <div class="page-header">
                  <h1>Блоки</h1>
              </div>
          </div>
      </div>
      <div class="row">
          <div class="col-lg-4">
              <div class="bs-component" align="center">
                  <div class="list-group" id="quizzesList"></div>
                  <div class="btn btn-primary btn-lg" onclick="newQuiz()">Добавить</div>
              </div>
          </div>
          <div class="col-lg-8">
              <div class="bs-component">
                  <form class="form-horizontal">
                      <fieldset>
                          <legend>Информация о блоке</legend>
                          <div class="form-group">
                              <label for="quizzName" class="col-lg-2 control-label">Название</label>
                              <div class="col-lg-10">
                                  <input type="text" class="form-control" id="quizzName" placeholder="Quiz Name" onchange='data.competitions[selectedCompetition].quizzes[selectedQuiz].name = $("#quizzName").val()'>
                              </div>
                          </div>
                          <div class="form-group">
                              <label for="quizzTitle" class="col-lg-2 control-label">Заголовок</label>
                              <div class="col-lg-10">
                                  <input type="text" class="form-control" id="quizzTitle" placeholder="Quiz Title" onchange='data.competitions[selectedCompetition].quizzes[selectedQuiz].title = $("#quizzTitle").val()'>
                              </div>
                          </div>
                          <div class="form-group">
                              <label for="quizValuer" class="col-lg-2 control-label">Оценивающий скрипт</label>
                              <div class="col-lg-10">
                                  <select class="form-control" id="quizValuer" onchange='data.competitions[selectedCompetition].quizzes[selectedQuiz].valuer = $("#quizValuer").val()'>
                                  </select>
                              </div>
                          </div>
                      </fieldset>
                  </form>
              </div>
          </div>
      </div>
  </div>

  <div class="container" id="questions">
      <div class="row">
          <div class="col-lg-12">
              <div class="page-header">
                  <h1>Вопросы</h1>
              </div>
          </div>
      </div>
      <div class="row">
          <div class="col-lg-4">
              <div class="bs-component" align="center">
                  <div class="list-group" id="questionsList"></div>
                  <div class="btn btn-primary btn-lg" onclick="newQuestion()">Добавить</div>
              </div>
          </div>
          <div class="col-lg-8">
              <div class="bs-component">
                  <form class="form-horizontal">
                      <fieldset>
                          <legend>Информация о вопросе</legend>
                          <div class="form-group">
                              <div class="form-group" id="questionTextBlock">
                                  <label for="questionText" class="col-lg-2 control-label">Текст</label>
                                  <div class="col-lg-10">
                                      <input type="text" class="form-control" id="questionText" placeholder="Question Text" onchange='data.competitions[selectedCompetition].quizzes[selectedQuiz].questions[selectedQuestion].text = $("#questionText").val()'>
                                  </div>
                              </div>
                              <div class="form-group">
                                  <label for="questionAnswer" class="col-lg-2 control-label">Ответ</label>
                                  <div class="col-lg-10">
                                      <input type="text" class="form-control" id="questionAnswer" placeholder="Answer" onchange='data.competitions[selectedCompetition].quizzes[selectedQuiz].questions[selectedQuestion].answer = $("#questionAnswer").val()'>
                                  </div>
                              </div>
                              <div class="form-group" id="questionMusicBlock">
                                  <label for="questionMusic" class="col-lg-2 control-label">Музыкальный файл</label>
                                  <div class="col-lg-10">
                                      <select class="form-control" id="questionMusic" onchange='data.competitions[selectedCompetition].quizzes[selectedQuiz].questions[selectedQuestion].music = $("#questionMusic").val(); updateTest();'>
                                      </select>
                                  </div>
                              </div>
                              <div class="form-group" id="questionTimeBlock">
                                  <label for="questionTime" class="col-lg-2 control-label">Границы проигрыша (в секундах)</label>
                                  <div class="col-lg-10">
                                      <input type="text" class="form-control" id="questionTime" placeholder="5.5-10" onchange='data.competitions[selectedCompetition].quizzes[selectedQuiz].questions[selectedQuestion].music_time = $("#questionTime").val(); updateTest();'>
                                  </div>
                              </div>
                              <div class="form-group" id="questionTestBlock">
                                  <label class="col-lg-2 control-label">Тест музыкального файла</label>
                                  <div class="col-lg-10" id="questionTest"></div>
                              </div>
                              <div class="form-group" id="questionPhotoBlock">
                                  <label for="questionPhoto" class="col-lg-2 control-label">Фото</label>
                                  <div class="col-lg-10">
                                      <select type="text" class="form-control" id="questionPhoto" onchange='data.competitions[selectedCompetition].quizzes[selectedQuiz].questions[selectedQuestion].photo = $("#questionPhoto").val(); updateTest();'>
                                      </select>
                                  </div>
                              </div>
                              <div class="form-group" id="questionTestPhotoBlock">
                                  <label for="questionPhoto" class="col-lg-2 control-label">Тест фото</label>
                                  <div class="col-lg-10" id="photoTest">
                                  </div>
                              </div>
                              <div class="form-group">
                                  <label class="col-lg-2 control-label">Подзадачи</label>
                                  <div class="col-lg-10">
                                      <div class="bs-component" align="center">
                                          <ul class="list-group" id="scoresList"></ul>
                                          <div class="btn btn-primary btn-lg" onclick="newScore()">Добавить</div>
                                      </div>
                                      <br>
                                      <div class="panel panel-primary">
                                          <div class="panel-heading">
                                              <h3 class="panel-title">Информация о подзадаче</h3>
                                          </div>
                                          <div class="panel-body">
                                              <form class="form-horizontal">
                                                  <fieldset>
                                                      <div class="form-group">
                                                          <label for="teamName" class="col-lg-2 control-label">Название</label>
                                                          <div class="col-lg-10">
                                                              <input type="text" class="form-control" id="scoreTitle" placeholder="Score Title" onchange='data.competitions[selectedCompetition].quizzes[selectedQuiz].questions[selectedQuestion].scores[selectedScore].title = $("#scoreTitle").val()'>
                                                          </div>
                                                      </div>
                                                      <div class="form-group">
                                                          <label for="teamName" class="col-lg-2 control-label">Стоимость</label>
                                                          <div class="col-lg-10">
                                                              <input type="text" class="form-control" id="scorePoints" placeholder="Score Points" onchange='data.competitions[selectedCompetition].quizzes[selectedQuiz].questions[selectedQuestion].scores[selectedScore].score = $("#scorePoints").val()'>
                                                          </div>
                                                      </div>
                                                  </fieldset>
                                              </form>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </fieldset>
                  </form>
              </div>
          </div>
      </div>
  </div>

  <div class="container" id="players">
      <div class="row">
          <div class="col-lg-12">
              <div class="page-header">
                  <h1>Игроки</h1>
              </div>
          </div>
      </div>
      <div class="row">
          <div class="col-lg-12">
              <table>
                  <thead>
                  <tr><th>ID</th><th>Имя</th><th></th></tr>
                  </thead>
                  <tbody id="playersList">
                  </tbody>
              </table>
              <br>
              <div class="btn btn-primary" onclick="newPlayer()">Добавить</div>
          </div>
      </div>
  </div>

  <div id="run">
      <div class="container">
          <div class="row">
              <br><br>
              <div class="col-lg-3">
                  <select class="form-control" id="runCompetition" onchange='selectCompetition(Number($("#runCompetition").val()))'></select>
              </div>
              <div class="col-lg-3">
                  <select class="form-control" id="runQuiz" onchange='selectQuiz(Number($("#runQuiz").val()))'></select>
              </div>
              <div class="col-lg-3">
                  <select class="form-control" id="runQuestion" onchange='selectQuestion(Number($("#runQuestion").val()))'></select>
              </div>
              <div class="col-lg-3">
                <div class="btn btn-primary btn-lg" onclick="prev()">Назад</div>
                <div class="btn btn-primary btn-lg" onclick="next()">Далее</div>
              </div>
          </div>
          <div class="row">
              <div class="col-lg-12">
                  <div class="page-header">
                      <h3 id="runCompetitionName"></h3>
                      <h4 id="runQuizTitle"></h4>
                  </div>
              </div>
          </div>
          <div class="row">
              <div class="col-lg-12">
                  <div class="bs-component">
                      <div class="panel panel-primary" id="questionPanel">
                          <div class="panel-heading">
                              <h1 class="panel-title">Вопрос (<a onclick="$('#questionPanel').hide(); $('#answersPanel').show();">Переключиться на запись ответов</a>)</h1>
                          </div>
                          <div class="panel-body">
                              <h2 id="runQuestionText"></h2>
                              <h3 id="runQuestionAnswer"></h3>
                              <div class="col-lg-10" id="runMusic"></div>
                              <div class="col-lg-10" id="runPhoto"></div>
                          </div>
                      </div>
                      <div class="panel panel-primary" id="answersPanel" hidden>
                          <div class="panel-heading">
                              <h1 class="panel-title">Запись ответов (<a onclick="$('#answersPanel').hide(); $('#questionPanel').show();">Переключиться на показ вопросов</a>)</h1>
                          </div>
                          <div class="panel-body">
                              <div class="row">
                                  <div class="col-lg-12">
                                      <h3 id="runQuestionAnswer2"></h3>
                                      <h4 id="subtasksList"></h4>
                                  </div>
                              </div>
                              <div class="row">
                                  <div class="col-lg-6">
                                      <h4>Записанные ответы</h4>
                                      <table class="table table-striped table-hover" id="answersTable"></table>
                                  </div>
                                  <div class="col-lg-6 well">
                                      <h4>Записать ответ</h4>

                                      <div class="form-group">
                                          <label for="variables" class="col-lg-2 control-label">Переменные</label>
                                          <div class="col-lg-10">
                                              <textarea class="form-control" rows="3" id="variables" placeholder="variable=value"></textarea>
                                              <span class="help-block">Установите значения необходимых переменных (например "round=1") по одной в строке</span>
                                          </div>
                                      </div>
                                      <div class="form-group">
                                          <label for="command" class="col-lg-2 control-label">Команда</label>
                                          <div class="col-lg-10">
                                              <input type="text" class="form-control" id="command" onkeypress="sendAnswer(event)">
                                              <div class="help-block">
                                              <p>Введите без пробелов: номер команды, последовательность символов "y" и "n" для каждой подзадачи, где "y" означает правильный ответ, "n" - неправильный</p>
                                              <p>Приме: <small>1ynn</small></p>
                                              <p>Для отправки нажмите enter</p>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
              </div>
          </div>
      </div>
  </div>

  </div>

  <div id="table">
      <br><br>
      <div class="container">
          <div class="row">
              <div class="col-lg-12">
                  <table class="table table-bordered" id="resultsTable"></table>
              </div>
          </div>
      </div>
  </div>

  <script>
      var s = window.location.href.split("#")[1];
      setPage((s == null || s == "") ? "competitions" : s);
      loadAndDraw();
      setInterval(drawResults, 10000);
  </script>

  </body>
</html>

