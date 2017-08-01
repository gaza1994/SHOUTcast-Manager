<?php
/*
 * @author: Scottish Borders Design
 * @script: SBD SHOUTcast Manager
 * @function: API Documentation Script
 * @website: http://scottishbordersdesign.co.uk/
 */

$domain = $config['web_addr'];
$nopost = array();
?>
<div class="box">
    <div class="box-header" style=" ">
        <h3 class="box-title">Server API</h3>
    </div><!-- /.box-header -->
    <div class="box-body table-responsive no-padding">
        <table class="table table-hover">
            <tbody><tr>
                <th>Action</th>
                <th>Status</th>
                <th>URL</th>
                <th>POST</th>
                <th>Descriptions</th>
            </tr>
<!-- Server Docs -->
            <tr>
                <td>Start Server</td>
                <td><span class="label label-success">Online</span></td>
                <td><?php echo $domain;?>/public/api/server/<span class="yourAPIKey">YOUR-API-KEY</span>/<span class="yourPortNumber">0000</span>/start/</td>
                <td><pre><?php print_r($nopost);?></pre></td>
                <td>Starts the server with current configuration.</td>
            </tr>
            <tr>
                <td>Stop Server</td>
                <td><span class="label label-success">Online</span></td>
                <td><?php echo $domain;?>/public/api/server/<span class="yourAPIKey">YOUR-API-KEY</span>/<span class="yourPortNumber">0000</span>/stop/</td>
                <td><pre><?php print_r($nopost);?></pre></td>
                <td>Stops the server.</td>
            </tr>
            <tr>
                <td>Server Status</td>
                <td><span class="label label-success">Online</span></td>
                <td><?php echo $domain;?>/public/api/server/<span class="yourAPIKey">YOUR-API-KEY</span>/<span class="yourPortNumber">0000</span>/status/</td>
                <td><pre><?php print_r($nopost);?></pre></td>
                <td>Returns '1' if server is on and returns '0' if server is off.</td>
            </tr>
            <?php
                $demo = array(  'settings' => '1',
                                'broadcast_password' => "PaSsWoRd!" 
                            );
            ?>
            <tr>
                <td>Change Broadcast Password</td>
                <td><span class="label label-success">Online</span></td>
                <td><?php echo $domain;?>/public/api/server/<span class="yourAPIKey">YOUR-API-KEY</span>/<span class="yourPortNumber">0000</span>/broadcast-password/</td>
                <td><pre><?php print_r($demo);?></pre></td>
                <td>Change the broadcast password.</td>
            </tr>
            <?php
                $demo = array(  'settings' => '1',
                                'song_history' => '10',
                                'public_server' => 'always'
                                );
            ?>
            <tr>
                <td>Change Server Settings</td>
                <td><span class="label label-success">Online</span></td>
                <td><?php echo $domain;?>/public/api/server/<span class="yourAPIKey">YOUR-API-KEY</span>/<span class="yourPortNumber">0000</span>/settings/</td>
                <td><pre><?php print_r($demo);?></pre></td>
                <td>Change basic server settings.</td>
            </tr>
                <tr>
                    <td colspan="5">
                        <span style="float:right;"><a href="<?php echo $config['web_addr'];?>/api/docs/SHOUTcast_Manager_Examples.zip" target="_blank" name="downloadExamples" class="btn btn-primary">Download Examples (1 kb)</a></span> 
                    </td>
                </tr>
            </tbody>
        </table>
    </div><!-- /.box-body -->
</div>
<div class="box">
    <div class="box-header" style=" ">
        <h3 class="box-title">Information API</h3>
    </div><!-- /.box-header -->
    <div class="box-body table-responsive no-padding">
        <table class="table table-hover">
            <tbody><tr>
                <th>Action</th>
                <th>Status</th>
                <th>URL</th>
                <th>POST</th>
                <th>Descriptions</th>
            </tr>
<!-- SHOUTcast Information -->
            <tr>
                <td>Current Playing Track</td>
                <td><span class="label label-success">Online</span></td>
                <td><?php echo $domain;?>/public/api/server/<span class="yourAPIKey">YOUR-API-KEY</span>/<span class="yourPortNumber">0000</span>/info/?info=song</td>
                <td><pre><?php print_r($nopost);?></pre></td>
                <td>Returns the current playing track</td>
            </tr>
            <tr>
                <td>Current Genre</td>
                <td><span class="label label-success">Online</span></td>
                <td><?php echo $domain;?>/public/api/server/<span class="yourAPIKey">YOUR-API-KEY</span>/<span class="yourPortNumber">0000</span>/info/?info=genre</td>
                <td><pre><?php print_r($nopost);?></pre></td>
                <td>Returns the current genre</td>
            </tr>
            <tr>
                <td>Current DJ (Automatic)</td>
                <td><span class="label label-success">Online</span></td>
                <td><?php echo $domain;?>/public/api/server/<span class="yourAPIKey">YOUR-API-KEY</span>/<span class="yourPortNumber">0000</span>/info/?info=dj</td>
                <td><pre><?php print_r($nopost);?></pre></td>
                <td>If a live show is booked it will show the dj booked else it will show the current live DJ title</td>
            </tr>
            <tr>
                <td>Current DJ (Live Manual)</td>
                <td><span class="label label-success">Online</span></td>
                <td><?php echo $domain;?>/public/api/server/<span class="yourAPIKey">YOUR-API-KEY</span>/<span class="yourPortNumber">0000</span>/info/?info=live-dj</td>
                <td><pre><?php print_r($nopost);?></pre></td>
                <td>The DJ's Server title will be returned</td>
            </tr>
            <tr>
                <td>Current DJ (Booked Manual)</td>
                <td><span class="label label-success">Online</span></td>
                <td><?php echo $domain;?>/public/api/server/<span class="yourAPIKey">YOUR-API-KEY</span>/<span class="yourPortNumber">0000</span>/info/?info=booked-dj</td>
                <td><pre><?php print_r($nopost);?></pre></td>
                <td>The booked show's DJ name will be returned</td>
            </tr>
                <tr>
                    <td colspan="5">
                        <span style="float:right;"><a href="<?php echo $config['web_addr'];?>/api/docs/SHOUTcast_Manager_Examples.zip" target="_blank" name="downloadExamples" class="btn btn-primary">Download Examples (1 kb)</a></span> 
                    </td>
                </tr>
            </tbody>
        </table>
    </div><!-- /.box-body -->
</div>
<!-- AutoDJ Stuff -->
<div class="box">
    <div class="box-header" style=" ">
        <h3 class="box-title">AutoDJ API</h3>
    </div><!-- /.box-header -->
    <div class="box-body table-responsive no-padding">
        <table class="table table-hover">
            <tbody><tr>
                <th>Action</th>
                <th>Status</th>
                <th>URL</th>
                <th>POST</th>
                <th>Descriptions</th>
            </tr>
            <tr>
                <td>Start AutoDJ</td>
                <td><span class="label label-success">Online</span></td>
                <td><?php echo $domain;?>/public/api/autodj/<span class="yourAPIKey">YOUR-API-KEY</span>/<span class="yourPortNumber">0000</span>/start/</td>
                <td><pre><?php print_r($nopost);?></pre></td>
                <td>Starts the AutoDJ with last saved playlist, if the server is Offline, this will start the server.</td>
            </tr>
            <tr>
                <td>Stop AutoDJ</td>
                <td><span class="label label-success">Online</span></td>
                <td><?php echo $domain;?>/public/api/autodj/<span class="yourAPIKey">YOUR-API-KEY</span>/<span class="yourPortNumber">0000</span>/stop/</td>
                <td><pre><?php print_r($nopost);?></pre></td>
                <td>Stops the AutoDJ.</td>
            </tr>
            <tr>
                <td>AutoDJ Status</td>
                <td><span class="label label-success">Online</span></td>
                <td><?php echo $domain;?>/public/api/autodj/<span class="yourAPIKey">YOUR-API-KEY</span>/<span class="yourPortNumber">0000</span>/status/</td>
                <td><pre><?php print_r($nopost);?></pre></td>
                <td>Returns '1' if AutoDJ is on and returns '0' if AutoDJ is off.</td>
            </tr>
            <?php
                $demo = array('settings' => '1',
                              'track_1' => 'Full-Track-Name-1.mp3',
                              'track_2' => 'Full-Track-Name-2.mp3',
                              'track_3' => 'Full-Track-Name-3.mp3');
            ?>
            <tr>
                <td>AutoDJ Playlist</td>
                <td><span class="label label-success">Online</span></td>
                <td><?php echo $domain;?>/public/api/autodj/<span class="yourAPIKey">YOUR-API-KEY</span>/<span class="yourPortNumber">0000</span>/playlist/</td>
                <td><pre><?php print_r($demo);?></pre></td>
                <td>Edits the AutoDJ Playlist with a list of tracks.</td>
            </tr>
            <?php
                $demo = array('settings' => '1',
                              'stream_title' => 'Your Stream Title',
                              'stream_genre' => 'Stream Genre',
                              'stream_website' => 'http://mywebsite.com/',
                              'shuffle' => '1');
            ?>
            <tr>
                <td>AutoDJ Settings</td>
                <td><span class="label label-success">Online</span></td>
                <td><?php echo $domain;?>/public/api/autodj/<span class="yourAPIKey">YOUR-API-KEY</span>/<span class="yourPortNumber">0000</span>/settings/</td>
                <td><pre><?php print_r($demo);?></pre></td>
                <td>Change the AutoDJ settings by posting an array of settings to the API.</td>
            </tr>
                <tr>
                    <td colspan="5">
                        <span style="float:right;"><a href="<?php echo $config['web_addr'];?>/api/docs/SHOUTcast_Manager_Examples.zip" target="_blank" name="downloadExamples" class="btn btn-primary">Download Examples (1 kb)</a></span> 
                    </td>
                </tr>
            </tbody>
        </table>
    </div><!-- /.box-body -->
</div>