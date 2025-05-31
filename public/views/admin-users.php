<?php
require_once __DIR__."/../utils/ComponentLoader.php";
ComponentLoader::load('header', ['title' => 'Admin - Users']);

use models\Role;
$roles = Role::toArray();
?>

<main>
    <?php ComponentLoader::load("admin-nav"); ?>
    <h2>Welcome to the Admin Panel --- Users Archive</h2>
    <div>
        <?php
        if(isset($messages)){
            foreach($messages as $message){
                echo "<p>$message</p>";
            }
        }
        ?>
    </div>
    <div class="dashboard">
        <div id="archive" class="dashboard-list">
        </div>
        <div class="scroll-container">
            <div id="user-modal" class="dashboard-modal">
                    <h3 id="user-modal-title">ADD NEW USER</h3>
                    <form class="dashboard-modal-form" action="/admin/addUser" method="POST">
                        <label for="user-email">Email:
                            <input type="email" name="email" id="user-email" required minlength="3">
                        </label>

                        <label for="user-password">Password:
                            <input type="password" name="password" id="user-password" required minlength="6">
                        </label>

                        <label for="user-role">Role:
                            <select name="role" id="user-role" required>
                            <?php foreach($roles as $role): ?>
                                <option value="<?= $role ?>"><?= $role ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>

                        <button type="submit">Save</button>
                    </form>
                </div>
        </div>
    </div>
</main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('/api/getAllUsers', {
                method: "POST",
                headers: getSessionHeaders(),
            })
                .then(response => response.json())
                .then(data => {
                    const usersList = document.getElementById('archive');
                    data.forEach(user => {
                        const userItem = document.createElement('div');
                        userItem.className = 'user-item';
                        userItem.id = `user_${user.id}`;
                        userItem.innerHTML = `
                <p>Email: ${user.email}</p>
                <p>Role: ${user.role}</p>
                <button class="delete-user" onclick="deleteUser('${user.id}')">Delete</button>
            `;
                        usersList.appendChild(userItem);
                    });
                })
                .catch(error => console.error('Error fetching users:', error));
        });

        function deleteUser(userId) {
            fetch(`/api/deleteUser`, {
                method: "POST",
                headers: getSessionHeaders(),
                body: JSON.stringify({ user_id: userId })
            })
                .then(response => {
                    if (response.ok) {
                        document.getElementById(`user_${userId}`).remove();
                    } else {
                        console.error('Error deleting user:', response.statusText);
                    }
                })
                .catch(error => console.error('Error deleting user:', error));
        }
    </script>

<?php
ComponentLoader::load('footer');
?>