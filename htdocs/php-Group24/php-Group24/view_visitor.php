<?php 
if(isset($_GET['id']) && $_GET['id'] > 0){
   
    $sql = "SELECT *, COALESCE((SELECT `fullname` FROM `user_list` where `user_list`.`user_id` = `visitor_list`.`user_id`),'N/A') as `encoded` FROM `visitor_list` where `visitor_id` = '{$_GET['id']}' ";
    $query = $conn->query($sql);
    $data = $query->fetchArray();
    $date_created = new DateTime($data['date_created'], new DateTimeZone('UTC'));
    $date_created->setTimezone(new DateTimeZone('Asia/Manila'));
    $date_created = $date_created->format("M d, Y g:i A");
    if(!is_null($data['date_out'])){
        $date_out = new DateTime($data['date_out'], new DateTimeZone('UTC'));
        $date_out->setTimezone(new DateTimeZone('Asia/Manila'));
        $date_out = $date_out->format("M d, Y g:i A");
    }
    if($data['status'] == 0 && $_SESSION['user_id'] != $data['user_id']){
        throw new ErrorException("Invalid ID or you don't have permission to access this page.");
        exit;
    }

}else{
    throw new ErrorException("This page requires a valid ID.");
}
$_SESSION['formToken']['visitors'] = password_hash(uniqid(), PASSWORD_DEFAULT);
$_SESSION['formToken']['visitorDetails'] = password_hash(uniqid(), PASSWORD_DEFAULT);
$_SESSION['formToken']['comment-form'] = password_hash(uniqid(), PASSWORD_DEFAULT);
?>
<h1 class="text-center fw-bolder">Visitor Details</h1>
<hr class="mx-auto opacity-100" style="width:50px;height:3px">
<div class="col-lg-8 col-md-10 col-sm-12 mx-auto py-3">
    <div class="card rounded-0 shadow">
        <div class="card-body rounded-0">
            <div class="container-fluid">
                
                <table class="table table-sm table-bordered">
                    <colgroup>
                        <col width="50%">
                        <col width="50%">
                    </colgroup>
                    <tbody>
                        <tr>
                            <td><b>Visitor ID #</b></td>
                            <td><?= $data['id_number'] ?? "" ?></td>
                        </tr>
                        <tr>
                            <td><b>Name</b></td>
                            <td><?= $data['fullname'] ?? "" ?></td>
                        </tr>
                        <tr>
                            <td><b>Email</b></td>
                            <td><?= $data['email'] ?? "N/A" ?></td>
                        </tr>
                        <tr>
                            <td><b>Contact #</b></td>
                            <td><?= $data['contact'] ?? "N/A" ?></td>
                        </tr>
                        <tr>
                            <td><b>Reason</b></td>
                            <td><?= $data['reason'] ?? "N/A" ?></td>
                        </tr>
                        <tr>
                            <td><b>Remarks</b></td>
                            <td><?= $data['remarks'] ?? "N/A" ?></td>
                        </tr>
                        <tr>
                            <td><b>Entered Date and Time</b></td>
                            <td><?= $date_created ?? "<div class='text-center'>------</div>" ?></td>
                        </tr>
                        <tr>
                            <td><b>Exited Date and Time</b></td>
                            <td><?= $date_out ?? "<div class='text-center'>------</div>" ?></td>
                        </tr>
                        <tr>
                            <td><b>Encoded By</b></td>
                            <td><?= $data['encoded'] ?? "" ?></td>
                        </tr>
                    </tbody>
                </table>
                
                <hr>
                <div class="text-center">
                    <?php if(isset($_GET['fromFeed'])): ?>
                        <a href="./" class="btn btn-sm btn-secondary rounded-0">Back</a>
                    <?php else: ?>
                        <a href="./?page=visitor" class="btn btn-sm btn-secondary rounded-0">Back to List</a>
                    <?php endif; ?>
                    <?php if(isset($data['visitor_id']) && is_null($data['date_out'])): ?>
                        <button type="button" data-id="<?= $data['visitor_id'] ?? "" ?>" class="btn btn-sm btn-info rounded-0 exit_data">Mark as Exited</button>
                    <?php endif; ?>

                    <?php if(isset($data['visitor_id']) && $data['user_id'] == $_SESSION['user_id']): ?>
                        <a href="./?page=manage_visitor&id=<?= $data['visitor_id'] ?>&toview=true" class="btn btn-sm btn-primary rounded-0">Edit</a>
                        <button type="button" data-id="<?= $data['visitor_id'] ?>" class="btn btn-sm btn-danger rounded-0 delete_data">Delete</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('.delete_data').on('click', function(e){
            e.preventDefault()
            var id = $(this).attr('data-id');
            start_loader()
            var _conf = confirm(`Are you sure to delete this visitor data? This action cannot be undone`);
            if(_conf === true){
                $.ajax({
                    url:'Master.php?a=delete_visitor',
                    method:'POST',
                    data: {
                        token: '<?= $_SESSION['formToken']['visitors'] ?>',
                        id: id
                    },
                    dataType:'json',
                    error: err=>{
                        console.error(err)
                        alert("An error occurred.")
                        end_loader()
                    },
                    success:function(resp){
                        if(resp.status == 'success'){
                            location.replace("./?page=visitor")
                        }else{
                            console.error(resp)
                            alert(resp.msg)
                        }
                        end_loader()
                    }
                })
            }else{
                end_loader()
            }
        })
        $('.exit_data').on('click', function(e){
            e.preventDefault()
            var id = $(this).attr('data-id');
            start_loader()
            var _conf = confirm(`Are you sure to mark this visitor as exited? This action cannot be undone`);
            if(_conf === true){
                $.ajax({
                    url:'Master.php?a=exit_visitor',
                    method:'POST',
                    data: {
                        token: '<?= $_SESSION['formToken']['visitors'] ?>',
                        id: id
                    },
                    dataType:'json',
                    error: err=>{
                        console.error(err)
                        alert("An error occurred.")
                        end_loader()
                    },
                    success:function(resp){
                        if(resp.status == 'success'){
                            location.reload()
                        }else{
                            console.error(resp)
                            alert(resp.msg)
                        }
                        end_loader()
                    }
                })
            }else{
                end_loader()
            }
        })
        $('.edit_comment').click(function(e){
            e.preventDefault();
            var comment_id = $(this).attr('data-id')
            start_loader()
            $.ajax({
                url: "Master.php?a=get_comment",
                method: "POST",
                data: {comment_id:comment_id, formToken: '<?= $_SESSION['formToken']['visitorDetails'] ?>'},
                dataType:"JSON",
                error: err=>{
                    alert("An error occurred while fetching the data.")
                    end_loader()
                    console.error(err)
                },
                success: function(resp){
                    console.log(typeof resp)
                    if(typeof resp === "object"){
                        var modal = $('#EditCommentModal')
                        modal.find('[name="comment_id"]').val(resp.comment_id)
                        modal.find('[name="comment"]').val(resp.comment)
                        modal.modal('show')
                    }else{
                        alert("An error occurred while fetching the data.")
                        console.error(resp)
                    }
                    end_loader()
                }
            })
        })
        $('.delete_comment').on('click', function(e){
            e.preventDefault()
            var comment_id = $(this).attr('data-id');
            start_loader()
            var _conf = confirm(`Are you sure to delete this comment? This action cannot be undone`);
            if(_conf === true){
                $.ajax({
                    url:'Master.php?a=delete_comment',
                    method:'POST',
                    data: {
                        token: '<?= $_SESSION['formToken']['visitorDetails'] ?>',
                        comment_id: comment_id
                    },
                    dataType:'json',
                    error: err=>{
                        console.error(err)
                        alert("An error occurred.")
                        end_loader()
                    },
                    success:function(resp){
                        if(resp.status == 'success'){
                            location.reload()
                        }else{
                            console.error(resp)
                            alert(resp.msg)
                        }
                        end_loader()
                    }
                })
            }else{
                end_loader()
            }
        })
        $('#comment-form, #update-comment-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            _this.find('button').attr('disabled',true)
            $.ajax({
                url:'./Master.php?a=save_comment',
                method:'POST',
                data:$(this).serialize(),
                dataType:'JSON',
                error:err=>{
                    console.log(err)
                    _el.addClass('alert alert-danger')
                    _el.text("An error occurred.")
                    _this.prepend(_el)
                    _el.show('slow')
                    _this.find('button').attr('disabled',false)
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        location.reload();
                        return false;
                    }else{
                        _el.addClass('alert alert-danger')
                    }
                    _el.text(resp.msg)

                    _el.hide()
                    _this.prepend(_el)
                    _el.show('slow')
                    _this.find('button').attr('disabled',false)
                }
            })
        })
    })
</script>
