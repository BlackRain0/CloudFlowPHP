
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>CloudFlow</title>
	<link href="../../../../bs/bootstrap.min.css" rel="stylesheet">
    <link href="../../../../bs/bootstrap-icons-1.13.1/bootstrap-icons.css" rel="stylesheet">
	<style></style>
</head>
<body>
	<div class="header">
		<div class="container-fluid">
			<div class="row bg-primary mb-4">
				<div class="col-12">
                    <div class="navbar">
                        <div class="dropdown">
                            <a class="btn" href="#" role="button" id="dropdownMenu" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-list text-white" style="font-size: 2rem;"></i>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenu">
                                <!-- НЕ Авторизованным -->
                                <?php 
                                if($_SESSION['email']  == ''){
                                    
                    
                                ?>
                                <li>
                                      <a href="/login" class="dropdown-item" data-bs-toggle="tooltip" data-bs-placement="top" title="Необходимо авторизоваться">
                                            Необходимо авторизоваться
                                      </a>
                                </li>
                                <?php }else if($_SESSION['email']  != null){?>
                                <!-- --------------------------------------------- -->
                                <li>
                                    <a href="#" class="dropdown-item">
                                        Группы
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="dropdown-item">
                                        Мои задачи
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="dropdown-item">
                                        Профиль
                                    </a>
                                </li>
                                <?php }?>
                            </ul>
                        </div>

                         <h1 class="text-center text-white">CloudFlow</h1>
                         <div class="dropdown">
                             <a href="#" class="btn" role="button" id="dropdownMenu2" data-bs-toggle="dropdown" aria-expanded="false">
                                 <i class="bi bi-person-fill text-white" style="font-size: 2rem;"></i>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                    <?php
                                    if($_SESSION['email'] == ''){
                                    ?>
                                    <li>
                                        <a href="/login" class="dropdown-item">
                                            Войти 
                                        </a>
                                    <li>
                                        <a href="/auth" class="dropdown-item">
                                            Зарегистрироваться 
                                        </a>
                                    </li>
                                    <?php }else if($_SESSION['email'] != null){ ?>
                                                 <li>
                                        <a href="#" class="dropdown-item">
                                            <?= $_SESSION['email'] ?> 
                                            <form action="/user/logout" method="POST">
                                                <input type="submit" value="Выйти">
                                            </form>
                                            <?php } ?>
                                        </a>
                                    </li>
                                </ul>
                         </div>
                    </div>
                       
				</div>
			</div>
		</div>
	</div>
