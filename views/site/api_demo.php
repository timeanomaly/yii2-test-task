<h1>Проверка API</h1>

<label>Логин:</label>
<input type="text" id="login" value="admin" class="form-control"><br/>
<label>Пароль:</label>
<input type="text" id="pass" value="asd123" class="form-control"><br/>
<button data-action='{"method": "login"}' class="btn btn-primary">Отправить</button>
<p><i>После авторизации - токен будет сохранен, и использован для последующих запросов</i></p>
<textarea id="login_result" class="form-control"></textarea>

<h2>Получить список всех комментариев</h2>

<button data-action='{"method": "getAllComments", "result_input_id": "get_all_result"}' class="btn btn-primary">Отправить</button>
<textarea id="get_all_result" class="form-control"></textarea>

<h2>Получить список всех авторов</h2>

<button data-action='{"method": "getAllAuthors", "result_input_id": "get_all_authors_result"}' class="btn btn-primary">Отправить</button>
<textarea id="get_all_authors_result" class="form-control"></textarea>

<h2>Получить комментарии по IP</h2>
<label>IP:</label>
<input type="text" id="ip_input" value="::1" class="form-control"><br/>
<button data-action='{"method": "getCommentsByIp", "result_input_id": "get_by_ip_result"}' class="btn btn-primary">Отправить</button>
<textarea id="get_by_ip_result" class="form-control"></textarea>

<h2>Действия по ID</h2>
<label>ID:</label>
<input type="text" id="id_input" value="" class="form-control"><br/>
<button data-action='{"method": "getCommentById", "result_input_id": "id_action_result"}' class="btn btn-primary">Получить</button>
<!--button data-action='{"method": "deleteCommentById", "result_input_id": "id_action_result"}' class="btn btn-primary">Удалить</button-->
<textarea id="id_action_result" class="form-control"></textarea>

<h2>Создать комментарий</h2>
<p><i>Будет создан комментарий с данными из скрипта</i></p>
<button data-action='{"method": "createComment", "result_input_id": "create_comment_result"}' class="btn btn-primary">Отправить</button>
<textarea id="create_comment_result" class="form-control"></textarea>

<h2>Редактировать комментарий</h2>
<label>ID:</label>
<input type="text" id="edit_comment_id" value="" class="form-control"><br/>
<label>Новый рейтинг:</label>
<input type="text" id="edit_comment_rating" value="5" class="form-control"><br/>
<label>Новый текст:</label>
<textarea type="text" id="edit_comment_new_text" class="form-control">Это новый текст, который был отредактирован через API</textarea>
<button data-action='{"method": "editComment", "result_input_id": "edit_comment_result"}' class="btn btn-primary">Отправить</button>
<textarea id="edit_comment_result" class="form-control"></textarea>