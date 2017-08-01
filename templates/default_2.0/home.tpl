	<h1>
	    SHOUTcast Panel
	    <small>Home</small>
	</h1>
	<ol class="breadcrumb">
	    <li><a href="#"><i class="fa fa-dashboard"></i> SHOUTcast Panel</a></li>
	    <li class="active">Home</li>
	</ol>
</section>
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
    	{$notice}
		<div class="box">
            <div class="box-header">
                <h3 class="box-title">{$lang.servers}</h3>
                <div class="box-tools">
					{if $userlevel eq '5'}
					{else}
                        <li class="dropdown pull-right">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                {$lang.servercontrols} <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="control.php?start-all=1">{$lang.startall}</a></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="control.php?stop-all=1">{$lang.stopall}</a></li>
                                <li role="presentation" class="divider"></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="control.php?restart-all=1">{$lang.restartall}</a></li>
                            </ul>
                        </li>
					{/if}
				</div>
            </div><!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover text-center">
                    <thead>
                        <tr>
                            <th>{$lang.servername}</th>
                            <th>{$lang.port}</th>
                            <th>{$lang.status}</th>
                            <th>{$lang.live}</th>
                            <th>{$lang.listners}</th>
                            <th>{$lang.action}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- FOREACH -->
						{foreach from=$servers item=server}
						    <tr>
						    	<td>
						    		<span id="server_{$server.id}">{$server.servername}
										{if !empty($server.message_notification)}
											<i style='color:#d9534f;' class='fa fa-exclamation-triangle' data-toggle="tooltip" data-original-title="You have a message notification on this server, click 'View Server'"></i>
										{/if}
						    		</span>
						    	</td>
						    	<td>
						    		<a href="http://{$config.host_addr}:{$server.PortBase}" target="sbd_{$server.PortBase}">{$server.PortBase}</a>
						    	</td>
						    	<td>
									{$server.status_html}
						    	</td>
						    	<td>
						    		{$server.live_info}
						    	</td>
						    	<td>
						    		{$server.listners}
						    	</td>
						    	<td>
						    		<a href="{$server.action_URL}">
						    			<i class="fa fa-arrow-circle-right"> {$lang.viewserver}</i>
						    		</a>
						    	</td>
						    </tr>
						{/foreach}
                        <!-- END FOREACH -->
					</tbody>
				</table>
			</div><!-- /.box-body -->
		</div>
    </div><!-- /.row -->
</div>