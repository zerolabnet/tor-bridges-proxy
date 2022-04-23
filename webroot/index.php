<?php
	// Простая форма авторизации
	//-----------------------------------
		session_start();

		require 'pwd.php';

		// MD5 хэш от хэша пароля с солью для проверки при входе
		$hash = md5($salt1.$password.$salt2);

		$self = $_SERVER['REQUEST_URI'];

		// Logout
		if (isset($_GET['logout'])) {
			unset($_SESSION['loggedIn_tbp']);
			header('Location: '.$_SERVER['HTTP_REFERER']);
		}

		// Если мы пришли сюда из формы
		if (isset($_POST['login'])) {
			// Проверка пароля
			if (password_verify($_POST['password'],$password)) { $_SESSION['loggedIn_tbp'] = $hash; }
			else { echo '<script>alert("Wrong password!")</script>'; }
		}

		// Если сессия не была получена и хэш не совпадает, то показываем форму входа
		if (isset($_SESSION['loggedIn_tbp']) != $hash) {
			die("
				<html>
				<head>
				<meta charset='UTF-8'>
				<meta name='viewport' content='width=device-width, initial-scale=1'>
				<title>Der Parol</title>
				<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css'>
				<style>
				*, *:before, *:after {
					box-sizing: border-box;
				}

				body {
					min-height: 100vh;
					font-family: sans-serif;
				}

				.container {
					position: absolute;
					width: 100%;
					height: 100%;
					overflow: hidden;
				}
				.container:hover .top:before, .container:hover .top:after, .container:hover .bottom:before, .container:hover .bottom:after, .container:active .top:before, .container:active .top:after, .container:active .bottom:before, .container:active .bottom:after {
					margin-left: 200px;
					transform-origin: -200px 50%;
					transition-delay: 0s;
				}
				.container:hover .center, .container:active .center {
					opacity: 1;
					transition-delay: 0.2s;
				}

				.top:before, .top:after, .bottom:before, .bottom:after {
					content: '';
					display: block;
					position: absolute;
					width: 200vmax;
					height: 200vmax;
					top: 50%;
					left: 50%;
					margin-top: -100vmax;
					transform-origin: 0 50%;
					transition: all 0.5s cubic-bezier(0.445, 0.05, 0, 1);
					z-index: 10;
					opacity: 0.65;
					transition-delay: 0.2s;
				}

				.top:before {
					transform: rotate(45deg);
					background: #e46569;
				}
				.top:after {
					transform: rotate(135deg);
					background: #ecaf81;
				}

				.bottom:before {
					transform: rotate(-45deg);
					background: #60b8d4;
				}
				.bottom:after {
					transform: rotate(-135deg);
					background: #3745b5;
				}

				.center {
					position: absolute;
					width: 400px;
					height: 400px;
					top: 50%;
					left: 50%;
					margin-left: -200px;
					margin-top: -200px;
					display: flex;
					flex-direction: column;
					justify-content: center;
					align-items: center;
					padding: 30px;
					opacity: 0;
					transition: all 0.5s cubic-bezier(0.445, 0.05, 0, 1);
					transition-delay: 0s;
					color: #333;
				}
				.center input {
					width: 100%;
					padding: 15px;
					margin: 5px;
					border-radius: 1px;
					border: 1px solid #ccc;
					font-family: inherit;
				}
				</style>
				</head>

				<body ontouchstart=''>
				<form class='container' action = '$self' method = 'POST'>
					<div class='top'></div>
					<div class='bottom'></div>
					<div class='center'>
						<input type='password' name='password' placeholder='Der Parol' autofocus />
						<input type='submit' name='login' hidden />
					</div>
				</form>
				</body>
				</html>
			");
		}
	//-----------------------------------
	// Простая форма авторизации

	// Записываем мосты, перезапускаем Tor
	function prepare_and_apply_bridges() {
		shell_exec('/srv/bridges.sh');
	}

	// Загружаем файл -- Вызываем через AJAX для заполнения текстовой области
	if (isset($_POST['retrieve']) == "1") {
		if (isset($_POST['fileName'])) { die(file_get_contents($_POST['fileName'])); }
	}

	// Сохраняем файл -- Вызываем через AJAX функцию doSave()
	else if (isset($_POST['save']) == "1") {
		// Удаляем пробелы или другие символы из начала и конца строки
		$content = trim($_POST['content']);
		// Если есть содержимое, то добавляем перевод строки в конце файла
		if ($content != "") {
			$content = $content.PHP_EOL;
		} else { // иначе – fwrite не любит записывать пустые файлы, поэтому мы превращаем пустую строку в экранированную пустую строку
			$content = "\0";
		}

		// Если мы смогли открыть файл
		if ($file = fopen($_POST['fileName'],"w")) {
			// Записываем его
			if (fwrite($file,$content)) {
				fclose($file);
				prepare_and_apply_bridges();
				die(json_encode(["desc"=>"Done","level"=>"success"]));
			} else { die(json_encode(["desc"=>"An error occurred writing the file","level"=>"fatal"])); }
		} else { die(json_encode(["desc"=>"Could not open file","level"=>"fatal"])); }
	}

	// LOG //
	$logFile = "/var/log/tor/notices.log"; // локальный путь к файлу журнала
	$checksInterval = 1000; // как часто проверяем лог-файл на наличие изменений, минимум – 100

	// Не нужно ничего менять ниже
	if($checksInterval < 100) $checksInterval = 100;
	if(isset($_GET['getLog'])) {
	    echo file_get_contents($logFile);
	} else {
	// LOG //
?>

<html>
	<head>
		<meta charset="UTF-8">
		<link rel="shortcut icon" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAQAAAD9CzEMAAAEAElEQVR42rXXe2hWdRzH8deziz06DVPLNXRT0zR12vJuGtqcd5yBF2xe8JKmKZZa/eGUlMJLMwmyaYFSahehQkW6oGiOyhQzc5ERtBYtLUwrb4jm6Q8PD49zsIvPzvnv/Dif9+98v+d8vp/D7R0tdFCPR47ZWtSf/HBfaYuI/rolUjhZsohhjnnQnYbbZ5VGiQS01VIfR8zXw3anLZWWSPkUE/Xxi53mq3DRIqmJrXyGTT5yXrEzripKbHGgSIlzSlW4bqOmiZZv7rzzApcF3tE48a/mVEF4HtI+8fLJtobyf8kTSTwgwyGBwHWPS06s9P2e1EmuMoFrNiZ+763t9KpdLgkc1THxgIhCA2Rb7YL9utaHtQ30PpgiUF4fgAxnpGK2wPH6ALRxUSPMEfhHNNHySZ4I9z1eIFAoJbHyw5TIQ6pOrgn8a1ziEA2t8rO5IqIaa+awQOB38xLj/v0cVG6mFBEZGmlrhkuhYRzU//a60dIy3yoOJ26K3iJypPs0ZnmnrZNVV/nO9vpDfmyk3OMRDEZeDBC46qQhdflyc5zwtbaxK409K4oxovgwDhGoMLZ23hqRp9y2mHxEJ5NCg8g0RES2izchfjOqNoCuvrHNXTH5AovcG2v7UOnuUHITIHBK95rKp3rDHkkxi1hqYliAFMnSddNLsrWVANdrPkIfdVzvWDhcEOedd0vTS9RUEdNdrYT4U27N6n/Ya2HW6ellzeMGZg9JxmKAAUY4WwkQKK4JINsVQ0Ery2KFIlVv7TBfQ0nWe1j5LYBTNbGPWf7WFMkmxbWtpX5hqboYKeI5Q/14CyDQqXrAGkdAmsWxUNhTfuydSjJIur4WKK0CMLF6wJt2gAIDw9IMMyHOV+miozQrnagC8FL1gM02gy2iiOhpdFwfskQ0N1IDS6t8gterB2ywFWwPJae5M7aWKUdnEbM0sNLJKgCrqwesUBI+QQO0kh+XSx8TNQ9zNLFBWRWAguoBk5zTAlP1wGQZMYtYLhPLpZqqux1OVwHoUj0g0xXj0MxcPB0L6L2NB3O0N90gn7hwi/zpmo3R3d6VJsU0LU3QM7w6I7x5rFwLTfGB63VpMTzkpBEYKNd9FoVGNyNcHSvfCoW2VbH/wTUN6a84LCpNoSaKwi48E64WGG211T6+xU231PxnsIMv7dJMZy9aYjJYrDVYaIwXPO9UJUBZ7X5I+vvOWzLlW+Jt3ZGtCGwy2SZbKsmX6lvbqdzOZ76QrcBMez0g2Xuy8L2n/BALLjfO47WXv+Ggm1QostYIGw002Bp97VfqWlzlz9qtTV2DS6o8xT73kwOO2uOoYyrixMtsln+7EbKhLKOtt0+ZC/4TuOxXB6wzShtp1cWV/wHAC62FagOAnQAAAABJRU5ErkJggg==" />
		<title>Tor bridges</title>
		<script src="https://code.jquery.com/jquery-2.2.0.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/taboverride@4.0.3/build/output/taboverride.min.js"></script>
		<style>
		a:link,
		a:visited {
			color: #555555;
			text-decoration: none;
			background: none;
		}

		a:hover {
			text-decoration: underline;
			background: none;
		}

		body {
			font-family: Arial, sans-serif;
			background: #fafafa;
			color: #bcbdbc;
		}

		.topnav {
			overflow: hidden;
			background-color: #f3f4f7;
			border: 1px solid #e9e9e9;
			margin: 0em 0em .35em 0em;
		}

		.topnav a:hover {
			background-color: #ddd;
			color: black;
		}

		.topnav .left-container {
			float: left;
		}

		.topnav .left-container button:hover {
			background: #0066cc;
		}

		.topnav .right-container {
			float: right;
			background: #ccc;
		}

		.topnav .right-container a:hover {
			background: #0066cc;
			color: #fff;
		}

		.log {
			position: relative;
			padding: 10px;
			background-color: #000;
			color: #fff;
			font-size: 12px;
			line-height: 16px;
			overflow: auto;
			height: 23%;
		}

		/* PC */
		@media screen and (min-width: 1000px) {
		#editor {
			resize: none;
			overflow-x: scroll;
			background: #fff;
			color: #383838;
			font-family: Courier;
			width: 100%;
			height: 67%;
			font-size: 13px;
			padding: 10px;
			tab-size: 2em;
			-moz-tab-size: 2em;
			-o-tab-size: 2em;
			white-space: pre;
		}

		.topnav a {
			float: left;
			display: block;
			color: #404040;
			text-align: center;
			padding: 14px 16px;
			text-decoration: none;
			font-size: 17px;
		}

		.topnav .left-container button {
			background: #3cb371;
			border: none;
			cursor: pointer;
			color: #fff;
			font-family: Arial;
			padding: 10px 70px;
			margin: 4px;
			text-decoration: none;
			font-size: 17px;
		}
		}
		/* PC */

		/* Mobile */
		@media screen and (min-width: 480px) and (max-width: 1000px) {
		#editor {
			resize: none;
			overflow-x: scroll;
			background: #fff;
			color: #383838;
			font-family: Courier;
			width: 100%;
			height: 67%;
			font-size: 42px;
			padding: 10px;
			tab-size: 2em;
			-moz-tab-size: 2em;
			-o-tab-size: 2em;
			white-space: pre;
		}

		.topnav a {
			float: left;
			display: block;
			color: 404040;
			text-align: center;
			padding: 70px 76px;
			text-decoration: none;
			font-size: 50px;
		}

		.topnav .left-container button {
			background: #3cb371;
			border: none;
			cursor: pointer;
			color: #fff;
			font-family: Arial;
			padding: 60px 76px;
			margin: 10px;
			text-decoration: none;
			font-size: 50px;
		}
		}
		/* Mobile */
		</style>
		<script>
			// Минимизированное сокращение AJAX — любезно предоставлено iworkforthem на Github
			function postAjax(url, data, success) {var params = typeof data == 'string' ? data : Object.keys(data).map(function(k){return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])}).join('&');var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");xhr.open('POST', url);xhr.onreadystatechange = function() {if(xhr.readyState>3 && xhr.status==200) { success(xhr.responseText); }};xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');xhr.send(params);return xhr;}
			// Переменные GET в javascript — любезно предоставлено gion_13 на StackOverflow
			var $_GET = {}; if(document.location.toString().indexOf('?') !== -1) { var query = document.location.toString().replace(/^.*?\?/, '').replace(/#.*$/, '').split('&'); for(var i=0, l=query.length; i<l; i++) { var aux = decodeURIComponent(query[i]).split('='); $_GET[aux[0]] = aux[1]; } }

			// Имя и путь до файла
			var fileName = "/etc/tor/bridges.txt";

			// Сохранить файл
			function doSave() {
				postAjax("?",{"save":"1","fileName":fileName,"content":document.getElementById('editor').value},function(data) {
					// Проверка JSON и обработка ошибок
					try { data = JSON.parse(data); }
					catch(e) { console.log(data); data = {desc:'An unknown error occurred'}; }
						document.getElementById('saveStatus').innerHTML = '::. ' + data.desc + ' .::';
						window.setTimeout(function() {
							$('#saveStatus').fadeOut(
								function() {
									document.getElementById('saveStatus').innerHTML = 'Save & Apply';
									document.getElementById('saveStatus').style['display']='inline';
								}
							);
						},3000);
				});
			}

			// Ctrl+S вызывает функцию doSave()
			$(document).keydown(function(e) {if((e.which == '115' || e.which == '83' ) && (e.ctrlKey || e.metaKey)){ e.preventDefault(); doSave(); }});

			// LOG //
			/* Как часто проверяем лог-файл на наличие изменений, минимум – 100 */
			var checksInterval = 1000;
			setInterval(readLogFile, checksInterval);
			window.onload = function() {
			    readLogFile();
			};

			/* Читаем лог */
			var pathname = window.location.pathname;
			function readLogFile(){
			    $.get(pathname, { getLog : "true" }, function(data) {
			        data = data.replace(new RegExp("\n", "g"), "<br />");
			        $("#log").html(data);
			    });
			}

			/* Прокрутка лога (пауза и возобновление) */
			function pauseDiv() {
			    $('#log').scrollTop($('#log')[1].scrollHeight);
			}
			function resumeDiv() {
			    $('#log').scrollTop($('#log')[0].scrollHeight);
			}
			// LOG //
		</script>
	</head>
	<body>
		<div class="topnav">
			<div class="left-container">
				<button id = 'saveStatus' onclick = 'doSave();'>Save & Apply</button>
			</div>
			<div class="right-container">
				<a href="?logout">Logout</a>
			</div>
		</div>

		<!-- Текстовое поле, где происходит все редактирование. Заполняется через AJAX. -->
		<textarea active name = 'newContent' id = 'editor'></textarea>

		<!-- Включаем табуляцию в текстовой области -->
		<script>tabOverride.set(document.getElementById('editor'));</script>

		<!-- Заполняем все пустые поля -->
		<script>
			// Заполняем textarea
			postAjax("?",{"retrieve":"1","fileName":fileName},function(data) {
				document.getElementById("editor").innerHTML = data;
			});
		</script>

		<!-- LOG -->
		<div id="log" class="log" onMouseOver="pauseDiv();" onMouseOut="resumeDiv();"></div>
		<!-- LOG -->
	</body>
</html>
<?php } ?>
