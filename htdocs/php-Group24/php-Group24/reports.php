<?php 
$_SESSION['formToken']['visitors'] = password_hash(uniqid(),PASSWORD_DEFAULT);
$from = isset($_GET['from']) ? date("Y-m-d h:i:s", strtotime($_GET['from']. " 00:00:00")) : date("Y-m-d h:i:s", strtotime(date("Y-m-d"). " 00:00:00"));
$to = isset($_GET['to']) ? date("Y-m-d h:i:s", strtotime($_GET['to']. " 23:59:59")) : date("Y-m-d h:i:s", strtotime(date("Y-m-d"). " 23:59:59"));
?>

<?php 
/**
 * Print View Details
 */
$report_title = "Date-wise Visitor Records Report";
if(date("F d, Y", strtotime($from)) == date("F d, Y", strtotime($to))){
    $report_date = date("F d, Y", strtotime($from));
}else if(date("F-Y", strtotime($from)) == date("F-Y", strtotime($to))){
    $report_date = date("F ", strtotime($from));
    $report_date .= date("d", strtotime($from));
    $report_date .= date("-d", strtotime($to));
    $report_date .= date(", Y", strtotime($from));
}else{
    $report_date = date("F d, Y", strtotime($from)) ." - ". date("F d, Y", strtotime($to));
}
?>
<style>
    #visitorTBL .btn-group .btn-sm{
        line-height: .9rem !important;
        padding: 5px;
    }
    #visitorTBL .btn-group .material-symbols-outlined{
        line-height: .9rem !important;
        font-size: .85rem !important;
    }
    #input-search-field input{
        border-top-left-radius:3em;
        border-bottom-left-radius:3em;
    }
    #input-search-field span.input-group-text{
        border-top-right-radius:50%;
        border-bottom-right-radius:50%;
    }
</style>
<h1 class="text-center fw-bolder">Date-wise Visitor Record Reports</h1>
<hr class="mx-auto opacity-100" style="width:50px;height:3px">
<div class="col-lg-12 col-md-12 col-sm-12 mx-auto py-3">
    <div class="card rounded-0 shadow">
        <div class="card-body rounded-0">
            <div class="container-fluid">
                <div class="mb-3">
                    <div class="row align-items-end justify-content-center">
                        <div class="col-lg-4 col-md-5 col-sm-12 col-12">
                            <label for="date_from">Date From</label>
                            <input type="date" value="<?= date("Y-m-d", strtotime($from)) ?>" class="form-control form-control-sm rounded-0" id="date_from" name="date_from" required="required">
                        </div>
                        <div class="col-lg-4 col-md-5 col-sm-12 col-12">
                            <label for="date_to">Date To</label>
                            <input type="date" value="<?= date("Y-m-d", strtotime($to)) ?>" class="form-control form-control-sm rounded-0" id="date_to" name="date_to" required="required">
                        </div>
                        <div class="col-auto">
                            <div class="d-flex">
                                <button class="btn btn-primary btn-sm rounded-0 d-flex align-items-center me-2" id="filter"><span class="material-symbols-outlined">filter_alt</span> Filter</button>
                                <button class="btn btn-success btn-sm rounded-0 d-flex align-items-center" id="print"><span class="material-symbols-outlined">print</span> Print</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover table-striped" id="visitorTBL">
                        <thead>
                            <tr>
                                <th class="text-center">ID</th>
                                <th class="text-center">Entry DateTime</th>
                                <th class="text-center">Exit DateTime</th>
                                <th class="text-center">Visitor</th>
                                <th class="text-center">Reason</th>
                                <th class="text-center">Remarks</th>
                                <th class="text-center">Encoded by</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $from = new DateTime($from, new DateTimeZone('Asia/Manila'));
                            $from->setTimezone(new DateTimeZone('UTC'));
                            $from = $from->format("Y-m-d");
                            $to = new DateTime($to, new DateTimeZone('Asia/Manila'));
                            $to->setTimezone(new DateTimeZone('UTC'));
                            $to = $to->format("Y-m-d");
                            $i = 1;
                            $visitors_sql = "SELECT *, COALESCE((SELECT `fullname` FROM `user_list` where `user_list`.`user_id` = `visitor_list`.`user_id`),'N/A') as `encoded` FROM `visitor_list` where date(`date_created`) BETWEEN '{$from}' and '{$to}' ORDER BY strftime('%s', `date_created`) desc";
                            
                            $visitors_qry = $conn->query($visitors_sql);
                            while($row = $visitors_qry->fetchArray()):
                                $date_created = new DateTime($row['date_created'], new DateTimeZone('UTC'));$date_created->setTimezone(new DateTimeZone('Asia/Manila'));

                                if($row['date_out']){
                                    $date_out = new DateTime($row['date_out'], new DateTimeZone('UTC'));
                                    $date_out->setTimezone(new DateTimeZone('Asia/Manila'));
                                    $date_out = $date_out->format('M d, Y g:i A');
                                }
                            ?>
                            <tr>
                                <td class="text-center"><?= $i++; ?></td>
                                <td class="text-center"><?= $date_created->format('M d, Y g:i A') ?></td>
                                <td class="text-center"><?= $date_out ?? "-----" ?></td>
                                <td class="">
                                    <div class="lh-1">
                                        <div class="text-secondary">ID #: <?= $row['id_number'] ?></div>
                                        <div><?= $row['fullname'] ?></div>
                                        <div class="text-secondary">Contact #: <?= $row['contact'] ?></div>
                                        <div class="text-secondary">Email: <?= $row['email'] ?></div>
                                    </div>
                                </td>
                                <td class=""><small><?= $row['reason'] ?></small></td>
                                <td class=""><small><?= $row['remarks'] ?></small></td>
                                <td class=""><?= $row['encoded'] ?></td>
                            </tr>
                            <?php endwhile; ?>
                            <?php if(!$visitors_qry->fetchArray()): ?>
                                <tr>
                                    <td colspan="7" class="text-center">No data found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function(){
        $('#filter').click(function(e){
            e.preventDefault()
            var from = $('#date_from').val()
            var to = $('#date_to').val()
            location.replace(`./?page=reports&from=${from}&to=${to}`)
        })
        $('#print').click(function(){
            var _head = ``
            $('link').each(function(){
                _head += `<link rel="${$(this)[0].rel}"  href="${$(this)[0].href}">`
            })

            var report_header = `<h3 class="text-center"><?= $report_title ?></h3>`
                report_header += `<h5 class="text-center"><?= $report_date ?></h5>`
                report_header += `<hr>`

            var report_content = $('#visitorTBL').clone()
            report_content.removeClass('table-hover')
            start_loader()

            var nw = window.open("", "_blank", `width=${window.outerWidth}px,height=${window.outerHeight}px`)
                nw.document.querySelector('head').innerHTML = _head
                nw.document.body.innerHTML = report_header
                nw.document.body.innerHTML += report_content[0].outerHTML
                nw.document.close()
                setTimeout(() => {
                    nw.print()
                    setTimeout(() => {
                        nw.close();
                        end_loader();
                    }, 100);
                }, 500);
        })

    })
</script>
