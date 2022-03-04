<?php
require_once'php/core/init.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();

$successMessage=null;$pageError=null;$errorMessage=null;$noE=0;$noC=0;$noD=0;
$users = $override->getData('user');
if($user->isLoggedIn()) {
    if(Input::exists('post')){
        if(Input::get('edit_file_status')){
            if(Input::get('status')==1){$st=0;}else{$st=2;}
            try {
                $user->updateRecord('file_request', array('status' => $st,'approved_on'=>date('Y-m-d H:i:s'),'approve_staff'=>$user->data()->id), Input::get('request_id'));
                $user->updateRecord('study_files',array('status'=>1),Input::get('id'));
            } catch (Exception $e) {
                $e->getMessage();
            }
            $successMessage='File Status changed successful';
        }
    }
}else{
    Redirect::to('index.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title> Dashboard - FileTrack </title>
    <?php include "head.php";?>
</head>
<body>
<div class="wrapper">

    <?php include 'topbar.php'?>
    <?php include 'menu.php'?>
    <div class="content">


        <div class="breadLine">

            <ul class="breadcrumb">
                <li><a href="#">Dashboard</a> <span class="divider">></span></li>
            </ul>
            <?php include 'pageInfo.php'?>
        </div>

        <div class="workplace">

            <div class="row">

                <div class="col-md-3">

                    <div class="wBlock red clearfix">
                        <div class="dSpace">
                            <h3>Studies</h3>
                            <span class="mChartBar" sparkType="bar" sparkBarColor="white"><!--130,190,260,230,290,400,340,360,390--></span>
                            <span class="number"><?=$override->getNo('study')?></span>
                        </div>
                    </div>

                </div>

                <div class="col-md-3">

                    <div class="wBlock green clearfix">
                        <div class="dSpace">
                            <h3>Files</h3>
                            <span class="mChartBar" sparkType="bar" sparkBarColor="white"><!--5,10,15,20,23,21,25,20,15,10,25,20,10--></span>
                            <span class="number"><?=$override->getNo('study_files')?></span>
                        </div>
                    </div>

                </div>

                <div class="col-md-3">

                    <div class="wBlock blue clearfix">
                        <div class="dSpace">
                            <h3>Circulating Files</h3>
                            <span class="mChartBar" sparkType="bar" sparkBarColor="white"><!--240,234,150,290,310,240,210,400,320,198,250,222,111,240,221,340,250,190--></span>
                            <span class="number"><?=$override->getCount('study_files','status',1)?></span>
                        </div>

                    </div>

                </div>

                <div class="col-md-3">
                    <div class="wBlock yellow clearfix">
                        <div class="dSpace">
                            <h3>Free Files</h3>
                            <span class="mChartBar" sparkType="bar" sparkBarColor="white"><!--240,234,150,290,310,240,210,400,320,198,250,222,111,240,221,340,250,190--></span>
                            <span class="number"><?=$override->getCount('study_files','status',0)?></span>
                        </div>
                    </div>

                </div>

            </div>

            <div class="dr"><span></span></div>
            <div class="row">
                <div class="col-md-12">
                    <?php if($errorMessage){?>
                        <div class="alert alert-danger">
                            <h4>Error!</h4>
                            <?=$errorMessage?>
                        </div>
                    <?php }elseif($pageError){?>
                        <div class="alert alert-danger">
                            <h4>Error!</h4>
                            <?php foreach($pageError as $error){echo $error.' , ';}?>
                        </div>
                    <?php }elseif($successMessage){?>
                        <div class="alert alert-success">
                            <h4>Success!</h4>
                            <?=$successMessage?>
                        </div>
                    <?php }?>
                    <div class="head clearfix">
                        <div class="isw-grid"></div>
                        <h1>File Requests</h1>
                        <ul class="buttons">
                            <li><a href="#" class="isw-download"></a></li>
                            <li><a href="#" class="isw-attachment"></a></li>
                            <li>
                                <a href="#" class="isw-settings"></a>
                                <ul class="dd-list">
                                    <li><a href="#"><span class="isw-plus"></span> New document</a></li>
                                    <li><a href="#"><span class="isw-edit"></span> Edit</a></li>
                                    <li><a href="#"><span class="isw-delete"></span> Delete</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class="block-fluid">
                        <table cellpadding="0" cellspacing="0" width="100%" class="table">
                            <thead>
                            <tr>
                                <th width="25%">File</th>
                                <th width="15%">Requested Date</th>
                                <th width="15%">Requested Staff</th>
                                <th width="15%">File Status</th>
                                <th width="15%">Request Status</th>
                                <th width="25%">Manage</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($override->get('file_request','status',0) as $fileRequest){
                                $file=$override->get('study_files','id',$fileRequest['file_id'])[0];
                                $staff=$override->get('user','id',$fileRequest['requesting_staff_id'])[0];
                                $own=$override->getNews('file_request','status',1,'file_id',$fileRequest['file_id'])[0];
                                $stf=$override->get('user','id',$own['staff_id'])[0]?>
                                <tr>
                                    <td><?=$file['name']?></td>
                                    <td><?=$fileRequest['create_on']?></td>
                                    <td><?=$staff['firstname'].' '.$staff['lastname']?></td>
                                    <td>
                                        <?php if($file['status']==1){?>
                                            <button class="btn btn-sm btn-danger" type="button" disabled>Circulating </button> <?=$stf['firstname']?>
                                        <?php }elseif ($file['status']==0){?>
                                            <button class="btn btn-sm btn-success" type="button" disabled>Available</button>
                                        <?php }?>
                                    </td>
                                    <td>
                                        <?php if($fileRequest['status']==0){?>
                                            <button class="btn btn-sm btn-warning" type="button" disabled>Pending</button>
                                        <?php }?>
                                    </td>
                                    <td>
                                        <a href="#rStatus<?=$file['id']?>" class="btn btn-sm btn-default" type="button" data-toggle="modal">Manage</a>
                                    </td>
                                </tr>
                                <div class="modal fade" id="rStatus<?=$file['id']?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form method="post">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                    <h4> File Status </h4>
                                                </div>
                                                <div class="modal-body modal-body-np">
                                                    <?php if($file['status'] == 1){ $label='( '. $stf['firstname'].' has the file )';$rmS='checked';$ds='disabled';}
                                                    else{ $label='';$rmS='';$ds='';}?>
                                                    <div class="row">
                                                        <div class="col-md-9"><strong> Approve Request <?=$label?></strong></div>
                                                        <label class="switch">
                                                            <input type="checkbox" name="status" class="skip" value="<?=$file['status']?>" <?=$rmS?> <?=$ds?>/>
                                                            <span></span>
                                                        </label>
                                                        <div class="dr"><span></span></div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <input type="hidden" name="id" value="<?=$file['id']?>">
                                                    <input type="hidden" name="request_id" value="<?=$fileRequest['id']?>">
                                                    <input type="submit" name="edit_file_status" class="btn btn-warning"  aria-hidden="true" value="Save updates">
                                                    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            <?php }?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="dr"><span></span></div>

            <div class="row">

            </div>

            <div class="dr"><span></span></div>
        </div>

    </div>
</div>
<script>
    <?php if($user->data()->pswd == 0){?>
    $(window).on('load',function(){
        $("#change_password_n").modal({
            backdrop: 'static',
            keyboard: false
        },'show');
    });
    <?php }?>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script>
</body>

</html>
