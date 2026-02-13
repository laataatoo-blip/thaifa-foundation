<!-- JavaScript -->

<!-- Bootstrap JS -->
<script src="assets/js/bootstrap.bundle.min.js"></script>

<!--plugins-->
<script src="assets/js/jquery.min.js"></script>
<script src="assets/plugins/simplebar/js/simplebar.min.js"></script>
<script src="assets/plugins/metismenu/js/metisMenu.min.js"></script>
<!-- <script src="assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script> -->
<!-- App JS -->
<script src="assets/js/app.js"></script>
<!-- 
    <script>
		new PerfectScrollbar('.dashboard-social-list');
		new PerfectScrollbar('.dashboard-top-countries');
	</script> 
-->


<!-- theam mode script -->
<script>
/*switcher*/
$(".switcher-btn").on("click", function() {
    $(".switcher-wrapper").toggleClass("switcher-toggled");
});


$("#darkmode").on("click", () => {
    $("html").attr("class", "dark-theme")
    fetch("https://dsil.kmutt.ac.th/setTheam.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "theam=dark"
    });
});

$("#lightmode").on("click", function () {
    $("html").attr("class", "light-theme")
    fetch("https://dsil.kmutt.ac.th/setTheam.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "theam=light"
    });
});

$("#darksidebar").on("click", function () {
    $("html").attr("class", "dark-sidebar")
    fetch("https://dsil.kmutt.ac.th/setTheam.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "theam=semi-dark"
    });
});

<?php if(isset($_SESSION['theam'])) : ?>
	
	<?php if($_SESSION['theam'] == "dark") : ?>
		$("html").attr("class", "dark-theme")
	<?php endif ?>
	
	<?php if($_SESSION['theam'] == "light") : ?>
	    $("html").attr("class", "light-theme")
	<?php endif ?>

	<?php if($_SESSION['theam'] == "semi-dark") : ?>
	    $("html").attr("class", "dark-sidebar")
	<?php endif ?>

<?php endif ?>

</script>