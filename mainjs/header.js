jQuery(function ($) {
	$(".hamburger").on("click", function () {
		$(this).toggleClass("is-active");
		$(".contenthamburgermenu").toggleClass("active");
		$("#header2023").toggleClass("active");
	});

	$(".mainitem").hover(
		function () {
			$(".subitems").addClass("hover");
		},
		function () {
			$(".subitems").removeClass("hover");
		}
	);

	$(".allsubitems").hover(
		function () {
			$(".subitems").addClass("hover");
			$(".mainitem").addClass("hover");
		},
		function () {
			$(".subitems").removeClass("hover");
			$(".mainitem").removeClass("hover");
		}
	);

	$(".blackbtn").hover(function () {
		$(".animarrow").hover();
	});

	$(".openhamburgermenu").on("click", function () {
		$(".mainservicemobil").toggle("active");

		if ($(".openhamburgermenu").hasClass("active")) {
			$(".openhamburgermenu").removeClass("active");
		} else {
			$(".openhamburgermenu").addClass("active");
		}
	});

	setTimeout(function () {
		var $banner = $("#hdsb-stickybanner");

		if ($banner.length && $banner.hasClass("is-active")) {
			$("body").addClass("banner-abierto");
		}

		$("body").on("click", ".hdsb-stickybanner-close", function () {
			$("body").removeClass("banner-abierto");
		});
	}, 500);
});
