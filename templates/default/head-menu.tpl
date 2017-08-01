<nav class="navbar navbar-static-top" role="navigation">
    <!-- Sidebar toggle button-->
    <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">{$lang.togglenav}</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </a>
    <div class="navbar-right">
        <ul class="nav navbar-nav">
            <!-- User Account: style can be found in dropdown.less -->
            <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="glyphicon glyphicon-time"></i>
                    <span id="clock" >&nbsp;</span>
                </a>
            </li>

            <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="glyphicon glyphicon-user"></i>
                    <span>{$client.username} <i class="caret"></i></span>
                </a>
                <ul class="dropdown-menu">
                    <!-- User image -->
                    <li class="user-header bg-light-blue">
                        <img src="{$getgravatar}" class="img-circle" alt="User Image" />
                        <p>
                            {$logginmessage}                           
                        </p>
                    </li>
                    <!-- Menu Footer-->
                    <li class="user-footer">
                        <div class="pull-left">
                            <a href="{$web_addr}/my.php" class="btn btn-default btn-flat">{$lang.editprofile}</a>
                        </div>
                        <div class="pull-right">
                            {if isset($smarty.session.adminlogin)}
                            <a href="{$web_addr}/{$smarty.session.returnurl}" class="btn btn-default btn-flat">{$lang.returnas} {$smarty.session.adminlogin}</a>
                            {else}
                            <a href="{$web_addr}/logout.php" class="btn btn-default btn-flat">{$lang.logout}</a>
                            {/if}
                        </div>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>