$(document).ready(function(){
			  $(".hamburger").click(function(){
			    $(this).toggleClass("is-active");
			    $(".contenthamburgermenu").toggleClass("active");
			    $("#header2023").toggleClass("active");
			  });

			  $( ".mainitem" ).hover(
				  function() {
				    $( ".subitems" ).addClass( "hover" );
				  }, function() {
				    $( ".subitems" ).removeClass( "hover" );
				  }
				);

				$( ".allsubitems" ).hover(
				  function() {
				    $( ".subitems" ).addClass( "hover" );
				    $( ".mainitem" ).addClass( "hover" );
				  }, function() {
				    $( ".subitems" ).removeClass( "hover" );
				    $( ".mainitem" ).removeClass( "hover" );
				  }
				);

				$( ".blackbtn" ).hover(
				  function() {
				    $( ".animarrow" ).hover();
				  }
				);

				//mobil menu
				$( ".openhamburgermenu" ).on( "click", function() {
				  $( ".mainservicemobil" ).toggle("active");

				  if( $( ".openhamburgermenu" ).hasClass( "active" ) ){
				  	$( ".openhamburgermenu" ).removeClass("active");
				  }else{
				  	$( ".openhamburgermenu" ).addClass("active");
				  }
				  
				} );
  
  				setTimeout(function() {
        
        var $banner = $('#hdsb-stickybanner');
        var $closeBtn = $('.hdsb-stickybanner-close');

        // Debugging: Mira la consola del navegador (F12) para ver estos mensajes
        console.log('Buscando banner...', $banner.length > 0 ? 'Encontrado' : 'No encontrado');
        console.log('Tiene clase is-active?', $banner.hasClass('is-active'));

        // 1. Verificamos si el banner existe y tiene la clase
        if ($banner.length && $banner.hasClass('is-active')) {
            console.log('Aplicando clase al body...');
            $('body').addClass('banner-abierto');
        }

        // 2. Escuchamos el clic en el botón de cerrar
        // Usamos 'body' como delegado por si el botón se crea dinámicamente
        $('body').on('click', '.hdsb-stickybanner-close', function() {
            $('body').removeClass('banner-abierto');
            // Opcional: ocultar banner visualmente si el script original no lo hace
            // $banner.hide(); 
        });

    }, 500);
			  
			});