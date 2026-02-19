<div class="sidebar-wrapper" data-simplebar="true">
	<div class="sidebar-header">
		<div class="">
			<img src="./assets/images/Logo.png" class="logo-icon-2" alt="ThaiFa Foundation Admin"
				onerror="this.style.display='none'; this.nextElementSibling.style.display='block';" />
			<i class='bx bxs-school logo-icon-2 text-danger' style="display:none; font-size: 2rem;"></i>
		</div>
		<div>
			<h4 class="logo-text">ThaiFa FD</h4>
		</div>
		<a href="javascript:;" class="toggle-btn ms-auto"><i class="bx bx-menu"></i></a>
	</div>

	<ul class="metismenu" id="menu">
		<?php if (isset($_SESSION['StaffLogin'])) : ?>

			<li>
				<a href="./index.php">
					<div class="parent-icon icon-color-1"><i class="bx bx-home-alt"></i></div>
					<div class="menu-title">Home</div>
				</a>
			</li>

			<li class="menu-label">Web Apps</li>

			<li>
				<a href="./news_list.php">
					<div class="parent-icon icon-color-2"><i class="bx bx-envelope"></i></div>
					<div class="menu-title">News</div>
				</a>
			</li>

			<li>
				<a href="./chat-box.html">
					<div class="parent-icon icon-color-3"><i class="bx bx-conversation"></i></div>
					<div class="menu-title">Chat Box</div>
				</a>
			</li>

		<?php endif; ?>
	</ul>
</div>
