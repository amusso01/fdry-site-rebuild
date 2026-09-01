//btn start
	$( "#startbtn" ).click(function() {
	  $( "#step2" ).addClass( "active" );
	  $( "#step2 .ui-step-content" ).addClass( "in" );

	  $( "#step1" ).removeClass( "active" );
	  $( "#step1 .ui-step-content" ).removeClass( "in" );
	});

	//2 step
	$( ".labelop input[type=radio]" ).change(function() {
	  $( "#step2 .blackbuttom" ).removeClass( "disable" );
	});
	$( "#otherfield" ).change(function() {
		if ( $(this).is(':checked') ) {
			$( "#step2 .blackbuttom" ).removeClass( "disable" );
		}
	});

	$( "#back1" ).click(function() {
	  $( "#step1" ).addClass( "active" );
	  $( "#step1 .ui-step-content" ).addClass( "in" );

	  $( "#step2" ).removeClass( "active" );
	  $( "#step2 .ui-step-content" ).removeClass( "in" );
	});

	$( "#next3" ).click(function() {
	  $( "#step3" ).addClass( "active" );
	  $( "#step3 .ui-step-content" ).addClass( "in" );

	  $( "#step2" ).removeClass( "active" );
	  $( "#step2 .ui-step-content" ).removeClass( "in" );
	});

	//step 3
	$( "#step3 .boxlabel input[type=radio]" ).change(function() {
	  $( "#step3 .blackbuttom" ).removeClass( "disable" );
	});

	$( "#back2" ).click(function() {
	  $( "#step2" ).addClass( "active" );
	  $( "#step2 .ui-step-content" ).addClass( "in" );

	  $( "#step3" ).removeClass( "active" );
	  $( "#step3 .ui-step-content" ).removeClass( "in" );
	});

	$( "#next4" ).click(function() {
	  $( "#step4" ).addClass( "active" );
	  $( "#step4 .ui-step-content" ).addClass( "in" );

	  $( "#step3" ).removeClass( "active" );
	  $( "#step3 .ui-step-content" ).removeClass( "in" );
	});

	//step 4
	$( "#step4 .boxlabel input[type=radio]" ).change(function() {
	  $( "#step4 .blackbuttom" ).removeClass( "disable" );
	});

	$( "#back3" ).click(function() {
	  $( "#step3" ).addClass( "active" );
	  $( "#step3 .ui-step-content" ).addClass( "in" );

	  $( "#step4" ).removeClass( "active" );
	  $( "#step4 .ui-step-content" ).removeClass( "in" );
	});

	$( "#next5" ).click(function() {
	  $( "#step5" ).addClass( "active" );
	  $( "#step5 .ui-step-content" ).addClass( "in" );

	  $( "#step4" ).removeClass( "active" );
	  $( "#step4 .ui-step-content" ).removeClass( "in" );
	});

	//step 5
	$( "#back4" ).click(function() {
	  $( "#step4" ).addClass( "active" );
	  $( "#step4 .ui-step-content" ).addClass( "in" );

	  $( "#step5" ).removeClass( "active" );
	  $( "#step5 .ui-step-content" ).removeClass( "in" );
	});

	$( "#next6" ).click(function() {
	  $( "#step6" ).addClass( "active" );
	  $( "#step6 .ui-step-content" ).addClass( "in" );

	  $( "#step5" ).removeClass( "active" );
	  $( "#step5 .ui-step-content" ).removeClass( "in" );
	});

	//step 6
	$( ".blacksubmit" ).click(function() {
	  if ( $("#fullname").val() &&  $("#email").val() ) {
	  	$( "#submit6" ).click();
	  }else{
	  	$("#fullname").css("border", "2px solid red");
	  	$("#email").css("border", "2px solid red");
	  }
	});

	$( "#back5" ).click(function() {
	  $( "#step5" ).addClass( "active" );
	  $( "#step5 .ui-step-content" ).addClass( "in" );

	  $( "#step6" ).removeClass( "active" );
	  $( "#step6 .ui-step-content" ).removeClass( "in" );
	});

	$('input:checkbox').change(


		function(){
			if ( $( ".areaother" ).hasClass( "on" ) ) {
				$( ".areaother" ).removeClass( "on" );
			}else{
				$( ".areaother" ).addClass( "on" );
			}
		}
	); 