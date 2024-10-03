<h1 class="text-center fw-bolder">Welcome to Establishment Visitor Management System</h1>
<hr class="mx-auto opacity-100" style="width:50px;height:3px">
<?php 
include_once("./Master.php");
?>
<div class="row">
    <div class="col-lg-4 col-md-6 col-sm-12 col-12">
        <div class="card rounded-0 shadow dash-box">
            <div class="card-body">
                <div class="dash-box-icon">
                    <span class="material-symbols-outlined">groups</span>
                </div>
                <div class="dash-box-title">Visitors Today</div>
                <div class="dash-box-text"><?= $master->today_visitors() ?></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-12 col-12">
        <div class="card rounded-0 shadow dash-box">
            <div class="card-body">
                <div class="dash-box-icon">
                    <span class="material-symbols-outlined">group</span>
                </div>
                <div class="dash-box-title">Unexited Visitors Today</div>
                <div class="dash-box-text"><?= $master->today_visitors_not_exited() ?></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-12 col-12">
        <div class="card rounded-0 shadow dash-box">
            <div class="card-body">
                <div class="dash-box-icon">
                    <span class="material-symbols-outlined">logout</span>
                </div>
                <div class="dash-box-title">Exited Visitors Today</div>
                <div class="dash-box-text"><?= $master->today_visitors_exited() ?></div>
            </div>
        </div>
    </div>
</div>