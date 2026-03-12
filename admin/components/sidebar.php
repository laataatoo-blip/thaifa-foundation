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
			<?php if (isset($_SESSION['StaffLogin']) || isset($_SESSION['AdminLogin'])) : ?>

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
				<a href="./shop_products.php">
					<div class="parent-icon icon-color-4"><i class="bx bx-store"></i></div>
					<div class="menu-title">Shop Products</div>
				</a>
			</li>

			<li>
				<a href="./shop_orders.php">
					<div class="parent-icon icon-color-5"><i class="bx bx-package"></i></div>
					<div class="menu-title">Shop Orders</div>
				</a>
			</li>

			<li>
				<a href="./calendar_events.php">
					<div class="parent-icon icon-color-6"><i class="bx bx-calendar-event"></i></div>
					<div class="menu-title">Calendar Events</div>
				</a>
			</li>

			<li>
				<a href="./donations.php">
					<div class="parent-icon icon-color-12"><i class="bx bx-donate-heart"></i></div>
					<div class="menu-title">Donations</div>
				</a>
			</li>

			<li>
				<a href="./team_members.php">
					<div class="parent-icon icon-color-7"><i class="bx bx-group"></i></div>
					<div class="menu-title">Foundation Team</div>
				</a>
			</li>

			<li>
				<a href="./analytics.php">
					<div class="parent-icon icon-color-8"><i class="bx bx-line-chart"></i></div>
					<div class="menu-title">Web Analytics</div>
				</a>
			</li>

			<li>
				<a href="./security_audit.php">
					<div class="parent-icon icon-color-10"><i class="bx bx-shield-quarter"></i></div>
					<div class="menu-title">Admin Access Audit</div>
				</a>
			</li>

			<li>
				<a href="./contact_messages.php">
					<div class="parent-icon icon-color-9"><i class="bx bx-message-rounded-dots"></i></div>
					<div class="menu-title">Contact Messages</div>
				</a>
			</li>

				<?php endif; ?>
	</ul>
</div>
