<footer class="footer">
	<?php
	$end = microtime(true);
	$creationtime = ($end - $_SESSION['render']);
	?>
	<div class="card-body">
		<div class="card-text">
			<div class="float-left">
				<p class="float-right img-fluid" style="color: white;">
					Development with ❤️ by <a href="https://www.leaks.ro/profile/14122-pericolrpg/" style="color: purple;"> PericolRPG</a></p>
			</div>
			<div class="float-right text-white-50">
				<p style="color: white;">
					(c) purplepanel.bugged.ro - 2022 | This page took <a style="color: purple;"> <?php printf("%.3f", $creationtime) ?> </a> seconds to render | Version 1.5.0b
				</p>
			</div>
		</div>
	</div>
</footer>
<!-- END WRAPPER -->
<!-- Javascript -->
<script src="<?php echo Config::$_PAGE_URL; ?>assets/vendor/bootstrap-progressbar/js/bootstrap-progressbar.min.js"></script>
<script src="<?php echo Config::$_PAGE_URL; ?>assets/vendor/flot.tooltip/jquery.flot.tooltip.js"></script>
<script src="<?php echo Config::$_PAGE_URL; ?>assets/vendor/x-editable/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
<script src="<?php echo Config::$_PAGE_URL; ?>assets/vendor/jquery.maskedinput/jquery.maskedinput.min.js"></script>
<script src="<?php echo Config::$_PAGE_URL; ?>assets/vendor/moment/min/moment.min.js"></script>
<script src="<?php echo Config::$_PAGE_URL; ?>assets/vendor/jquery-sparkline/js/jquery.sparkline.min.js"></script>
<script src="<?php echo Config::$_PAGE_URL; ?>assets/vendor/bootstrap-tour/js/bootstrap-tour.min.js"></script>
<script src="<?php echo Config::$_PAGE_URL; ?>assets/vendor/jquery-appear/jquery.appear.min.js"></script>
<script src="<?php echo Config::$_PAGE_URL; ?>assets/vendor/justgage-toorshia/justgage.js"></script>
<script src="<?php echo Config::$_PAGE_URL; ?>assets/scripts/klorofilpro-common.min.js"></script>
<script src="<?php echo Config::$_PAGE_URL; ?>assets/vendor/pace/pace.min.js"></script>
<script src="<?php echo Config::$_PAGE_URL; ?>assets/vendor/sweetalert2/sweetalert2.js"></script>
<script src="<?php echo Config::$_PAGE_URL; ?>assets/vendor/toastr/toastr.min.js"></script>
<script src="<?php echo Config::$_PAGE_URL; ?>assets/vendor/markdown/markdown.js"></script>
<script src="<?php echo Config::$_PAGE_URL; ?>assets/vendor/to-markdown/to-markdown.js"></script>
<script src="<?php echo Config::$_PAGE_URL; ?>assets/vendor/bootstrap-markdown/bootstrap-markdown.js"></script>
</body>

</html>
<script>
	$(document).ready(function() {
		$(document).bind("contextmenu", function(e) {
			return false;
		});
	})
</script>
<?php
ob_flush();
?>