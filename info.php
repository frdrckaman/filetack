<?php
require_once'php/core/init.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();

$successMessage=null;$pageError=null;$errorMessage=null;
if($user->isLoggedIn()) {
    if(Input::exists('post')){
        $validate = new validate();
        if(Input::get('edit_position')){
            $validate = $validate->check($_POST, array(
                'name' => array(
                    'required' => true,
                ),
            ));
            if ($validate->passed()) {
                try {
                    $user->updateRecord('position', array(
                        'name' => Input::get('name'),
                    ),Input::get('id'));
                    $successMessage = 'Position Successful Updated';

                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        }
        elseif (Input::get('edit_staff')){
            $validate = new validate();
            $validate = $validate->check($_POST, array(
                'firstname' => array(
                    'required' => true,
                ),
                'lastname' => array(
                    'required' => true,
                ),
                'position' => array(
                    'required' => true,
                ),
                'phone_number' => array(
                    'required' => true,
                ),
                'email_address' => array(
                ),
            ));
            if ($validate->passed()) {
                $salt = $random->get_rand_alphanumeric(32);
                $password = '12345678';
                switch (Input::get('position')) {
                    case 1:
                        $accessLevel = 1;
                        break;
                    case 2:
                        $accessLevel = 2;
                        break;
                    case 3:
                        $accessLevel = 3;
                        break;
                }
                try {
                    $user->updateRecord('user', array(
                        'firstname' => Input::get('firstname'),
                        'lastname' => Input::get('lastname'),
                        'position' => Input::get('position'),
                        'phone_number' => Input::get('phone_number'),
                        'email_address' => Input::get('email_address'),
                        'accessLevel' => $accessLevel,
                        'user_id'=>$user->data()->id,
                    ),Input::get('id'));

                    $successMessage = 'Account Updated Successful';

                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                $pageError = $validate->errors();
            }
        }
        elseif (Input::get('reset_pass')){
            $salt = $random->get_rand_alphanumeric(32);
            $password = '12345678';
            $user->updateRecord('user', array(
                'password' => Hash::make($password,$salt),
                'salt' => $salt,
            ),Input::get('id'));
            $successMessage = 'Password Reset Successful';
        }
        elseif (Input::get('delete_staff')){
            $user->updateRecord('user', array(
                'status' => 0,
            ),Input::get('id'));
            $successMessage = 'User Deleted Successful';
        }
    }
}else{
    Redirect::to('index.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title> Info - FileTrack </title>
    <?php include "head.php";?>
</head>
<body>
<div class="wrapper">

    <?php include 'topbar.php'?>
    <?php include 'menu.php'?>
    <div class="content">


        <div class="breadLine">

            <ul class="breadcrumb">
                <li><a href="#">Info</a> <span class="divider">></span></li>
            </ul>
            <?php include 'pageInfo.php'?>
        </div>

        <div class="workplace">
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

            <div class="row">
                <?php if($_GET['id'] == 1 && $user->data()->accessLevel == 1){?>
                    <div class="col-md-12">
                        <div class="head clearfix">
                            <div class="isw-grid"></div>
                            <h1>List of Staff</h1>
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
                                    <th><input type="checkbox" name="checkall"/></th>
                                    <th width="25%">Name</th>
                                    <th width="25%">Username</th>
                                    <th width="25%">Position</th>
                                    <th width="25%">Branch</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($override->get('user','status', 1) as $staff){
                                    $position=$override->get('position','id', $staff['position'])[0]?>
                                    <tr>
                                        <td><input type="checkbox" name="checkbox"/></td>
                                        <td> <?=$staff['firstname'].' '.$staff['lastname']?></td>
                                        <td><?=$staff['username']?></td>
                                        <td><?=$position['name']?></td>
                                        <td>
                                            <a href="#user<?=$staff['id']?>" role="button" class="btn btn-info" data-toggle="modal">Edit</a>
                                            <a href="#reset<?=$staff['id']?>" role="button" class="btn btn-warning" data-toggle="modal">Reset</a>
                                            <a href="#delete<?=$staff['id']?>" role="button" class="btn btn-danger" data-toggle="modal">Delete</a>
                                        </td>

                                    </tr>
                                    <div class="modal fade" id="user<?=$staff['id']?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <form method="post">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                        <h4>Edit User Info</h4>
                                                    </div>
                                                    <div class="modal-body modal-body-np">
                                                        <div class="row">
                                                            <div class="block-fluid">
                                                                <div class="row-form clearfix">
                                                                    <div class="col-md-3">First name:</div>
                                                                    <div class="col-md-9"><input type="text" name="firstname" value="<?=$staff['firstname']?>" required/></div>
                                                                </div>
                                                                <div class="row-form clearfix">
                                                                    <div class="col-md-3">Last name:</div>
                                                                    <div class="col-md-9"><input type="text" name="lastname" value="<?=$staff['lastname']?>" required/></div>
                                                                </div>
                                                                <div class="row-form clearfix">
                                                                    <div class="col-md-3">Position</div>
                                                                    <div class="col-md-9">
                                                                        <select name="position" style="width: 100%;" required>
                                                                            <option value="<?=$position['id']?>"><?=$position['name']?></option>
                                                                            <?php foreach ($override->getData('position') as $position){?>
                                                                                <option value="<?=$position['id']?>"><?=$position['name']?></option>
                                                                            <?php }?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="row-form clearfix">
                                                                    <div class="col-md-3">Phone Number:</div>
                                                                    <div class="col-md-9"><input value="<?=$staff['phone_number']?>" class="" type="text" name="phone_number" id="phone" required />  <span>Example: 0700 000 111</span></div>
                                                                </div>

                                                                <div class="row-form clearfix">
                                                                    <div class="col-md-3">E-mail Address:</div>
                                                                    <div class="col-md-9"><input value="<?=$staff['email_address']?>" class="validate[required,custom[email]]" type="text" name="email_address" id="email" />  <span>Example: someone@nowhere.com</span></div>
                                                                </div>
                                                            </div>
                                                            <div class="dr"><span></span></div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <input type="hidden" name="id" value="<?=$staff['id']?>">
                                                        <input type="submit" name="edit_staff" value="Save updates" class="btn btn-warning">
                                                        <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="modal fade" id="reset<?=$staff['id']?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <form method="post">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                        <h4>Reset Password</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to reset password to default (12345678)</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <input type="hidden" name="id" value="<?=$staff['id']?>">
                                                        <input type="submit" name="reset_pass" value="Reset" class="btn btn-warning">
                                                        <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="modal fade" id="delete<?=$staff['id']?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <form method="post">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                        <h4>Delete User</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <strong style="font-weight: bold;color: red"><p>Are you sure you want to delete this user</p></strong>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <input type="hidden" name="id" value="<?=$staff['id']?>">
                                                        <input type="submit" name="delete_staff" value="Delete" class="btn btn-danger">
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
                <?php } elseif ($_GET['id'] == 2 && $user->data()->accessLevel == 1){?>
                    <div class="col-md-6">
                        <div class="head clearfix">
                            <div class="isw-grid"></div>
                            <h1>List of Positions</h1>
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
                                    <th width="25%">Name</th>
                                    <th width="5%">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($override->getData('position') as $position){?>
                                    <tr>
                                        <td> <?=$position['name']?></td>
                                        <td><a href="#position<?=$position['id']?>" role="button" class="btn btn-info" data-toggle="modal">Edit</a></td>
                                        <!-- EOF Bootrstrap modal form -->
                                    </tr>
                                    <div class="modal fade" id="position<?=$position['id']?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="post">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                        <h4>Edit Position Info</h4>
                                                    </div>
                                                    <div class="modal-body modal-body-np">
                                                        <div class="row">
                                                            <div class="block-fluid">
                                                                <div class="row-form clearfix">
                                                                    <div class="col-md-3">Name:</div>
                                                                    <div class="col-md-9"><input type="text" name="name" value="<?=$position['name']?>" required/></div>
                                                                </div>
                                                            </div>
                                                            <div class="dr"><span></span></div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <input type="hidden" name="id" value="<?=$position['id']?>">
                                                        <input type="submit" name="edit_position" class="btn btn-warning" value="Save updates">
                                                        <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php }?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="head clearfix">
                            <div class="isw-grid"></div>
                            <h1>Document Categories</h1>
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
                                    <th width="25%">Name</th>
                                    <th width="5%">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($override->getData('doc_cat') as $doc){?>
                                    <tr>
                                        <td><?=$doc['name']?></td>
                                        <td><a href="#region<?=$doc['id']?>" role="button" class="btn btn-info" data-toggle="modal">Edit</a></td>
                                        <!-- EOF Bootrstrap modal form -->
                                    </tr>
                                    <div class="modal fade" id="region<?=$doc['id']?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <form method="post">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                        <h4>Edit Info</h4>
                                                    </div>
                                                    <div class="modal-body modal-body-np">
                                                        <div class="row">
                                                            <div class="block-fluid">
                                                                <div class="row-form clearfix">
                                                                    <div class="col-md-3">Name:</div>
                                                                    <div class="col-md-9"><input type="text" name="name" value="<?=$doc['name']?>" required/></div>
                                                                </div>
                                                            </div>
                                                            <div class="dr"><span></span></div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <input type="submit" name="edit_doc_cat" class="btn btn-warning" data-dismiss="modal" aria-hidden="true" value="Save updates">
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
                <?php } elseif ($_GET['id'] == 3){?>
                    <div class="col-md-12">
                        <div class="head clearfix">
                            <div class="isw-grid"></div>
                            <h1>List of Files</h1>
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
                                    <th><input type="checkbox" name="checkall"/></th>
                                    <th width="20%">Name</th>
                                    <th width="20%">Study</th>
                                    <th width="25%">Details</th>
                                    <th width="10%">Status</th>
                                    <th width="25%">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($override->getData('study_files') as $files){
                                    $study=$override->get('study','id',$files['study_id'])[0]?>
                                    <tr>
                                        <td><input type="checkbox" name="checkbox"/></td>
                                        <td> <?=$files['name']?></td>
                                        <td><?=$study['name']?></td>
                                        <td><?=$files['details']?></td>
                                        <td>
                                            <?php if($files['status'] == 1){?>
                                                <a href="#" role="button" class="btn btn-warning" data-toggle="modal">Assigned</a>
                                            <?php }else{?>
                                                <a href="#" role="button" class="btn btn-success" data-toggle="modal">Free</a>
                                            <?php }?>
                                        </td>
                                        <td>
                                            <a href="#user<?=$files['id']?>" role="button" class="btn btn-info" data-toggle="modal">Edit</a>
                                            <a href="#delete<?=$files['id']?>" role="button" class="btn btn-danger" data-toggle="modal">Delete</a>
                                        </td>

                                    </tr>
                                    <div class="modal fade" id="user<?=$files['id']?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <form method="post">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                        <h4>Edit File Info</h4>
                                                    </div>
                                                    <div class="modal-body modal-body-np">
                                                        <div class="row">
                                                            <div class="block-fluid">
                                                                <div class="row-form clearfix">
                                                                    <div class="col-md-3">Name:</div>
                                                                    <div class="col-md-9">
                                                                        <input value="<?=$files['name']?>" class="validate[required]" type="text" name="name" id="name"/>
                                                                    </div>
                                                                </div>
                                                                <div class="row-form clearfix">
                                                                    <div class="col-md-3">Study</div>
                                                                    <div class="col-md-9">
                                                                        <select name="study" style="width: 100%;" required>
                                                                            <option value="<?=$files['study_id']?>"><?=$study['name']?></option>
                                                                            <?php foreach ($override->getData('study') as $study){?>
                                                                                <option value="<?=$study['id']?>"><?=$study['name']?></option>
                                                                            <?php }?>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                <div class="row-form clearfix">
                                                                    <div class="col-md-3">Description:</div>
                                                                    <div class="col-md-9"><textarea name="details" rows="4" ><?=$files['details']?></textarea></div>
                                                                </div>
                                                            </div>
                                                            <div class="dr"><span></span></div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <input type="hidden" name="id" value="<?=$files['id']?>">
                                                        <input type="submit" name="edit_file" value="Save updates" class="btn btn-warning">
                                                        <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="modal fade" id="delete<?=$files['id']?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <form method="post">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                        <h4>Delete User</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <strong style="font-weight: bold;color: red"><p>Are you sure you want to delete this file</p></strong>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <input type="hidden" name="id" value="<?=$files['id']?>">
                                                        <input type="submit" name="delete_file" value="Delete" class="btn btn-danger">
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
                <?php } elseif ($_GET['id'] == 4){?>
                    <div class="col-md-12">
                        <div class="head clearfix">
                            <div class="isw-grid"></div>
                            <h1>List of Studies</h1>
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
                                    <th><input type="checkbox" name="checkall"/></th>
                                    <th width="10%">Name</th>
                                    <th width="10%">PI</th>
                                    <th width="10%">Coordinator</th>
                                    <th width="10%">Start Data</th>
                                    <th width="10%">End Data</th>
                                    <th width="25%">Details</th>
                                    <th width="10%">status</th>
                                    <th width="15%">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($override->getData('study') as $study){
                                    $pi=$override->get('user','id',$study['pi_id'])[0];
                                    $co=$override->get('user','id',$study['co_id'])[0]?>
                                    <tr>
                                        <td><input type="checkbox" name="checkbox"/></td>
                                        <td> <?=$study['name']?></td>
                                        <td><?=$pi['firstname'].' '.$pi['lastname']?></td>
                                        <td><?=$co['firstname'].' '.$co['lastname']?></td>
                                        <td> <?=$study['start_date']?></td>
                                        <td> <?=$study['end_date']?></td>
                                        <td> <?=$study['details']?></td>
                                        <td>
                                            <?php if($study['status'] == 1){?>
                                                <a href="#" role="button" class="btn btn-success" data-toggle="modal">Active</a>
                                            <?php }else{?>
                                                <a href="#" role="button" class="btn btn-danger" data-toggle="modal">End</a>
                                            <?php }?>
                                        </td>
                                        <td>
                                            <a href="#study<?=$study['id']?>" role="button" class="btn btn-info" data-toggle="modal">Edit</a>
                                            <a href="#delete<?=$study['id']?>" role="button" class="btn btn-danger" data-toggle="modal">Delete</a>
                                        </td>

                                    </tr>
                                    <div class="modal fade" id="study<?=$study['id']?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <form method="post">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                        <h4>Edit File Info</h4>
                                                    </div>
                                                    <div class="modal-body modal-body-np">
                                                        <div class="row">
                                                            <div class="block-fluid">

                                                            </div>
                                                            <div class="dr"><span></span></div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <input type="hidden" name="id" value="<?=$study['id']?>">
                                                        <input type="submit" name="edit_file" value="Save updates" class="btn btn-warning">
                                                        <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="modal fade" id="delete<?=$study['id']?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <form method="post">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                        <h4>Delete User</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <strong style="font-weight: bold;color: red"><p>Are you sure you want to delete this Study</p></strong>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <input type="hidden" name="id" value="<?=$study['id']?>">
                                                        <input type="submit" name="delete_file" value="Delete" class="btn btn-danger">
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
                <?php }?>
            </div>

            <div class="dr"><span></span></div>
        </div>
    </div>
</div>
</body>
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
    $(document).ready(function(){
        $('#wait_ds').hide();
        $('#region').change(function(){
            var getUid = $(this).val();
            $('#wait_ds').show();
            $.ajax({
                url:"process.php?cnt=region",
                method:"GET",
                data:{getUid:getUid},
                success:function(data){
                    $('#ds_data').html(data);
                    $('#wait_ds').hide();
                }
            });

        });
        $('#wait_wd').hide();
        $('#ds_data').change(function(){
            $('#wait_wd').hide();
            var getUid = $(this).val();
            $.ajax({
                url:"process.php?cnt=district",
                method:"GET",
                data:{getUid:getUid},
                success:function(data){
                    $('#wd_data').html(data);
                    $('#wait_wd').hide();
                }
            });

        });
        $('#download').change(function(){
            var getUid = $(this).val();
            $.ajax({
                url:"process.php?cnt=download",
                method:"GET",
                data:{getUid:getUid},
                success:function(data){

                }
            });

        });
    });
</script>

</html>
