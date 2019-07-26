
<!DOCTYPE html>

<html>
<head>
    <title>Intelligent Surveillance Sysytem</title>
    <meta http-equiv='cache-control' content='no-cache'>	 
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link href="jquery.easy_slides.css" rel="stylesheet" type="text/css" />
    <style type="text/css">
        #results { padding:9px; border:1px solid; background:#ccc; }
        .btn {margin-bottom:10px; margin-top:10px; }
        .input-container {margin-right:15px;}
        .container h1 {margin:10px;}
        .input-row {margin-top: 20px; text-align: center;}
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>
    <script src="jquery.easy_slides.js"></script>
</head>
<body>
  
<div class="container">
	<div class="row">
            <div class="col">
            	<h1 style="font-family:verdana;font-size:50px" class="text-center"><img src="harmanlogo.png" alt="Harman" height="90" width="200"/>   Intelligent Surveillance Sysytem</h1>
            	
			</div>
	</div>
    <form name = "myform" id="myForm" action="" method="POST" enctype="multipart/form-data">
    <!--form method="POST" action=""-->
        <div class="row">
            <div class="col-6">
                <div id="my_camera"></div>
                <input type="hidden" name="image_snapshot" class="image-tag">
					<input type="hidden" name="image_train" class="image-train">               
            </div>
            <div class="col-6">
                <div id="results" class="readyToCapture col">Your captured image will appear here...</div>
            </div>
       </div>
        <div class="row input-row">
            <div class="col-5">

            	<div class="input-group mb-3 field-input-group">
				    <div class="input-group-prepend">
				      <span class="input-group-text">Name</span>
				    </div>
				    <input name="field_name" type="text" class="form-control field-name" placeholder="Enter the name">
			  	</div>
            	

				<!--input name="file_name" class="file-name" /-->
				
			</div>
		</div>
		<div class="row col-md-6">
            <div class="input-container">
				<input class="btn col btn-lg btn-primary take_snapshot" type="button" name="take_snapshot" value="Take Snapshot" />				
			</div>
			<div class="input-container">				
				<input class="btn col btn-lg btn-success train_image" type="button" name="train_image" value="Image Trainer" />
			</div>
			<div class="input-container">
				<input class="btn col btn-lg btn-secondary train_model" type="button" name="train_model" value="Model Trainer" />
			</div>
		</div>
        
    </form>

    <?php
	$dirname = "trainer/";
	$images = glob($dirname."*.jpeg");?>
	<div class="row">
    	<div class="col">
    <?php 
    if($images) {
    	$total = count($images);    	
    ?>
		    <div class="slider slider_four_in_line">

	<?php
			if($total > 0) {
			foreach($images as $image) {

			$imageName = $image."?time=" . date('Y-m-d H:i:s');
	?>

	   		<div><img src="<?php echo $imageName; ?>" style="width:100%;height:auto" /></div>
	<?php
			}
			if($total < 4) {
	?>
				<div>..</div>
				<div>..</div>
				<div>..</div>
	<?php
			}
			if($total > 4) {
	?>
			<div class="next_button"></div>
		    <div class="prev_button"></div>
	<?php
			}
		}
	}	
	?>
		       
		    </div>
	    </div>
    </div>

</div>



 <script language="Javascript">
 
    Webcam.set({
        width: 520,
        height: 390,
        image_format: 'jpeg',
        jpeg_quality: 90
    });
	$('.field-name').hide();
  	$('.field-input-group').hide();
    Webcam.attach( '#my_camera' );
	var url = "output/output";
	
	$('.slider_four_in_line').EasySlides({
                'autoplay': false,
            })
	// function imageExists(url, callback) {
	//   var img = new Image();
	//   img.onload = function() { callback(true); };
	//   img.onerror = function() { callback(false); };
	//   img.src = url;
	// }

	// imageExists(url, function(exists) {
	//   if(exists) {
	// 	  // document.getElementById('results').innerHTML = '';
	// 	  // document.getElementById('results').innerHTML = '<img id="prevImg" src="'+url+'"/>';
	// 	 // $('#results').show();
	//   }
	// });
	
	$("body .train_image").click(function(event){	
		event.preventDefault();
		
		// take snapshot and get image data
        Webcam.snap( function(data_uri) {
			$(".image-train").val(data_uri);
			$(".image-tag").val('');
			document.getElementById('results').innerHTML = '<img id="prevImg" src="'+data_uri+'"/>';			
			// Webcam.reset( '#my_camera' );
            // display results in page  
            document.getElementById("myForm").submit();	          
        });
	});
		
	$("body .train_model").click(function(event){
		event.preventDefault();
		$('.field-name').show();
		$('.field-input-group').show();
		Webcam.attach( '#my_camera' );
		//$('.field-name').val('model trainer in progress');
		//document.getElementById("myForm").submit();

	});
	
	$("body .field-name").blur(function(event){
		document.getElementById("myForm").submit();	
		//location.reload();
	});
	$("body .take_snapshot").click(function(event){
		//event.preventDefault();
        Webcam.snap( function(data_uri) {			
            $(".image-tag").val(data_uri);
			$(".image-train").val('');			
			document.getElementById('results').innerHTML = '<img id="prevImg" src="'+data_uri+'"/>';
			
				var base64image =  document.getElementById("prevImg").src;

				if(base64image){
					Webcam.upload( base64image, 'upload.php', function(code, text) {
						if(code) {		
							setTimeout(function(){

							var url = "output/output.jpeg?time=" + new Date();
							// cache.delete();
							document.getElementById('results').innerHTML ='';
							document.getElementById('results').innerHTML = '<img id="prevImg" src="'+url+'"/>';
							// location.reload();
							}, 2000);						 
						}					
					});	
				}
			});
		});
  
    

	  
 </script>
 
  <?php
  if(isset($_POST['field_name']) && $_POST['field_name'] !== ''){

    // $img = $_POST['image_train'];
	$name = $_POST['field_name'];

    // $folderPath = "upload/";
  
 //    $image_parts = explode(";base64,", $img);

 //    $image_type_au = explode("image/", $image_parts[0]);
 //    $image_type = $image_type_au[1];
	
 //    $image_base64 = base64_decode($image_parts[1]);
 //    $fileName = $name . '_' .uniqid() . '.jpeg';
  
 //    $file = $folderPath . $fileName;
	// file_put_contents($file, $image_base64);


	$postData =(object) array('name' => $name);
	$myJSON = json_encode($postData);	

	$ch = curl_init('http://127.0.0.1:5000/');
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt($ch, CURLOPT_POSTFIELDS, $myJSON);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
	                                      'Content-Length: ' . strlen($myJSON ))
										  );
	$response  = curl_exec($ch);
	curl_close($ch);	
 }
 
 if(isset($_POST['image_train'])  && $_POST['image_train'] !== '' && $_POST['field_name'] === '') {  

    $img = $_POST['image_train'];
    $folderPath = "trainer/";
  
    $image_parts = explode(";base64,", $img);
    $image_type_aux = explode("image/", $image_parts[0]);
    $image_type = $image_type_aux[1];
  
    $image_base64 = base64_decode($image_parts[1]);
    $fileName = uniqid() . '.jpeg';
  
    $file = $folderPath . $fileName;
    file_put_contents($file, $image_base64);
 }
 
   
?>
  </body>
</html>
