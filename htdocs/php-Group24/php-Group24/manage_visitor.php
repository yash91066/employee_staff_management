<?php 
if(isset($_GET['id']) && $_GET['id'] > 0){
   
    $sql = "SELECT * FROM `visitor_list` where `visitor_id` = '{$_GET['id']}' ";
    $query = $conn->query($sql);
    $data = $query->fetchArray();

}

// Generate Manage visitor Form Token
$_SESSION['formToken']['visitor-form'] = password_hash(uniqid(),PASSWORD_DEFAULT);
?>
<h1 class="text-center fw-bolder"><?= isset($data['visitor_id']) ? "Update visitor Details" : "Add New visitor" ?></h1>
<hr class="mx-auto opacity-100" style="width:50px;height:3px">
<div class="col-lg-6 col-md-8 col-sm-12 col-12 mx-auto">
    <div class="card rounded-0">
        <div class="card-body">
            <div class="container-fluid">
                <form action="" id="visitor-form">
                    <input type="hidden" name="formToken" value="<?= $_SESSION['formToken']['visitor-form'] ?>">
                    <input type="hidden" name="visitor_id" value="<?= $data['visitor_id'] ?? '' ?>">
                    <div class="mb-3">
                        <label for="id_number" class="text-body-tertiary">ID Number</label>
                        <input type="text" class="form-control rounded-0" id="id_number" name="id_number" required="required" autofocus value="<?= $data['id_number'] ?? "" ?>">
                    </div>
                    <div class="mb-3">
                        <label for="fullname" class="text-body-tertiary">Fullname</label>
                        <input type="text" class="form-control rounded-0" id="fullname" name="fullname" required="required"  value="<?= $data['fullname'] ?? "" ?>">
                    </div>
                    <div class="mb-3">
                        <label for="contact" class="text-body-tertiary">Contact</label>
                        <input type="text" class="form-control rounded-0" id="contact" name="contact" required="required"  value="<?= $data['contact'] ?? "" ?>">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="text-body-tertiary">Email</label>
                        <input type="email" class="form-control rounded-0" id="email" name="email" value="<?= $data['email'] ?? "" ?>">
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="text-body-tertiary">Reason</label>
                        <textarea rows="5" class="form-control rounded-0" id="reason" name="reason" required="required" ><?= $data['reason'] ?? "" ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="remarks" class="text-body-tertiary">Remarks/Other Information</label>
                        <textarea rows="5" class="form-control rounded-0" id="remarks" name="remarks" ><?= $data['remarks'] ?? "" ?></textarea>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-footer">
            <div class="row justify-content-evenly">
                <button class="btn col-lg-4 col-md-5 col-sm-12 col-12 btn-primary rounded-0" form="visitor-form">Save</button>
                <a class="btn col-lg-4 col-md-5 col-sm-12 col-12 btn-secondary rounded-0" href='./?page=visitor'>Cancel</a>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('#visitor-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            _this.find('button').attr('disabled',true)
            $.ajax({
                url:'./Master.php?a=save_visitor',
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
                        if('<?= $_GET['toview'] ?? "" ?>' == ""){
                            location.replace("./?page=visitors");
                        }else{
                            location.replace("./?page=view_visitor&id=<?= $data['visitor_id'] ?? "" ?>");
                        }
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