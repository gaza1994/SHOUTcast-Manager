	<h1>
		SHOUTcast Panel
		<small>Tutorial Videos</small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> SHOUTcast Panel</a></li>
		<li class="active"><i class="fa fa-question"></i> Tutorial Videos</li>
	</ol>
</section>
<!-- Main content -->
<section class="content">
	<div class="row">
		<div class="col-lg-12 col-xs-12">
			<div class="box box-solid">
				<div class="box-header">
					<h3 class="box-title">Note</h3>
				</div><!-- /.box-header -->
				<div class="box-body">
					<p>These tutorials are best viewed in the highest quality available.<br />Please make sure you press the <i class="fa fa-fw fa-gear"></i> Settings icon, click <i>quality</i> and select <i>720p</i></p>
					<p>You can click on the software links below to download the software I used in the tutorials.</p>
					<p><small>Notice: VirtualDJ and Traktor are not free, free trials where used to make these videos. BUTT is freeware.</small></p>
				</div><!-- /.box-body -->
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-header">
					<h3 class="box-title">Tutorials Videos</h3>
				</div><!-- /.box-header -->
				<div class="box-body no-padding">
					<table class="table table-condensed table-hover">
						<tbody>
							<tr>
								<th style="width: 5%">&nbsp;</th>
								<th style="width: 70%">Tutorial</th>
								<th style="width: 20%">Software</th>
								<th style="width: 5%">Action</th>
							</tr>
							<tr>  
								<td>&nbsp;</td>
								<td>
									<a class="youtube" href="https://www.youtube.com/watch?v=3tGOd3TZ8zg">Streaming with VirtualDJ</a>
								</td>
								<td>
									<a href="http://www.virtualdj.com/" target=_blank>
										<span class="badge bg-green">VirtualDJ</span>
									</a>
								</td>
								<td>
									<a class="youtube" href="https://www.youtube.com/watch?v=3tGOd3TZ8zg">
										<span class="badge bg-blue">Watch</span>
									</a>
								</td>
							</tr>
							<tr>  
								<td>&nbsp;</td>
								<td>
									<a class="youtube" href="https://www.youtube.com/watch?v=KmLf1XuJ5q4&vq=hd720">Streaming with Winamp DSP</a>
								</td>
								<td>
									<a href="http://winampplugins.co.uk/Winamp/" target=_blank>
										<span class="badge bg-green">Winamp</span>
									</a>
									&nbsp;
									<a href="http://download.nullsoft.com/shoutcast/tools/shoutcast-dsp-2-3-5-windows.exe" target=_blank>
										<span class="badge bg-green">Winamp DSP</span>
									</a>
								</td>
								<td>
									<a class="youtube" href="https://www.youtube.com/watch?v=KmLf1XuJ5q4&vq=hd720">
										<span class="badge bg-blue">Watch</span>
									</a>
								</td>
							</tr>
							<tr>  
								<td>&nbsp;</td>
								<td>
									<a class="youtube" href="https://www.youtube.com/watch?v=w-bjb1QKwR8&vq=hd720">Streaming with Traktor</a>
								</td>
								<td>
									<a href="http://www.native-instruments.com/en/" target=_blank>
										<span class="badge bg-green">Traktor</span>
									</a>
									&nbsp;
									<a href="http://butt.sourceforge.net/" target=_blank>
										<span class="badge bg-green">BUTT</span>
									</a>
								</td>
								<td>
									<a class="youtube" href="https://www.youtube.com/watch?v=w-bjb1QKwR8&vq=hd720">
										<span class="badge bg-blue">Watch</span>
									</a>
								</td>
							</tr>
							<tr>  
								<td>&nbsp;</td>
								<td>
									<a class="youtube" href="https://www.youtube.com/watch?v=q5f4EchnH8E&vq=hd720">Streaming with BUTT</a>
								</td>
								<td>
									<a href="http://butt.sourceforge.net/" target=_blank>
										<span class="badge bg-green">BUTT</span>
									</a>
								</td>
								<td>
									<a class="youtube" href="https://www.youtube.com/watch?v=q5f4EchnH8E&vq=hd720">
										<span class="badge bg-blue">Watch</span>
									</a>
								</td>
							</tr>
						</tbody>
					</table>
				</div><!-- /.box-body -->
			</div>
		</div><!-- /.row -->
	</div>
</section>

<div class="modal fade" id="youtubevideo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width:40%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Graphic Stats</h4>
			</div>
			<div class="modal-body">
				<iframe src="#" seamless=seamless width="100%" height="60%" frameborder="0"></iframe>
			</div>
			<div class="modal-footer" id="">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script src=""></script>

<script type="text/javascript">
	{literal}
	(function(d, s, id){
	    var js, fjs = d.getElementsByTagName(s)[0];
	    if (d.getElementById(id)){ return; }
	    js = d.createElement(s); js.id = id;
	    js.onload = function(){
	        $(".youtube").YouTubeModal({autoplay:0, width:640, height:480});
	    };
	    js.src = "{/literal}{$template_dir}{literal}js/youtube.js";
	    fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'bootstrap-youtube-embed'));
	{/literal}
</script>