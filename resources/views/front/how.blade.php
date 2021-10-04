@extends('front.layout')
@section('content')
<style>
    li a:hover, li a:focus{
        background: inherit;
        text-decoration: none;
    }
</style>
<div id="main-wrapper" class="container">
                <div class="entry-content">
                    <div class="container">
						<p class="home_img"><img src="assets/frontend/images/howitworks_first.jpg"></p>
						<p class="home_text">Up to 50 video clips or pictures copied onto a custom-labeled HD DVD and delivered to your door.</p>
                        <p class="home_text">Up to 60 minutes of video clips per HD DVD.</p>
                        <p class="home_text">Upload from your phone, tablet, or personal computer.</p>
                        <p class="home_text">Order additional HD DVDs and have them sent directly to family and friends. They make a great gift or keepsake.</p>
                        <!--<p class="home_text">No contract, no risk. Cancel your subscription at any time. Signing up is free and you are never charged until you place your first order with Burn Video.</p>-->
                        <p class="home_text">No contract, no risk. No subscription, never locked in! Order when you want and as many as you want for the same low price. Signing up is free and you are never charged until you place your first order with Burn Video.</p>
						<div class="row">
							<div class="col-md-6" style="width:45%; float:left;margin:0px 10px 0px 10px;">
								<!-- <div class="hot-it-work"> -->
									<h2 class="home_text_left">How does the Burn Video app work?</h2>
									<ul class="video_text">
										<li>
											<span>Install the free app onto your phone or tablet from the 
											<a style="color: inherit;padding: 0px;display: inline-block" target="_blank" href="https://itunes.apple.com/us/app/burn-video-your-phones-videos/id1040524545?mt=8"> App Store </a>
											or
											<a style="color: inherit;padding: 0px;display: inline-block" target="_blank" href="https://itunes.apple.com/us/app/burn-video-your-phones-videos/id1040524545?mt=8"> Google Play Store </a>
											
											</span>
										</li>
										<li><span>Upload your videos and pictures to the app.</span></li>
										<li><span>Move them around and add text to them, If you'd like. Customize a title.</span></li>
										<li><span>Click Order. It's that simple!</span></li>
										<li><span>You'll receive your custom HD DVD in the mail in just a few days.</span></li>
									</ul>
								<!-- </div> -->
							</div>
							<div class="col-md-6" style="width:45%; float:right;mergin:0px 10px 0px 10px;">
								<!-- <div class="hot-it-work"> -->
									<h2 class="home_text_left">How does the website work?</h2>
									<ul class="video_text">
										<li><span>Simply create an account and log-in.</span></li>
										<li><span>Click Add/Remove Files. Choose the pictures and videos you want to use.</span></li>
										<li><span>Create a custom title for your DVD.</span></li>
										<li><span>Send additional DVD’s to family and friends if you’d like.</span></li>
										<li><span>Click Order. That’s it! We mail you your DVD in just a few days!</span></li>
									</ul>
								<!-- </div> -->

							</div>
						</div>
						<div class="row">
							<div class="col-md-6" style="width:45%; float:left;margin:0px 10px 0px 10px;">
								<h2 class="home_text02">CHECK US OUT ON YOUTUBE!</h2>
								<p class="home_text"><iframe width="100%" height="400" src="https://www.youtube.com/embed/videoseries?list=PL_PLJ3llPs__FiyL1fk4XFMwRU0n94Uni" frameborder="0" allowfullscreen=""></iframe></p>
							</div>
							<div class="col-md-6" style="width:45%; float:right;mergin:0px 10px 0px 10px;">
								<h2 class="home_text02">DOWNLOAD THE FREE APP HERE</h2>
								<p class="applelogo" style="padding-top:10px;">
									<a target="_blank" href="https://itunes.apple.com/us/app/burn-video-your-phones-videos/id1040524545?mt=8"> <img src="assets/frontend/images/iOS_app.png"> </a>
									<!-- <a target="_blank" href="https://play.google.com/store/apps/details?id=com.b24.burnvideo2"> <img src="assets/frontend/images/android_app.png"> </a>-->
								</p>
								<p class="applelogo" style="padding-top:40px;">
									<!--<a target="_blank" href="https://itunes.apple.com/us/app/burn-video-your-phones-videos/id1040524545?mt=8"> <img src="assets/frontend/images/iOS_app.png"> </a>-->
									<a target="_blank" href="https://play.google.com/store/apps/details?id=com.b24.burnvideo2"> <img src="assets/frontend/images/android_app.png"> </a>
								</p>
							</div>
						</div>
                        <p class="home_text"><img src="assets/frontend/images/logo2.png"></p>
                        <p class="home_img"><img src="burnvideologo.png"></p>
                    </div>
                </div>
</div><!-- Main Wrapper -->
@stop