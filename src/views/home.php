<?php  include("src/views/src/includes/header.php");?>


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
							<a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGroup">
								<i class="bi bi-plus-lg"></i>
							</a>
						</div>
					</div>
			</div>
			<div class="row m-5">
				<div class="col-3 col-sm-12 col-md-6">
					<?php
					// foreach($var as $variable){

					// } 
					?>
					<div class="card">
						<a href="#" class="btn">
							<div class="card-body">
								<h2 class="card-title"><?php ?>Группа</h2>
							</div>
						</a>
					</div>
				</div>
			</div>
		</div>
		
<?php include("src/views/src/includes/modalAddGroup.php"); ?>
<?php include("../CloudFlowPHP/src/views/src/includes/footer.php"); ?>