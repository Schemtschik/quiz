/**
 * @author Yevgeny Shemchik (schemtschik@gmail.com)
 */

var data;

var selectedCompetition = 0;
var selectedQuiz = 0;
var selectedQuestion = 0;
var selectedTeam = 0;
var selectedScore = 0;

function addPlayerToTeam() {
    for (var i = 0; i < data.players.length; i++)
        if (data.players[i].name == $("#newMembersList").val())
            id = data.players[i].id;

    var found = false;
    for (var i = 0; i < data.competitions[selectedCompetition].teams[selectedTeam].players.length; i++)
        if (data.competitions[selectedCompetition].teams[selectedTeam].players[i] == id)
            found = true;
    if (!found)
        data.competitions[selectedCompetition].teams[selectedTeam].players.push(id);
    draw();
}

function checkData() {
	var s = $.toJSON(data);
	for (var i = 0; i < s.length; i++)
		if (s[i] == '<' || s[i] == '>')
			return false;
	return true;
}

function saveAll() {
	if (!checkData()) {
		innerAlert("Ошибка", "Использованы недопустимые символы", "danger");
		return;
	}

    $.post("api/action.php?q=save",{data:$.toJSON(data)} ,function (_data) {
        if (_data != "error") {
            loadAndDraw();
            innerAlert("Сохранено");
        } else {
            innerAlert("Ошибка: ", _data.message, "danger");
        }
    });
}

function saveCompetition(num) {
	if (!checkData()) {
		innerAlert("Ошибка", "Использованы недопустимые символы", "danger");
		return;
	}

    $.post("api/action.php?q=save&competition=" + num,{data:$.toJSON(data)} ,function (_data) {
        if (_data != "error") {
            loadAndDraw();
            innerAlert("Сохранено");
        } else {
            innerAlert("Ошибка: ", _data.message, "danger");
        }
    });
}

function saveQuiz(num) {
	if (!checkData()) {
		innerAlert("Ошибка", "Использованы недопустимые символы", "danger");
		return;
	}

    $.post("api/action.php?q=save&competition=" + selectedCompetition + "&quiz=" + num,{data:$.toJSON(data)} ,function (_data) {
        if (_data != "error") {
            loadAndDraw();
            innerAlert("Сохранено");
        } else {
            innerAlert("Ошибка: ", _data.message, "danger");
        }
    });
}

function saveQuestion(num) {
	if (!checkData()) {
		innerAlert("Ошибка", "Использованы недопустимые символы", "danger");
		return;
	}

    $.post("api/action.php?q=save&competition=" + selectedCompetition + "&quiz=" + selectedQuiz + "&question=" + num,{data:$.toJSON(data)} ,function (_data) {
        if (_data != "error") {
            loadAndDraw();
            innerAlert("Сохранено");
        } else {
            innerAlert("Ошибка: ", _data.message, "danger");
        }
    });
}

function selectCompetition(num) {
    selectedCompetition = num;
    selectedQuiz = 0;
    selectedQuestion = 0;
    selectedScore = 0;
    selectedTeam = 0;
    draw();
}

function selectQuiz(num) {
    selectedQuiz = num;
    selectedQuestion = 0;
    selectedScore = 0;
    draw();
}

function selectQuestion(num) {
    selectedQuestion = num;
    draw();
}

function selectTeam(num) {
    selectedTeam = num;
    draw();
}

function selectScore(num) {
    selectedScore = num;
    draw();
}

function newCompetition() {
    data.competitions.push({
        id: data.nextCompetition,
        name: "Название соревнования",
        description: "Описание соревнования",
        quizzes:[],
        teams:[]
    });
    data.nextCompetition++;
    draw();
}

function deleteCompetition(id) {
    $.getJSON("api/action.php?q=delete&competition=" + id, function (_data) {
        if (_data != "error") {
            loadAndDraw();
            selectCompetition(0);
            innerAlert("Соревнование удалено");
        } else {
            innerAlert("Ошибка: ", _data.message, "danger");
        }
    });
}

function newTeam() {
    data.competitions[selectedCompetition].teams.push({
        id:(data.competitions[selectedCompetition].teams.length == 0 ? 0 : data.competitions[selectedCompetition].teams[data.competitions[selectedCompetition].teams.length - 1].id + 1),
        name:"Название команды",
        players:[]
    });
    draw();
}

function deleteTeam(num) {
    data.competitions[selectedCompetition].teams.splice(num, num + 1);
    draw();
}

function deleteFromTeam(num) {
    data.competitions[selectedCompetition].teams[selectedTeam].players.splice(num, num + 1);
    draw();
}

function newQuiz() {
    data.competitions[selectedCompetition].quizzes.push({
        id: (data.competitions[selectedCompetition].quizzes.length == 0 ? 0 : data.competitions[selectedCompetition].quizzes[data.competitions[selectedCompetition].quizzes.length - 1].id + 1),
        name:"Название блока",
        title:"Заголовок блока",
        questions:[]
    });
    draw();
}

function deleteQuiz(num) {
    data.competitions[selectedCompetition].quizzes.splice(num, num + 1);
    draw();
}

function newScore() {
    data.competitions[selectedCompetition].quizzes[selectedQuiz].questions[selectedQuestion].scores.push({
        title:"Название подзадачи",
        score:1
    });
    draw();
}

function deleteScore(num) {
    data.competitions[selectedCompetition].quizzes[selectedQuiz].questions[selectedQuestion].scores.splice(num, num + 1);
    draw();
}

function newQuestion() {
    var questions = data.competitions[selectedCompetition].quizzes[selectedQuiz].questions;
    if (questions.length == 0) {
        data.competitions[selectedCompetition].quizzes[selectedQuiz].questions.push({
            id: 0,
            text: "Текст вопроса",
            answer: "Ответ",
            music: "",
            music_time: "",
            photo: "",
            scores: [
                {
                    title: "Правильный ответ",
                    score: 1
                }
            ]
        });
    } else {
        var tmp = {};
        Object.assign(tmp, questions[questions.length - 1]);
        tmp.id++;
        data.competitions[selectedCompetition].quizzes[selectedQuiz].questions.push(tmp);
    }
    draw();
}

function deleteQuestion(num) {
    data.competitions[selectedCompetition].quizzes[selectedQuiz].questions.splice(num, num + 1);
    draw();
}

function newPlayer() {
    data.players.push({
        id:data.nextPlayer,
        name:"Имя пользователя"
    });
    data.nextPlayer++;
    draw();
}

function deletePlayer(num) {
    data.players.splice(num, num + 1);
    draw();
}

function updateTest() {
    var time = data.competitions[selectedCompetition].quizzes[selectedQuiz].questions[selectedQuestion].music_time;
    if (time != null && time != '')
        var timing = '#t=' + time.replace("-", ",");
    else
        var timing = '';
    var musicLink = "data/competitions/" + data.competitions[selectedCompetition].id + "/media/" + data.competitions[selectedCompetition].quizzes[selectedQuiz].questions[selectedQuestion].music;
    $("#questionTest").html('<audio volume=0 onplay="onPlay()" src="' + musicLink + timing + '" controls id="mAudio1"></audio>');
    if (data.competitions[selectedCompetition].quizzes[selectedQuiz].questions[selectedQuestion].music != null && data.competitions[selectedCompetition].quizzes[selectedQuiz].questions[selectedQuestion].music != "")
        $("#runMusic").html('<audio volume=0  onplay="onPlay()" src="' + musicLink + timing + '" controls id="mAudio2"></audio>');
    else
        $("#runMusic").html('');

    if (data.competitions[selectedCompetition].quizzes[selectedQuiz].questions[selectedQuestion].photo != "") {
        $("#photoTest").html("<img src='data/competitions/" + data.competitions[selectedCompetition].id + "/media/" + data.competitions[selectedCompetition].quizzes[selectedQuiz].questions[selectedQuestion].photo + "' width='200px'>")
        $("#runPhoto").html("<img src='data/competitions/" + data.competitions[selectedCompetition].id + "/media/" + data.competitions[selectedCompetition].quizzes[selectedQuiz].questions[selectedQuestion].photo + "' width='100%'>")
    } else {
        $("#photoTest").html("");
        $("#runPhoto").html("");
    }
}

function next() {
    if (selectedQuiz == data.competitions[selectedCompetition].quizzes.length - 1 && selectedQuestion == data.competitions[selectedCompetition].quizzes[selectedQuiz].questions.length - 1)
        return;

    selectedQuestion++;
    if (selectedQuestion >= data.competitions[selectedCompetition].quizzes[selectedQuiz].questions.length) {
        selectedQuestion = 0;
        selectedQuiz++;
    }

    draw();
}

function prev() {
    if (selectedQuiz == 0 && selectedQuestion == 0)
        return;

    selectedQuestion--;
    if (selectedQuestion < 0) {
        selectedQuiz--;
        selectedQuestion = data.competitions[selectedCompetition].quizzes[selectedQuiz].questions.length - 1;
    }

    draw();
}

function showAnswer() {
    $("#runAnswer").html(data.competitions[selectedCompetition].quizzes[selectedQuiz].questions[selectedQuestion].answer);
}

function sendAnswer(event) {
    if (event.keyCode != 13)
        return;

    var res = {};
    var str = $("#command").val();
    res.timestamp = new Date().getTime();
    res.team = parseInt(str);
    var i = 0;
    while (str[i] >= '0' && str[i] <= '9')
        i++;
    str = str.substr(i);
    res.results = [];
    for (i = 0; i < str.length; i++)
        res.results.push(str[i] == 'y' ? 1 : 0);

    if ($("#variables").val() != "")
        str = $("#variables").val().split('\n');
    for (i = 0; i < str.length; i++)
        res[str[i].split("=")[0]] = str[i].split("=")[1];

    var url = "api/action.php?q=addAnswer&competition=" + data.competitions[selectedCompetition].id;
    url += "&quiz=" + data.competitions[selectedCompetition].quizzes[selectedQuiz].id;
    url += "&question=" + data.competitions[selectedCompetition].quizzes[selectedQuiz].questions[selectedQuestion].id;
    url += "&data=" + $.toJSON(res);

    $.getJSON(url, function (_data) {
        if (_data != "error") {
            drawResults();
        } else {
            innerAlert("Ошибка: ", _data.message, "danger");
        }
    });

    $("#command").val('');
}

function drawResults() {
    if (selectedCompetition < data.competitions.length) {
        var competition = data.competitions[selectedCompetition];
        if (selectedQuiz < competition.quizzes.length) {
            var quiz = competition.quizzes[selectedQuiz];
            if (selectedQuestion < quiz.questions.length) {
                var question = quiz.questions[selectedQuestion];
                $.getJSON("api/action.php?q=getResults&competition=" + competition.id, function (_data) {

                    var text = "<thead> <tr><th>Команда</th>";
                    for (var i = 0; i < question.scores.length; i++)
                        text += "<th>" + question.scores[i].title + "</th>"
                    text += "</tr></thead> <tbody>";

                    if ( _data[quiz.id] != null && _data[quiz.id][question.id] != null) {
                        var arr = _data[quiz.id][question.id];
                        arr.sort(function(a, b) {
                            return (b.timestamp - a.timestamp);
                        });
                        for (var i = 0; i < arr.length; i++) {
                            var j = 0;
                            for (j = 0; data.competitions[selectedCompetition].teams[j].id != arr[i].team; j++) {}
                            text += "<tr><td>" + competition.teams[j].name + "</td>";
                            for (j = 0; j < arr[i].results.length; j++)
                                text += "<td class='" + (arr[i].results[j] == 1 ? "success" : "danger") + "'>" + (arr[i].results[j] == 1 ? "+" : "-") + "</td>"
                            text += "</tr>";
                        }
                    }

                    text += "</tbody>";
                    $("#answersTable").html(text);
                });

            }
        }

        $.getJSON("api/action.php?q=getTable&competition=" + competition.id, function (_data) {
            if (_data != "error") {
                var table = _data.lines;
                table.sort(function(a, b) {
                    return (b[b.length - 1] - a[a.length - 1]);
                });
                var text = "<thead> <tr><th>Команда</th>";
                for (var i = 0; i < competition.quizzes.length; i++)
                    text += "<th>" + competition.quizzes[i].title + "</th>"
                text += "<th>Итого</th></tr></thead> <tbody>";

                for (var i = 0; i < table.length; i++) {
                    text += "<tr>";
                    for (var j = 0; j < table[i].length; j++)
                        text += "<td>" + table[i][j] + "</td>";
                    text += "</tr>";
                }

                text += "</tbody>";
                $("#resultsTable").html(text);
            } else {
                innerAlert("Ошибка: ", _data.message, "danger");
            }
        });
    }
}

function draw() {
    if (selectedTeam > 0 && selectedTeam >= data.competitions[selectedCompetition].teams.length)
        selectedTeam = 0;

    var playersList = "";
    for (var i = 0; i < data.players.length; i++)
        playersList += (playersList == "" ? "" : "\n") + data.players[i].name;
    $("#playersList").val(playersList);

    var competitionsList = "";
    for (var i = 0; i < data.competitions.length; i++) {
        var competition = data.competitions[i];
        competitionsList += '<div style="cursor: pointer" class="list-group-item' + (selectedCompetition == i ? " active" : "") + '" onclick="selectCompetition(' + i + ')">';
        competitionsList += '<h4 class="list-group-item-heading">' + competition.id + '.' + competition.name + '</h4>';
        competitionsList += '<p class="list-group-item-text">' + competition.description + '</p>';
        competitionsList += '<br><div type="submit" class="btn btn-success" onclick="saveCompetition(' + data.competitions[i].id + ')">Сохранить</div> ';
        competitionsList += '<div type="submit" class="btn btn-danger" onclick="deleteCompetition(' + data.competitions[i].id + ')">Удалить</div></div>';
    }
    $("#competitionsList").html(competitionsList);

    if (selectedQuiz > 0 && selectedQuiz >= data.competitions[selectedCompetition].quizzes.length)
        selectedQuiz = 0;

    var quizzesList = "";
    for (var i = 0; i < data.competitions[selectedCompetition].quizzes.length; i++) {
        var quiz = data.competitions[selectedCompetition].quizzes[i];
        quizzesList += '<div style="cursor: pointer" class="list-group-item' + (selectedQuiz == i ? " active" : "") + '" onclick="selectQuiz(' + i + ')">';
        quizzesList += '<h4 class="list-group-item-heading">' + quiz.name + '</h4>';
        quizzesList += '<p class="list-group-item-text">' + quiz.title + '</p>';
        quizzesList += '<br><div type="submit" class="btn btn-success" onclick="saveQuiz(' + i + ')">Сохранить</div>';
        quizzesList += ' <div type="submit" class="btn btn-danger" onclick="deleteQuiz(' + i + ')">Удалить</div></div>';
    }
    $("#quizzesList").html(quizzesList);

    $("#competitionName").val(data.competitions[selectedCompetition].name);
    $("#competitionDescription").val(data.competitions[selectedCompetition].description);

    var teamsList = "";
    for (var i = 0; i < data.competitions[selectedCompetition].teams.length; i++) {
        var team = data.competitions[selectedCompetition].teams[i];
        teamsList += '<div style="cursor: pointer" class="list-group-item' + (selectedTeam == i ? " active" : "") + '" onclick="selectTeam(' + i + ')">';
        teamsList += '<h4 class="list-group-item-heading">' + (i) + '.' + team.name + '</h4>';
        var players = [];
        for (var j = 0; j < team.players.length; j++)
            for (var k = 0; k < data.players.length; k++)
                if (data.players[k].id == team.players[j])
                    players.push(data.players[k].name);
        teamsList += '<p class="list-group-item-text">' + players.join(", ") + '</p>';
        teamsList += '<div type="submit" class="btn btn-danger" onclick="deleteTeam(' + i + ')">Удалить</div></div>';
    }
    $("#teamsList").html(teamsList);

    var membersList = "";
    if (selectedTeam < data.competitions[selectedCompetition].teams.length) {
        for (var i = 0; i < data.competitions[selectedCompetition].teams[selectedTeam].players.length; i++)
            for (var j = 0; j < data.players.length; j++)
                if (data.competitions[selectedCompetition].teams[selectedTeam].players[i] == data.players[j].id)
                    membersList += '<li>' + data.players[j].name + ' (<a onclick="deleteFromTeam(' + i + ')">Удалить</a>)</li>';
    }
    $("#membersList").html(membersList);

    var newMembersList = "";
    for (var i = 0; i < data.players.length; i++)
        newMembersList += '<option onchange="addPlayerToTeam(' + data.players[i].id + ')">' + data.players[i].name + '</option>';
    $("#newMembersList").html(newMembersList);

    if (selectedTeam < data.competitions[selectedCompetition].teams.length)
        $("#teamName").val(data.competitions[selectedCompetition].teams[selectedTeam].name);

    if (selectedQuiz < data.competitions[selectedCompetition].quizzes.length) {

        var text = "";
        for (var i = 0; i < data.competitions[selectedCompetition].quizzes.length; i++)
            text += "<option value=" + i + ">" + data.competitions[selectedCompetition].quizzes[i].name + "</option>"
        $("#runQuiz").html(text);
        $("#runQuiz").val(selectedQuiz);

        $("#runQuizTitle").html(data.competitions[selectedCompetition].quizzes[selectedQuiz].title);

        if (selectedQuestion > 0 && selectedQuestion >= data.competitions[selectedCompetition].quizzes[selectedQuiz].questions.length)
            selectedQuestion = 0;

        var questionsList = "";
        for (var i = 0; i < data.competitions[selectedCompetition].quizzes[selectedQuiz].questions.length; i++) {
            var question = data.competitions[selectedCompetition].quizzes[selectedQuiz].questions[i];
            questionsList += '<div style="cursor: pointer" class="list-group-item' + (selectedQuestion == i ? " active" : "") + '" onclick="selectQuestion(' + i + ')">';
            questionsList += '<h4 class="list-group-item-heading">' + question.text + '</h4>';
            questionsList += '<p class="list-group-item-text">' + question.answer + '</p>';
            questionsList += '<br><div type="submit" class="btn btn-success" onclick="saveQuestion(' + i + ')">Сохранить</div>';
            questionsList += ' <div type="submit" class="btn btn-danger" onclick="deleteQuestion(' + i + ')">Удалить</div></div>';
        }
        $("#questionsList").html(questionsList);

        $("#quizzName").val(data.competitions[selectedCompetition].quizzes[selectedQuiz].name);
        $("#quizzTitle").val(data.competitions[selectedCompetition].quizzes[selectedQuiz].title);

        $.getJSON("api/action.php?q=getValuers", function (_data) {
            if (_data.status == "ok") {
                var text = "";
                for (var i = 0; i < _data.valuers.length; i++)
                    text += "<option>" + _data.valuers[i] + "</option>";
                $("#quizValuer").html(text);
                $("#quizValuer").val(data.competitions[selectedCompetition].quizzes[selectedQuiz].valuer);
            } else {
                innerAlert("Ошибка: ", _data.message, "danger");
            }
        });

        if (selectedQuestion < data.competitions[selectedCompetition].quizzes[selectedQuiz].questions.length) {
            var text = "";
            for (var i = 0; i < data.competitions[selectedCompetition].quizzes[selectedQuiz].questions.length; i++)
                text += "<option value=" + i + ">" + data.competitions[selectedCompetition].quizzes[selectedQuiz].questions[i].text + "</option>"
            $("#runQuestion").html(text);
            $("#runQuestion").val(selectedQuestion);

            $("#questionText").val(data.competitions[selectedCompetition].quizzes[selectedQuiz].questions[selectedQuestion].text);
            $("#runQuestionText").html(data.competitions[selectedCompetition].quizzes[selectedQuiz].questions[selectedQuestion].text)
            $("#questionAnswer").val(data.competitions[selectedCompetition].quizzes[selectedQuiz].questions[selectedQuestion].answer);
            $("#runQuestionAnswer").html('<div id="runAnswer" onclick="showAnswer()">(нажмите, чтобы увидеть ответ)</div>');
            $("#runQuestionAnswer2").html('Ответ: ' + data.competitions[selectedCompetition].quizzes[selectedQuiz].questions[selectedQuestion].answer);
            $("#questionTime").val(data.competitions[selectedCompetition].quizzes[selectedQuiz].questions[selectedQuestion].music_time);
            $("#questionMusic").val(data.competitions[selectedCompetition].quizzes[selectedQuiz].questions[selectedQuestion].music);
            $("#questionPhoto").val(data.competitions[selectedCompetition].quizzes[selectedQuiz].questions[selectedQuestion].photo);

            updateTest();

            var text = "Подзадачи: ";
            for (var i = 0; i < data.competitions[selectedCompetition].quizzes[selectedQuiz].questions[selectedQuestion].scores.length; i++)
                text += ' ' + data.competitions[selectedCompetition].quizzes[selectedQuiz].questions[selectedQuestion].scores[i].title + '<span class="badge">' + data.competitions[selectedCompetition].quizzes[selectedQuiz].questions[selectedQuestion].scores[i].score + '</span>';
            $("#subtasksList").html(text);

            var scoresList = "";
            for (var i = 0; i < data.competitions[selectedCompetition].quizzes[selectedQuiz].questions[selectedQuestion].scores.length; i++) {
                var score = data.competitions[selectedCompetition].quizzes[selectedQuiz].questions[selectedQuestion].scores[i];
                scoresList += '<li style="cursor: pointer" class="list-group-item ' + (i == selectedScore ? "active" : "") + '" onclick="selectScore(' + i + ')"><span class="badge">' + score.score + '</span>' + score.title + ' <a class="btn btn-sm btn-danger" onclick="deleteScore(' + i + ')">Удалить</a></li>';
            }
            $("#scoresList").html(scoresList);

            $("#scoreTitle").val(data.competitions[selectedCompetition].quizzes[selectedQuiz].questions[selectedQuestion].scores[selectedScore].title);
            $("#scorePoints").val(data.competitions[selectedCompetition].quizzes[selectedQuiz].questions[selectedQuestion].scores[selectedScore].score);

            $.getJSON("api/action.php?q=getFiles&competition=" + data.competitions[selectedCompetition].id, function (_data) {
                if (_data.status == "ok") {
                    var text = "<option></option>";
                    for (var i = 0; i < _data.files.length; i++)
                        text += "<option>" + _data.files[i] + "</option>";
                    $("#questionMusic").html(text);
                    $("#questionPhoto").html(text);
                    $("#questionMusic").val(data.competitions[selectedCompetition].quizzes[selectedQuiz].questions[selectedQuestion].music);
                    $("#questionPhoto").val(data.competitions[selectedCompetition].quizzes[selectedQuiz].questions[selectedQuestion].photo);
                } else {
                    innerAlert("Ошибка: ", _data.message, "danger");
                }
            });
        }
    }

    var text = "";
    for (var i = 0; i < data.competitions.length; i++)
        text += "<option value=" + i + ">" + data.competitions[i].name + "</option>"
    $("#runCompetition").html(text);
    $("#runCompetition").val(selectedCompetition);

    $("#runCompetitionName").html(data.competitions[selectedCompetition].name);

    var text = "";
    for (var i = 0; i < data.players.length; i++) {
        text += '<tr><td>' + data.players[i].id;
        text += '</td><td><input class="form-control" id="player' + i + '" onchange="data.players[' + i + '].name = $(\'#player' + i + '\').val()" value="' + data.players[i].name + '"></td>';
        text += '<td><div class="btn btn-danger" onclick="deletePlayer(' + i + ')">Удалить</div></td></tr>';
    }
    $("#playersList").html(text);

    var pages = ["#players", "#competitions", "#quizzes", "#questions", "#run", "#table"];
    var r;
    if (selectedCompetition >= data.competitions.length) {
        r = 2;
    } else {
        if (selectedQuiz >= data.competitions[selectedCompetition].quizzes.length) {
            r = 3;
        } else {
            if (data.competitions[selectedCompetition].quizzes[selectedQuiz].questions.length == 0)
                r = 4;
            else
                r = 6;
        }
    }

    for (var i = 0; i < r; i++)
        $(pages[i] + "Item").show();
    for (var i = r; i < pages.length; i++)
        $(pages[i] + "Item").hide();
}

function setPage(pagename) {
    var pages = ["run", "table", "competitions", "quizzes", "questions", "players"];
    for (var i = 0; i < pages.length; i++)
        if (pages[i] == pagename) {
            $("#" + pages[i]).show();
            $("#" + pages[i] + "Item").addClass("active");
        } else {
            $("#" + pages[i]).hide();
            $("#" + pages[i] + "Item").removeClass("active");
        }
}

function loadAndDraw() {
    $.getJSON("api/action.php?q=get", function (_data) {
        if (_data.status == "ok") {
            data = _data;
            draw();
        } else {
            innerAlert("Ошибка: ", _data.message, "danger");
        }
    });
}

function innerAlert(header, message = "", type = "success") {
    var text = '<div class="row"> <div class="col-lg-12"> <br> <div class="alert alert-dismissible alert-' + type + '"> ' +
        '<button type="button" class="close" onclick="$(\'#alertMessage\').fadeOut()">&times;</button> ' +
        '<h4>' + header + '</h4> ' +
        '<p>' + message + '</p> </div> </div></div>';
    $("#alertMessage").html(text);
    $("#alertMessage").fadeIn();

    if (type == "success") {
        setTimeout(function () {
            $("#alertMessage").fadeOut();
        }, 2000);
    }
}

function onPlay() {
    $("#mAudio1")[0].volume = 0.0;
    $("#mAudio2")[0].volume = 0.0;
    $("#mAudio1").animate({volume: 1.0}, 1000);
    $("#mAudio2").animate({volume: 1.0}, 1000);
    var timeStart = Number(data.competitions[selectedCompetition].quizzes[selectedQuiz].questions[selectedQuestion].music_time.split("-")[0]);
    var timeEnd = Number(data.competitions[selectedCompetition].quizzes[selectedQuiz].questions[selectedQuestion].music_time.split("-")[1]);
    setTimeout(beforeStop, 1000 * (timeEnd - timeStart - 1.0));
}

function beforeStop() {
    $("#mAudio1").animate({volume: 0.0}, 1000);
    $("#mAudio2").animate({volume: 0.0}, 1000);
}

function filterPlayers() {
    if (event.keyCode == 13) {
        newTeam();
        selectTeam(data.competitions[selectedCompetition].teams.length - 1);
        $('#teamName').val('');
        $('#teamName').focus()
    } else {
        var filter = $("#playersFilter").val();
        var newMembersList = "";
        for (var i = 0; i < data.players.length; i++)
            if (filter.toLowerCase() == data.players[i].name.substr(0, filter.length).toLowerCase())
                newMembersList += '<option onchange="addPlayerToTeam(' + data.players[i].id + ')">' + data.players[i].name + '</option>';
        $("#newMembersList").html(newMembersList);
    }
}

function focusOnFilter(event) {
    if (event.keyCode != 13)
        return;

    $('#playersFilter').focus();
}