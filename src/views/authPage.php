<?php
 include("src/views/src/includes/header.php");
?>
<?php if(isset($_GET['error'])): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($_GET['error']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if(isset($_GET['success'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($_GET['success']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <form action="/user/register" id="registration form" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                  <h2 class="text-center">Зарегистрироваться</h2>
                                <label for="email">
                                    Email*
                                </label>
                                <input type="email" class="form-control" id="email" placeholder="email" name="email"
                                 required>
                            </div>
                            <div class="form-group">
                                <label for="name">
                                   Ваше Имя*
                                </label>
                                <input type="text" class="form-control" id="name" placeholder="Имя" name="name"
                                 required>
                    
                            </div>
                            <div class="form-group">
                                <label for="password">
                                    Пароль*
                                </label>
                                <input type="password" class="form-control" id="password" placeholder="Пароль" name="pass"
                                 required>
                    
                            </div>
                                    <div class="form-group">
                                <label for="passwordCheck">
                                    Потвердите пароль*
                                </label>
                                <input type="password" class="form-control" id="passwordCheck" placeholder="Пароль" name="confirmPass"
                                 required>
                    
                            </div>
                                      <div class="form-group">
                                <label for="photo">
                                    Фото
                                </label>
                                <input type="file" class="form-control" id="photo" placeholder="Фото" name="photo">
                    
                            </div>
                            <button class="btn btn-primary my-3">Зарегистрироваться</button>
                        </form>
                        <p>Есть аккаунт?
                            <a href="/login">Войти</a>
                        </p>
                        <p>*- обязательные поля</p>
                    </div>
                </div>
        </div>
    </div>
</div>

<?php
 include("src/views/src/includes/footer.php");
?>