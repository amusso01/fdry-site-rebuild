<?php
/**
 * Template Name: Type Form page
 * 
 * Template Post Type: page
 *
 * This template shows Brief form
 * 
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();
?>


<?php if( isset( $_GET['send'] ) && $_GET['send'] == 'yes' ){ ?>

	<?php 
		if ( $_POST['fullname'] != '' &&  $_POST['email'] != '') { 
	
			$my_post = array(
			  'post_type'=>'typeform', 
			  'post_title'    => $_POST['fullname'],
			  'post_status'   => 'private',
			  'post_content'=>''
			);
			$post_id = wp_insert_post( $my_post );

			//Insert in ACF
			if ( $_POST['radio'] == '' ) {
				update_field('field_6266edb2d1b99', 'Other', $post_id);
			}else{
				update_field('field_6266edb2d1b99', $_POST['radio'], $post_id);
			}

			if ( $_POST['otherproject'] ) {
				update_field('field_62f1004851f38', $_POST['otherproject'], $post_id);
			}
			
			update_field('field_6266ee6fd1b9a', $_POST['cat'], $post_id);
			update_field('field_6266eeb3d1b9b', $_POST['budget'], $post_id);
			update_field('field_6266eeffd1b9d', $_POST['fullname'], $post_id);
			update_field('field_6266ef1bd1b9f', $_POST['email'], $post_id);
			

			if ( $_POST['company'] ) {
				update_field('field_6266ef12d1b9e', $_POST['company'], $post_id);
			}

			if ( $_POST['tel'] ) {
				update_field('field_62692c69e587a', $_POST['tel'], $post_id);
			}

			if ( $_POST['timecall'] == '' ) {
				update_field('field_62692c7ee587b', 'No selected', $post_id);
			}else{
				update_field('field_62692c7ee587b', $_POST['timecall'], $post_id);
			}

			if ( $_POST['kind'] ) {
				update_field('field_6266eeecd1b9c', $_POST['kind'], $post_id);
			}
			

			//Email notification

			$message = '
				<html>
		        <head>
		        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		            <title></title>
		        </head>
		        <body data-rsssl=1 data-rsssl=1 data-rsssl=1>
		            <div style="max-width: 800px; margin: 0 auto; background: #FFF;">
		            <img 
		            style="display: block; margin: 0 auto 0px; width: 200px;" src="https://www.fdry.com/wp-content/uploads/2022/04/fdrylogo.png">
		            <div style="background-color: #000; color:#fff; padding: 20px 0; text-align: center;"><h2 color: #fff; font-weight: 300;>New Website Brief</h2></div>

		            <div style="padding: 0 20px;">
		            
		            <p>Hi Andres,</p>
		            

		            <p>A visitor to FDRY has submitted a project brief. See details below</p>
		            <br>

		            <h4>Personal Information</h4>
		            <p><strong>Full name: </strong> '.$_POST['fullname'].'</p>
		            <p><strong>Company: </strong> '.$_POST['company'].'</p>
		            <p><strong>Email: </strong> '.$_POST['email'].'</p>
		            <p><strong>Tel: </strong> '.$_POST['tel'].'</p>
		            <p><strong>Time call: </strong> '.$_POST['timecall'].'</p>
					<br>
		            <h4>Project Details</h4>
		            <p><strong>Type of project: </strong> '.$_POST['radio'].'</p>
		            <p><strong>Business category: </strong> '.$_POST['cat'].'</p>
		            <p><strong>Budget: </strong> '.$_POST['budget'].'</p>
		            <p><strong>Project Brief: </strong> '.$_POST['kind'].'</p>

		            <p><strong>Ip Address : </strong> '.$_SERVER['REMOTE_ADDR'].'</p>
		            
		            
		            </div>
		            
		            <br><br>

		            <div style="background-color: #000; color:#fff; padding: 10px 0; text-align: center;">FDRY</div>
		            </div>
		        </body>
		        </html>
			';
			//$headers .= "From: FDRY <fdry@fdry.com> \r\n"; 
			$headers = "";
			$headers .= "From: FDRY <admin@fdry.com> \r\n";
			$headers .= 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			//$headers .= array('Content-Type: text/html; charset=UTF-8');
			$to = "andres@fdry.com";
			$subject = "FDRY New Brief";
			$mail = wp_mail($to, $subject, $message, $headers);

		}

	?>


	<div class="section">
		<div class="container">
			<div class="ui-step-content">
				<center>
					<img style="margin-top: 80px;" src="<?php echo get_stylesheet_directory_uri(); ?>/img/typeformimg/icon-thanks.svg">
					<h1 style="text-transform: uppercase;">We've received your brief!</h1>
					<h2 style="text-transform: uppercase;">Sit back and relax, we'll get in touch with you soon.</h2>
					<a class="morebtn" href="<?php echo get_the_permalink(50); ?>">Take a look at our work</a>
				</center>
			</div>
		</div>
	</div>



<script>
  dataLayer.push({
    'event':'ec_form_submit',
    'user_data': {
      "email": "<?php echo htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8'); ?>",
      "phone_number": "<?php echo htmlspecialchars($_POST['tel'], ENT_QUOTES, 'UTF-8'); ?>"
    }
  });
</script>

<?php }else{ ?>

		<div class="section">
			<div class="container">
			<div class="table table-full align-center">
			  <div class="table-row h100">
			    <div class="table-cell v-align-bottom">
			      <!-- ui-slide -->
			      <div class="relative">
			        <!-- card -->
			        <form class="formtype" action="?send=yes" method="post">
				        <div class="card">
				          <ul class="ui-formSlide">
				            <li id="step1" data-step="1" class="active">
				              <div class="ui-step-content in">
				              	<center>
					              	<img style="margin-top: 80px;" src="<?php echo get_stylesheet_directory_uri(); ?>/img/typeformimg/icon-welcome.svg">
					                <h1>Send us your brief</h1>
					                <h2 style="text-transform: uppercase;">We are ready to start working on your project. Calculate your budget and timescale to launch your digital business.</h2>

					                <!--<a id="startbtn" class="blackbuttom" href="javascript:void(0)">START</a>-->

					                <div class="pic blackbutton" style="margin-top: 177px;display: block;"><a id="startbtn" href="javascript:void(0)"><div class="button"><span>START</span></div></a></div>

				                </center>
				              </div>
				            </li>
				            <li id="step2" data-step="2" class="">
				              <div class="ui-step-content">
				              	<ul class="tabs">
								  <li><a class="li1 active" href="#panel1"><div class="circle"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/typeformimg/1-type.svg"></div><span>TYPE<br>&nbsp;</span></a></li>
								  <div class="dashedline"></div>
								  <li><a class="li2" href="#panel2"><div class="circle"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/typeformimg/2-business.svg"></div><span>BUSINESS<br>&nbsp;</span></a></li>
								  <div class="dashedline"></div>
								  <li><a class="li3" href="#panel3"><div class="circle"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/typeformimg/3-budget.svg"></div><span>BUDGET<br>&nbsp;</span></a></li>
								  <div class="dashedline"></div>
								  <li><a class="li1" href="#panel1"><div class="circle"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/typeformimg/4-details.svg"></div><span>MORE<br>DETAILS</span></a></li>
								  <div class="dashedline"></div>
								  <li><a class="li2" href="#panel2"><div class="circle"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/typeformimg/5-personal.svg"></div><span>PERSONAL<br>INFORMATION</span></a></li>
								 </ul>
				                <center>
				                	<h3>What type of project do you have in mind?</h3>
				                	<label class="labelop">
										<input type="radio" name="radio" value="Website Design"/>
										<span>Website Design</span>
									</label>
									<label class="labelop">
										<input type="radio" name="radio" value="Ecommerce Website" />
										<span>Ecommerce Website</span>
									</label>
									<label class="labelop">
										<input type="radio" name="radio" value="UI Design & UX" />
										<span>UI Design & UX</span>
									</label>
									<label class="labelop">
										<input type="radio" name="radio" value="Brand Design" />
										<span>Brand Design</span>
									</label>
									<label class="labelop">
										<input type="radio" name="radio" value="SEO" />
										<span>SEO</span>
									</label>
									<label class="labelop">
										<input type="radio" name="radio" value="Email marketing" />
										<span>Email marketing</span>
									</label>
									<!--<label class="labelop">
										<input type="radio" name="radio" value="Conversion Rate Optimisation" />
										<span>Conversion Rate Optimisation</span>
									</label>-->
									<!--<label class="labelop">
										<input type="radio" name="radio" value="Digital Advertising" />
										<span>Digital Advertising</span>
									</label>-->

									<label class="labelop">
										<input type="radio" name="radio" value="Organic social media" />
										<span>Organic social media</span>
									</label>

									<label class="labelop">
										<input type="radio" name="radio" value="Paid Media" />
										<span>Paid Media</span>
									</label>

									<br>
									<label class="cont">Other
									  <input id="otherfield" type="checkbox" name="radio" value="Other">
									  <span class="checkmark"></span>
									</label>


									<div class="areaother">
										<input type="text" name="otherproject" value="">
									</div>

									<div class="buttons">
									<a id="back1" class="whitebuttom" href="javascript:void(0)">BACK</a>
									<a id="next3" class="blackbuttom disable" href="javascript:void(0)">NEXT</a>
									</div>

				                </center>
				              </div>
				            </li>
				            <li id="step3" data-step="3">
				              <div class="ui-step-content">
				              	<ul class="tabs">
								  <li><a class="li1 active" href="#panel1"><div class="circle"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/typeformimg/1-type.svg"></div><span>TYPE<br>&nbsp;</span></a></li>
								  <div class="dashedline"></div>
								  <li><a class="li2 active" href="#panel2"><div class="circle"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/typeformimg/2-business.svg"></div><span>BUSINESS<br>&nbsp;</span></a></li>
								  <div class="dashedline"></div>
								  <li><a class="li3" href="#panel3"><div class="circle"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/typeformimg/3-budget.svg"></div><span>BUDGET<br>&nbsp;</span></a></li>
								  <div class="dashedline"></div>
								  <li><a class="li1" href="#panel1"><div class="circle"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/typeformimg/4-details.svg"></div><span>MORE<br>DETAILS</span></a></li>
								  <div class="dashedline"></div>
								  <li><a class="li2" href="#panel2"><div class="circle"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/typeformimg/5-personal.svg"></div><span>PERSONAL<br>INFORMATION</span></a></li>
								 </ul>
				                <center>
				                	<h3>What business category does your project fit into?</h3>
				                	<label class="boxlabel">
										<input type="radio" name="cat" value="B2B" />
										<span><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/typeformimg/business-btb.svg">B2B</span>
									</label>
									<label class="boxlabel">
										<input type="radio" name="cat" value="B2C" />
										<span><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/typeformimg/business-b2c.svg">B2C</span>
									</label>
									<label class="boxlabel">
										<input type="radio" name="cat" value="Marketplace" />
										<span><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/typeformimg/business-marketplace.svg">Marketplace</span>
									</label>

									<div class="buttons">
									<a id="back2" class="whitebuttom" href="javascript:void(0)">BACK</a>
									<a id="next4" class="blackbuttom disable" href="javascript:void(0)">NEXT</a>
									</div>

				                </center>
				              </div>
				            </li>
				            <li id="step4" data-step="4">
				              <div class="ui-step-content">
				              	<ul class="tabs">
								  <li><a class="li1 active" href="#panel1"><div class="circle"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/typeformimg/1-type.svg"></div><span>TYPE<br>&nbsp;</span></a></li>
								  <div class="dashedline"></div>
								  <li><a class="li2 active" href="#panel2"><div class="circle"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/typeformimg/2-business.svg"></div><span>BUSINESS<br>&nbsp;</span></a></li>
								  <div class="dashedline"></div>
								  <li><a class="li3 active" href="#panel3"><div class="circle"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/typeformimg/3-budget.svg"></div><span>BUDGET<br>&nbsp;</span></a></li>
								  <div class="dashedline"></div>
								  <li><a class="li1" href="#panel1"><div class="circle"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/typeformimg/4-details.svg"></div><span>MORE<br>DETAILS</span></a></li>
								  <div class="dashedline"></div>
								  <li><a class="li2" href="#panel2"><div class="circle"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/typeformimg/5-personal.svg"></div><span>PERSONAL<br>INFORMATION</span></a></li>
								 </ul>
				                <center>
				                	<h3>What is your budget?</h3>
				                	<label class="boxlabel">
										<input type="radio" name="budget" value="&pound;3,500 - &pound;8,500" />
										<span><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/typeformimg/budget-1.svg">&pound;3,500 - &pound;8,500</span>
									</label>
									<label class="boxlabel">
										<input type="radio" name="budget" value="&pound;8,500 - &pound;12,500" />
										<span><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/typeformimg/budger-2.svg">&pound;8,500 - &pound;12,500</span>
									</label>
									<label class="boxlabel">
										<input type="radio" name="budget" value="&pound;12,500 - &pound;18,000" />
										<span><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/typeformimg/budget-3.svg">&pound;12,000 - &pound;18,000</span>
									</label>
									<label class="boxlabel">
										<input type="radio" name="budget" value="&pound;18,000+" />
										<span><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/typeformimg/budget-4.svg">&pound;18,000+</span>
									</label>

									<div class="buttons">
									<a id="back3" class="whitebuttom" href="javascript:void(0)">BACK</a>
									<a id="next5" class="blackbuttom disable" href="javascript:void(0)">NEXT</a>
									</div>

				                </center>
				              </div>
				            </li>
				            <li id="step5" data-step="5">
				              <div class="ui-step-content">
				              	<ul class="tabs">
								  <li><a class="li1 active" href="#panel1"><div class="circle"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/typeformimg/1-type.svg"></div><span>TYPE<br>&nbsp;</span></a></li>
								  <div class="dashedline"></div>
								  <li><a class="li2 active" href="#panel2"><div class="circle"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/typeformimg/2-business.svg"></div><span>BUSINESS<br>&nbsp;</span></a></li>
								  <div class="dashedline"></div>
								  <li><a class="li3 active" href="#panel3"><div class="circle"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/typeformimg/3-budget.svg"></div><span>BUDGET<br>&nbsp;</span></a></li>
								  <div class="dashedline"></div>
								  <li><a class="li1 active" href="#panel1"><div class="circle"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/typeformimg/4-details.svg"></div><span>MORE<br>DETAILS</span></a></li>
								  <div class="dashedline"></div>
								  <li><a class="li2" href="#panel2"><div class="circle"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/typeformimg/5-personal.svg"></div><span>PERSONAL<br>INFORMATION</span></a></li>
								 </ul>
				                <center>
				                	<h3>Help us understand what kind of project you’re looking for so we can get back to you with a more accurate consultation:</h3>
				                	<textarea name="kind" placeholder="Tell us about your brand, what is this project for and the limitations…"></textarea>

				                	<div class="buttons">
									<a id="back4" class="whitebuttom" href="javascript:void(0)">BACK</a>
									<a id="next6" class="blackbuttom" href="javascript:void(0)">NEXT</a>
									</div>

				                </center>
				              </div>
				            </li>
				            <li id="step6" data-step="6">
				              <div class="ui-step-content">
				              	<ul class="tabs">
								  <li><a class="li1 active" href="#panel1"><div class="circle"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/typeformimg/1-type.svg"></div><span>TYPE<br>&nbsp;</span></a></li>
								  <div class="dashedline"></div>
								  <li><a class="li2 active" href="#panel2"><div class="circle"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/typeformimg/2-business.svg"></div><span>BUSINESS<br>&nbsp;</span></a></li>
								  <div class="dashedline"></div>
								  <li><a class="li3 active" href="#panel3"><div class="circle"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/typeformimg/3-budget.svg"></div><span>BUDGET<br>&nbsp;</span></a></li>
								  <div class="dashedline"></div>
								  <li><a class="li1 active" href="#panel1"><div class="circle"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/typeformimg/4-details.svg"></div><span>MORE<br>DETAILS</span></a></li>
								  <div class="dashedline"></div>
								  <li><a class="li2 active" href="#panel2"><div class="circle"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/typeformimg/5-personal.svg"></div><span>PERSONAL<br>INFORMATION</span></a></li>
								 </ul>
				                <div class="row">
				                	<div class="col-6">
				                		<h3>Full name *:</h3>
				                		<input type="text" id="fullname" name="fullname" required="required">
				                		<h3>Email *:</h3>
				                		<input type="email" id="email" name="email" required="required">
				                	</div>
				                	<div class="col-6">
				                		<h3>Company:</h3>
				                		<input type="text" id="comany" name="company">
				                		<h3>Telephone:</h3>
				                		<input type="tel" id="tel" name="tel" placeholder="E.g., +447712345678" required>
				                		<span id="error-msg" style="color: red; display: none; position: relative; top: -45px;">Invalid number</span>
				                	</div>
				                </div>
				                <center>
				                	<h3>Please call me back. Best time to call you:</h3>
				                	<label class="radiolabel">
										<input type="radio" name="timecall" value="9.00am - 12.00pm" />
										<span>9.00am - 12.00pm</span>
									</label>
									<label class="radiolabel">
										<input type="radio" name="timecall" value="12.00pm - 3.00pm" />
										<span>12.00pm - 3.00pm</span>
									</label>
									<label class="radiolabel">
										<input type="radio" name="timecall" value="3.00pm - 6.00pm" />
										<span>3.00pm - 6.00pm</span>
									</label>

									<div class="buttons">
										<a id="back5" class="whitebuttom" href="javascript:void(0)">BACK</a>
										<a class="blacksubmit" href="javascript:void(0)">SUBMIT</a>
										<input style="display: none;" id="submit6" type="submit" value="Submit" class="dis">
									</div>
				                
				                
				                </center>
				              </div>
				            </li>

				          </ul>
				        </div>
			    	</form>
			        <!-- ./card -->
			      </div>
			      <!-- .ui-slide -->
			    </div>
			  </div>
			  
			</div>  
			</div>
		</div>


				
<?php } ?>
			

<script src="<?php echo get_template_directory_uri(); ?>/mainjs/typeform.js"></script>

<script>
document.getElementById("tel").addEventListener("input", function (e) {
    let input = e.target;
    let value = input.value.replace(/[^\d+]/g, ""); // Allow only numbers and '+'
    let errorMsg = document.getElementById("error-msg");

    // Ensure '+' is only at the beginning
    if (value.includes("+") && value.indexOf("+") !== 0) {
        value = value.replace(/\+/g, ""); // Remove all '+'
        value = "+" + value; // Add '+' at the beginning
    }

    // Limit to 15 digits + 1 ('+' if present)
    if (value.startsWith("+")) {
        if (value.length > 16) value = value.slice(0, 16);
    } else {
        if (value.length > 15) value = value.slice(0, 15);
    }

    // Validate against E.164 format
    if (!/^\+\d{1,15}$/.test(value)) {
        if (!value.startsWith("+")) {
            errorMsg.textContent = "Number must start with '+'";
        } else {
            errorMsg.textContent = "Invalid number format";
        }
        errorMsg.style.display = "inline"; // Show error
    } else {
        errorMsg.style.display = "none"; // Hide error
    }

    input.value = value; // Update input value
});
document.getElementById("email").addEventListener("input", function (e) {
    e.target.value = e.target.value.toLowerCase(); // Convert input to lowercase
});
</script>


<?php get_footer(); ?>