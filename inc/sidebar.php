        <div class="sidebar">
            <div class="site-width">
                <!-- START: Menu-->
                <ul id="side-menu" class="sidebar-menu">
                    <li class="dropdown active"><a href="javascript:void();"><i class="icon-home mr-1"></i> Administrative Panel</a>
                        <ul>
                            <?php if (!isset($_SESSION['vin'], $_SESSION["accreditationID"])) {
                            ?>
                                <li <?php if ($page == "dashboard") {
                                        echo "class='active'";
                                    } ?>><a href="dashboard"><i class="icon-home"></i>Dashboard</a></li>

                                <li <?php if ($page == "candidates") {
                                        echo "class='active'";
                                    } ?>><a href="candidates"><i class="fas fa-user-tie"></i>Candidate</a></li>
                                <li <?php if ($page == "offices") {
                                        echo "class='active'";
                                    } ?>><a href="offices"><i class="fas fa-sitemap"></i>Offices</a></li>
                                <li <?php if ($page == "voters") {
                                        echo "class='active'";
                                    } ?>><a href="voters"><i class="fas fa-users"></i>Voters</a></li>
                                <li <?php if ($page == "live-charts") {
                                        echo "class='active'";
                                    } ?>><a href="live-charts"><i class="fas fa-chart-bar"></i>Live Charts</a></li>
                                <li <?php if ($page == "results") {
                                        echo "class='active'";
                                    } ?>><a href="results"><i class="fas fa-poll"></i>Results</a></li>
                                <li <?php if ($page == "settings") {
                                        echo "class='active'";
                                    } ?>><a href="settings"><i class="fas fa-cogs"></i>Settings</a></li>
                            <?php
                            }
                            ?>
                        </ul>
                    </li>
                </ul>
                <!-- END: Menu-->
                <ol class="breadcrumb bg-transparent align-self-center m-0 p-0 ml-auto">
                    <li class="breadcrumb-item"><a href="javascript:void();">Application</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>
        </div>