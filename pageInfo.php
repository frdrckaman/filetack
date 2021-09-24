<ul class="buttons">
    <li>
        <a href="#" class="link_bcPopupList"><span class="glyphicon glyphicon-user"></span><span class="text">Users list</span></a>

        <div id="bcPopupList" class="popup">
            <div class="head clearfix">
                <div class="arrow"></div>
                <span class="isw-users"></span>
                <span class="name">List users</span>
            </div>
            <div class="body-fluid users">
                <?php if($user->data()->position == 1){
                foreach ($override->getData('user') as $staff){?>
                    <div class="item clearfix">
                        <div class="image"><a href="#"><img src="img/users/no-image.jpg" width="32"/></a></div>
                        <div class="info">
                            <a href="#" class="name"><?=$staff['firstname'].' '.$staff['lastname']?></a>
                            <span><?php if($staff['position'] == 1){echo'Admin';}elseif ($staff['position'] == 2){echo'Sales Personnel';}?></span>
                        </div>
                    </div>
                <?php }}?>

            </div>
            <div class="footer">
                <a href="add.php?id=1" class="btn btn-default">Add new</a>
                <button class="btn btn-danger link_bcPopupList" type="button">Close</button>
            </div>
        </div>

    </li>
    <li>
        <a href="#" class="link_bcPopupSearch"><span class="glyphicon glyphicon-search"></span><span class="text">Search</span></a>

        <div id="bcPopupSearch" class="popup">
            <div class="head clearfix">
                <div class="arrow"></div>
                <span class="isw-zoom"></span>
                <span class="name">Search</span>
            </div>
            <div class="body search">
                <input type="text" placeholder="Some text for search..." name="search"/>
            </div>
            <div class="footer">
                <button class="btn btn-default" type="button">Search</button>
                <button class="btn btn-danger link_bcPopupSearch" type="button">Close</button>
            </div>
        </div>
    </li>
</ul>
