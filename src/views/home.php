<?php

use app\controllers\Group;
use app\controllers\User;

include("src/views/src/includes/header.php");

?>

		<div class="container">
			<div class="row">
				<div class="col-12">
						<div class="d-flex gap-2">
							<form action="#" method="" class="d-flex flex-grow-1">
								<input type="search" id="search" class="form-control" placeholder="Поиск">
								<button type="submit" class="btn btn-outline-primary ms-2">
									<i class="bi bi-search"></i>
								</button>
							</form>
							<?php if($_SESSION['email'] != null){?>
							<a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGroup">
								<i class="bi bi-plus-lg"></i>
							</a>
							<?php } else if($_SESSION['email'] == ''){?>
								<?php }?>
						</div>
					</div>
			</div>
									<?php
					if($_SESSION['email'] == ''){
						?> 
						<div class="col-12">

							<div class="text-center m-5">
								<a href="/login">
									<h1>Необходимо авторизоваться</h1>
								</a>
							</div>
						</div>
						 <?php
					}else if(isset($_SESSION['email']) && $_SESSION['email'] != null){
							$currentUser = User::getUserByEmail($_SESSION['email']);

						$userGroup = [];
						if($currentUser && isset($currentUser['id'])){
							try{
							$userGroup = Group::getGroupByUser(['userId' => $currentUser['id']]);
							$userGroup = is_array($userGroup) ? $userGroup : [];
							}catch(Exception $e){
								die("Error getting user groups: " . $e -> getMessage());
								$userGroup= [];
							}
						}
					?>
			<div class="row m-5">
					<?php if(!empty($userGroup)){
					foreach($userGroup as $group){?>
				<div class="col-3 col-sm-12 col-md-6 g-3">
					<div class="card">
						<a href="/group?id=<?=$group['id']?>" class="btn">
							<div class="card-body">
								<h2 class="card-title"><?= htmlspecialchars($group['title']);?></h2>
							</div>
						</a>
					</div>
				</div> <?php } ?>
			</div>
			<?php }
			} ?>
		</div>
		
<?php include("src/views/src/includes/modalAddGroup.php"); ?>
<?php include("../CloudFlowPHP/src/views/src/includes/footer.php"); ?>