<div class="modal fade" id="addGroup" tabindex="-1" aria-labelledby="addModel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addModel">Добавить/Создать группу</h1>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <div class="form-control">
                    <form action="#" method="POST" class="d-flex">
        <input type="text" class="form-control" name="code" id="groupCode" placeholder="Код группы">
        <button type="submit" class="btn btn-primary ms-3">Добавить</button>
    </form>
                </div>
                <div class="form-control mt-3">
                   <form action="/group/add" method="POST" class="d-flex">
                  <input type="text" class="form-control" name="groupTitle" id="title" placeholder="Название группы">
                   <button type="submit" class="btn btn-primary ms-3">Создать</button>
             </form>
                </div>
            </div>
        </div>
    </div>

</div>