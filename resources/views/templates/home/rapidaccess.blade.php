<?php
		function createBlock($image, $route, $button, $class, $text) {
			$rapidAccess =
        "
          <div class='col-md-6'>
            <div class='row'>
              <div class='col-md-1'>
              </div>
              <div class='col-md-5'>
								<a href='".$route."'>
	                <img align='right' style=' width: 90%; height: 90%'' src='".$image."' alt='' class='img-rounded'>
								</a>
              </div>
              <div class='col-md-5'>
                <div class='row'>
                  <a href='".$route."' onClick='holaFun()' class='btn btn-".$class."' style='display:inline;'>".$button."</a>
                </div>
                <div class='row'>
                  <div class='bs-callout bs-callout-info'>
                    <p>".$text."</p>
                  </div>
                </div>
              </div>
              <div class='col-md-1'>
              </div>
            </div>
          </div>
    			";

		return $rapidAccess;
		}
