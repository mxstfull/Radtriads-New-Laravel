        		

		
				
        	</div>
        </div>
        
        <div class="row">
			<div class="col-md-12">
		        <footer class="footer-homepage">
			        <div class="row clearfix" style="margin-bottom: 20px;">
				        <div class="col-md-4" style="text-align: center;">
					        <a href="page.php?id=4">Terms of Use</a><br>
					        <a href="page.php?id=5">Privacy policy</a>
				        </div>
				        <div class="col-md-4" style="text-align: center;">
					        
					        <a href="about.php">About us</a><br>
					        <a href="mailto:support@radtriads.com">Contact us</a>
				        </div>
				        <div class="col-md-4" style="text-align: center;">
					        
					        <a href="mailto:support@radtriads.com">Support</a><br>
					        <a href="faq.php">FAQ</a>
				        </div>
			        </div>
					<div class="copyright-container">
						<div class="copyright-text">
							Copyright RadTriads Inc. 2020
						</div>
						<div class="social-footer-container">
							<a class="footer-fb" href="https://www.facebook.com/RadTriads" target="_blank"><i class="fab fa-facebook"></i></a>
							<a class="footer-twitter" href="https://twitter.com/radtriads" target="_blank"><i class="fab fa-twitter"></i></a>
						</div>
					</div>
				</footer>
			</div>
		</div>
        
        <!-- Modal -->
		<div class="modal fade" id="ten_days_left_modal" tabindex="-1" role="dialog" aria-hidden="true">
		    <div class="modal-dialog" role="document">
		        <div class="modal-content">
		            <div class="modal-header">
			            <?php
				        if(!isset($days_left_before_trial_ends)) {
					        $days_left_before_trial_ends = "-";
				        }  
				        ?>
		                <h5 class="modal-title" id="exampleModalLongTitle"><span class="action-text"><?php echo $days_left_before_trial_ends; ?> days left before your trial ends!</span></h5>
		                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		                    <span aria-hidden="true">&times;</span>
		                </button>
		            </div>
		            <div class="modal-body">
			            <p style="text-align: center">
			            	<img width="100" src="img/danger.png" />
			            </p>
		                <p style="margin-bottom: 0; text-align: center;">
			                We wanted to inform you that your free trial will expire in <strong><?php echo $days_left_before_trial_ends; ?> days</strong>! You should upgrade soon to a paid plan to continue to use our platform.
		                </p>
		            </div>
		            <div class="modal-footer">
			            <div style="width: 100%">
				            <div class="row">
					            <div class="col-md-12">
									<div class="text-center">
										<a href="switch-plan.php" class="btn btn-primary"><i class="fas fa-check"></i> <span class="action-text">Upgrade Now</a>
									</div>
					            </div>
				            </div>
			            </div>
		            </div>
		        </div>
		    </div>
		</div>
        

		<div class="bottom_actions_bar sticky-bottom">
			
			<div class="nb_files_selected">
				
			</div>
			
			<div class="container d-flex align-items-center">
				<ul class="nav">
					<li class="nav-item">
						<a class="btn btn-danger btn-delete-files" href="#"><i class="fas fa-times"></i> Delete Selected</a>
					</li>
					<li class="nav-item">
						<a class="btn btn-primary btn-copy-files" href="#"><i class="fas fa-copy"></i> Copy Selected</a>
					</li>
					<li class="nav-item">
						<a class="btn btn-primary btn-move-files" href="#"><i class="fas fa-share-alt"></i> Move Selected</a>
					</li>
				</ul>
			</div>
			
		</div>
        

		<div class="bottom_delete_actions_bar sticky-bottom">
			
			<div class="container d-flex align-items-center">
				<ul class="nav">
					<li class="nav-item">
						<a class="btn btn-danger btn-delete-permanently-files" href="#"><i class="fas fa-ban"></i> Delete Permanently</a>
					</li>
					<li class="nav-item">
						<a class="btn btn-primary btn-recover-files" href="#"><i class="fas fa-trash-restore"></i> Recover Files</a>
					</li>
				</ul>
			</div>
			
		</div>
		
		<a id="backtop"></a>
		
        <!-- Bootstrap core JavaScript-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="js/bootstrap.bundle.min.js"></script>
        <!-- Core plugin JavaScript-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
        <!-- Custom scripts for all pages-->
        <script src="js/sb-admin-2.min.js"></script>
        <script src="js/jquery.sticky-sidebar.min.js"></script>
        <script type="text/javascript" src="js/jquery.lazy.js"></script>
        <script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
		<script src="https://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.js"></script>
		<script src="https://vjs.zencdn.net/7.8.4/video.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/videojs-flash@2/dist/videojs-flash.min.js"></script>
	    
	    <?php
		echo $analytics_code;  
		?>
		
		<!-- Modal -->
		<div class="modal fade" id="modal-video" tabindex="-1" role="dialog" aria-hidden="true">
		    <div class="modal-dialog" role="document">
		        <div class="modal-content">
		            <div class="modal-header">
			            <h5 class="modal-title" id="exampleModalLongTitle"><span class="action-text">Video Preview</span></h5>
		                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		                    <span aria-hidden="true">&times;</span>
		                </button>
		            </div>
		            <div class="modal-body">
		
						<video data-setup='{"controls" : true, "autoplay" : true, "preload" : "auto"}' class="video-js vjs-default-skin" width="640" height="320" controls id="myVideo">
						    <source src="" type="">
						    Your browser doesn't support HTML5 video tag.
						</video>
						
		            </div>
		        </div>
		    </div>
		</div>
		
		<script type="text/javascript">
		
		$(document).ready(function(e) {
			
			var btn = $('#backtop');	
			
				
			var $vid_obj;
			
			
			$(document).on("click", ".btn-video-play", function(e) {
				
				e.preventDefault();
				
				$vid_obj = videojs("myVideo");
				
				var video_src = $(this).attr("data-url");
				var video_type = $(this).attr("data-type");
				
				$vid_obj.src({
				  type: video_type,
				  src: video_src
				});
				
				/*			
				$("#myVideo").find("source").attr("src", video_src);
				$("#myVideo").find("source").attr("type", video_type);
				
				$("#myVideo")[0].load();
				$("#myVideo")[0].play();
				*/
				
				$("#modal-video").modal("show");
				$vid_obj.load();
				$vid_obj.play();
				
			});	
			
			$('#modal-video').on('hidden.bs.modal', function () {
				$vid_obj.pause();
			})

			$(window).scroll(function() {
				if ($(window).scrollTop() > 300) {
					btn.addClass('show');
				} else {
					btn.removeClass('show');
				}
			});
			
			btn.on('click', function(e) {
				e.preventDefault();
				$('html, body').animate({scrollTop:0}, '300');
			});
			
			$(".top_header .dropdown-item").click(function(e) {
				
				e.preventDefault();
				
				if($(this).attr("data-url")) {		
					window.location.href = $(this).attr("data-url");
				}
								
				
			});
			
		});
			
		</script>
		
        
        <?php
	    foreach($js_files as $js_f) {  
	    ?>
        <script type="text/javascript" src="<?php echo $js_f; ?>?v=<?php echo mt_rand(0,99999); ?>"></script>
        <?php
	    }
	    ?>
	    
	    
	    
	    <script type="text/javascript">
		    
		    <?php
			if(isset($show_10_days_left_popup)) {
			?>
			var show_10_days_left_popup = "<?php echo $show_10_days_left_popup; ?>";
			<?php
			} else {
			?>
			var show_10_days_left_popup = "0";
			<?php
			} 
			?>
		    
		</script>
	    
	    <script type="text/javascript" src="js/pages/all_pages.js?v=<?php echo mt_rand(0,99999); ?>"></script>
    </body>
</html>