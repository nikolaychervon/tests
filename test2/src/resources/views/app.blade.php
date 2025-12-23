<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Список пользователей</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Список пользователей</h2>
    <div id="user-list" class="list-group mt-3"></div>

    <script>
        fetch('/api/list')
            .then(res => res.json())
            .then(users => {
                const container = document.getElementById('user-list');
                users.forEach(user => {
                    const div = document.createElement('div');
                    div.className = 'list-group-item d-flex align-items-center';
                    div.innerHTML = `
                        <img src="${user.avatar}" style="width:70px; height:70px; border-radius:50%; margin-right:15px;">
                        <span>${user.nickname}</span>
                    `;
                    container.appendChild(div);
                });
            });
    </script>
</div>

</body>
</html>
