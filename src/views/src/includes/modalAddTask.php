<?php 

?>
<div class="modal fade" id="addTask" tabindex="-1" aria-labelledby="addTaskLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTaskLabel">Добавить Задачу (Группа: <?= htmlspecialchars($currentGroup['title']) ?>)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <form action="/group/task/add" method="POST">
                    <input type="hidden" value="<?=$groupId?>" name="groupId">
                    <input type="hidden" value="<?=$currentDateTime?>" name="createAt">
                    <input type="hidden" value="0" name="status">
                    <input type="hidden" value="<?=null ?>" name="closedAt">
                    <div class="mb-3">
                        <label for="taskTitle" class="form-label">Название задачи</label>
                        <input type="text" class="form-control" id="taskTitle" name="title" placeholder="Название задачи" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="taskDesc" class="form-label">Описание задачи</label>
                        <textarea class="form-control" id="taskDesc" name="description" rows="3" placeholder="Описание задачи"></textarea>
                    </div>
                    
                 <div class="mb-3">
    <label for="userId" class="form-label">Выбрать пользователя</label>
    <select class="form-select" id="userId" name="userId" required>
        <option value="" selected disabled>Выберите пользователя...</option>
        <?php if(!empty($groupUsers)): ?>
            <?php foreach($groupUsers as $user): ?>
                <option value="<?= $user['id'] ?>">
                    <?= htmlspecialchars($user['name'] ?? 'Без имени') ?>
                    <?php if(isset($user['mail'])): ?> (<?= htmlspecialchars($user['mail']) ?>)<?php endif; ?>
                </option>
            <?php endforeach; ?>
        <?php else: ?>
            <option value="" disabled>В группе пока нет пользователей</option>
        <?php endif; ?>
    </select>
</div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
    <button type="submit" class="btn btn-primary">Добавить</button>
</form>
            </div>
        </div>
    </div>
</div>