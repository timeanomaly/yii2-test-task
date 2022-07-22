
let access_token = null;
let refresh_token = null;

// todo синхронный вызов. Для тестового задания это нормально,
//     но в реальном проекте будет переделано под асинхронный.
function renewAccessTokenWithRefreshToken() {
    let is_success = false;
    let formData = new FormData();
    formData.append('refresh_token', refresh_token);
    $.ajax({
        url: '/auth/refresh-token',
        type: "POST",
        data: formData,
        async: false,
        cache: false,
        processData: false,
        contentType: false,
        success: function (data) {
            if (data.status === 'ok') {
                is_success = true;
                access_token = data['access_token'];
                //window.localStorage.setItem('access_token', data['access_token']);
            }
        },
        error: function (rs, e) {
            if (rs.status === 401) {
                //window.location.href = "/login/";
                // todo
            } else {
                // console.error(rs.responseText);
                // todo
            }
        }
    });
    return is_success
}

var API = {
    logout: function () {
        access_token = null;
        refresh_token = null;
        //window.localStorage.removeItem('refresh_token');
        //window.localStorage.removeItem('access_token');
    },
    login: function (login, pass) {

        return new Promise(function (resolve, reject) {

            let formData = new FormData();
            formData.append('LoginForm[username]', login);
            formData.append('LoginForm[password]', pass);

            $.ajax({
                url: "/auth/login",
                type: "POST",
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function (data) {
                    if (data.status === 'ok' && data['refresh_token'] && data['access_token']) {
                        access_token = data['access_token'];
                        refresh_token = data['refresh_token'];
                        //window.localStorage.setItem('refresh_token', data['refresh_token']);
                        //window.localStorage.setItem('access_token', data['access_token']);
                    }
                    resolve(data);
                },
                error: function (rs, e) {
                    //console.error(rs.status);
                    //console.error(rs.responseText);
                    reject({m: "Ошибка выполнения запроса (сервер недоступен?)"});
                }
            });

        });

    },
    call: function (url, request_type, data) {
        return new Promise(function (resolve, reject) {

            if (!access_token) {
                reject({m:"Не авторизованы"});
                return;
            }

            $.ajax({
                url: url,
                headers: {
                    'Authorization': `Bearer ${access_token}`
                },
                type: request_type,
                data: data,
                tokenFlag: true,
                success: function (data) {
                    resolve(data);
                },
                error: function handleAjaxError(rs, e) {
                    if (rs.status === 401) {
                        if (this.tokenFlag) {
                            this.tokenFlag = false;
                            if (renewAccessTokenWithRefreshToken()) {
                                this.headers["Authorization"] = `Bearer ${access_token}`
                                $.ajax(this);  // calling API endpoint again with new access token
                            } else {
                                reject({m: "Ошибка авторизации"});
                            }
                        }
                    } else {
                        reject({m: "Ошибка HTTP " + rs.status});
                    }
                }
            });

        });

    },

}

var Demo = {
    _processRequest: function (result_input_id, promise) {
        $("#" + result_input_id).val("Выполняется запрос...");
        promise.then(function (result){
            let text = typeof (result) == "string" ? result : JSON.stringify(result);
            $("#" + result_input_id).val(text);
        }).catch(function (data){
            let text = "<ошибка выполнения запроса>:\n" + JSON.stringify(data);
            $("#" + result_input_id).val(text);
        });
    },
    login: function (action_data) {
        this._processRequest(
            "login_result",
            API.login($("#login").val().trim(), $("#pass").val().trim())
        );
    },
    getAllComments: function (action_data) {
        this._processRequest(
            action_data.result_input_id,
            API.call("/api", "GET", null)
        );
    },
    getAllAuthors: function (action_data) {
        this._processRequest(
            action_data.result_input_id,
            API.call("/api/get-all-authors", "GET", null)
        );
    },
    getCommentsByIp: function (action_data) {
        let ip = $("#ip_input").val();
        this._processRequest(
            action_data.result_input_id,
            API.call("/api/get-comments-from-ip?ip=" + encodeURIComponent(ip), "GET", null)
        );
    },
    getCommentById: function (action_data) {
        let id = $("#id_input").val();
        this._processRequest(
            action_data.result_input_id,
            API.call("/api/" + id, "GET", null)
        );
    },
    deleteCommentById: function (action_data) {
        let id = $("#id_input").val();
        this._processRequest(
            action_data.result_input_id,
            API.call("/api/" + id, "DELETE", null)
        );
    },
    createComment: function (action_data) {
        let formData = {
            product_id: 1,
            name: "Api",
            email: "api@test.com",
            rating: 5,
            content: "Этот комментарий создан через API"
        };
        this._processRequest(
            action_data.result_input_id,
            API.call("/api", "POST", formData)
        );
    },
    editComment: function (action_data) {
        let id = $("#edit_comment_id").val();
        let formData = {
            rating: $("#edit_comment_rating").val(),
            content: $("#edit_comment_new_text").val(),
        };
        this._processRequest(
            action_data.result_input_id,
            API.call("/api/" + id, "PUT", formData)
        );
    },
}

$(document).ready(function (){

    $("*[data-action]").on('click', function(event) {
        let raw_action_data = event.target.getAttribute("data-action");
        let action_data = JSON.parse(raw_action_data);
        Demo[action_data.method](action_data);
    })

});