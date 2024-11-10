<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo List con Editar y Eliminar</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <label for="content">Nueva tarea</label>
        <input type="text" id="content" class="form-control" placeholder="Ingresa una tarea"><br>
        <button id="guardar" class="btn btn-primary">Guardar</button>
        
        <h2 class="mt-4">TODO</h2>
        <ul id="lista" class="list-group">
            <?php
                require "DB.php";
                require "todo.php";
                try {
                    $db = new DB();
                    $todo_list = Todo::DB_selectAll($db->connection);
                    foreach ($todo_list as $row) {
                        echo "<li class='list-group-item' data-id='" . $row->getItem_id() . "'>";
                        echo $row->getItem_id() . ". " . $row->getContent();
                        echo " <button class='btn btn-danger btn-sm float-right delete-btn'>Check</button>";
                        echo " <input type='text' class='edit-input form-control-sm ml-2' placeholder='Editar tarea'>";
                        echo " <button class='btn btn-info btn-sm float-right edit-btn'>Editar</button>";
                        echo "</li>";
                    }
                } catch (PDOException $e) {
                    echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
                }
            ?>
        </ul>
    </div>

    <script>
        // Añadir una nueva tarea
        document.getElementById('guardar').addEventListener('click', function() {
            const content = document.getElementById('content').value;
            if (!content) {
                alert('Por favor, introduce un valor.');
                return;
            }

            const url = 'http://todo2.pepe.lan/controller.php';
            const postData = { content: content };

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(postData)
            })
            .then(response => response.json())
            .then(data => {
                const ul = document.getElementById('lista');
                ul.innerHTML = '';

                data.forEach(item => {
                    var li = document.createElement("li");
                    li.className = "list-group-item";
                    li.setAttribute("data-id", item.item_id);
                    li.innerHTML = item.item_id + ". " + item.content +
                        " <button class='btn btn-danger btn-sm float-right delete-btn'>Check</button>" +
                        " <input type='text' class='edit-input form-control-sm ml-2' placeholder='Editar tarea'>" +
                        " <button class='btn btn-info btn-sm float-right edit-btn'>Editar</button>";
                    ul.appendChild(li);
                });
            })
            .catch(error => console.error('Error en la solicitud POST:', error));
        });

        // Eliminar tarea
        document.getElementById('lista').addEventListener('click', function(e) {
            if (e.target.classList.contains('delete-btn')) {
                const item_id = e.target.parentElement.getAttribute("data-id");
                const url = `http://todo2.pepe.lan/controller.php?id=${item_id}`;

                fetch(url, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    e.target.parentElement.remove();  // Eliminar el elemento específico
                })
                .catch(error => console.error('Error en la solicitud DELETE:', error));
            }
        });

        // Editar tarea
        document.getElementById('lista').addEventListener('click', function(e) {
            if (e.target.classList.contains('edit-btn')) {
                const item_id = e.target.parentElement.getAttribute("data-id");
                const newContent = e.target.previousElementSibling.value; // Obtener el contenido del input

                if (!newContent) {
                    alert('Por favor, introduce un nuevo contenido.');
                    return;
                }

                const url = `http://todo2.pepe.lan/controller.php?id=${item_id}`;
                const putData = { content: newContent };

                fetch(url, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(putData)
                })
                .then(response => response.json())
                .then(data => {
                    // Actualizar la lista con los nuevos datos
                    const ul = document.getElementById('lista');
                    ul.innerHTML = '';

                    data.forEach(item => {
                        var li = document.createElement("li");
                        li.className = "list-group-item";
                        li.setAttribute("data-id", item.item_id);
                        li.innerHTML = item.item_id + ". " + item.content +
                            " <button class='btn btn-danger btn-sm float-right delete-btn'>Check</button>" +
                            " <input type='text' class='edit-input form-control-sm ml-2' placeholder='Editar tarea'>" +
                            " <button class='btn btn-info btn-sm float-right edit-btn'>Editar</button>";
                        ul.appendChild(li);
                    });
                })
                .catch(error => console.error('Error en la solicitud PUT:', error));
            }
        });
    </script>
</body>
</html>
