<ul class="sidebar-menu">
    <li class="{if $currPage == 'home.php'}active{/if} {if $currPage == 'viewserver.php'}active{/if} {if $currPage == 'edit.php'}active{/if}">
        <a href="{$web_addr}/home.php">
            <i class="fa fa-home"></i> <span>{$lang.menuhome}</span>
        </a>
    </li>
    <li class="{if $currPage == 'widgets.php'}active{/if}">
        <a href="{$web_addr}/widgets.php">
            <i class="fa fa-code"></i> <span>{$lang.menuwidgets}</span>
        </a>
    </li>
    <li class="{if $currPage == 'media.php'}active{/if}">
        <a href="{$web_addr}/media.php">
            <i class="fa fa-music"></i> <span>{$lang.menumedia}</span>
        </a>
    </li>
    <li class="{if $currPage == 'api.php'}active{/if}">
        <a href="{$web_addr}/api.php">
            <i class="fa fa-cogs"></i> <span>{$lang.menuapi}</span>
            {$apistatus}
        </a>
    </li>

    {if $userlevel eq '5'}
        <li class="{if $currPage == 'new.php'}active{/if}">
            <a href="{$web_addr}/new.php">
                <i class="fa fa-th"></i> <span>{$lang.menunewserver}</span> <!-- <small class="badge pull-right bg-green">new</small> -->
            </a>
        </li>
        <li class="{if $currPage == 'users.php'}active{/if}">
            <a href="{$web_addr}/users.php">
                <i class="fa fa-user"></i> <span>{$lang.menuusermanagement}</span> <!-- <small class="badge pull-right bg-green">new</small> -->
            </a>
        </li>
        <li class="{if $currPage == 'eventlog.php' && $_GET['log'] == 'admin'}active{/if}">
            <a href="{$web_addr}/events/admin/">
                <i class="fa fa-bar-chart-o"></i>
                <span>{$lang.menueventlog}</span>
            </a>
        </li>
        <li class="{if $currPage == 'eventlog.php' && $_GET['log'] == 'api'}active{/if}">
            <a href="{$web_addr}/events/api/">
                <i class="fa fa-tasks"></i>
                <span>{$lang.menuapieventlog}</span>
            </a>
        </li>
        <li class="{if $currPage == 'news.php'}active{/if}">
            <a href="{$web_addr}/notices.php?edit=true">
                <i class="fa fa-edit"></i> <span>{$lang.menueditnotice}</span>
            </a>
        </li>
        <li class="{if $currPage == 'maintenance.php'}active{/if}">
            <a href="{$web_addr}/maintenance.php">
                <i class="fa fa-terminal"></i> <span>{$lang.menumaintenance}</span>
            </a>
        </li>
        <li class="{if $currPage == 'setup.php'}active{/if}">
            <a href="{$web_addr}/setup.php">
                <i class="fa fa-cog"></i> <span>{$lang.menusettings}</span>
            </a>
        </li>
    {/if}

    <li class="{if $currPage == 'tutorials.php'}active{/if}">
        <a href="{$web_addr}/tutorials.php">
            <i class="fa fa-question"></i> <span>{$lang.menututorial}</span>
        </a>
    </li>

    {if isset($smarty.session.adminlogin)}
        <li class="{if $currPage == 'logout.php'}active{/if}">
            <a href="{$web_addr}/{$smarty.session.returnurl}">
                <i class="fa fa-arrow-left"></i> <span>{$lang.returnas} {$smarty.session.adminlogin}</span>
            </a>
        </li>
    {else}
        <li class="{if $currPage == 'logout.php'}active{/if}">
            <a href="{$web_addr}/logout.php">
                <i class="fa fa-ban"></i> <span>{$lang.logout}</span>
            </a>
        </li>
    {/if}      
</ul>