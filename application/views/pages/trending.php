<?php
foreach($items as $item){
    ?>
        <div>
                <h3 style="padding-bottom: 0px;"><?php echo $item->title ?></h3>
                <a style="color: blue;" href="<?php echo $item->link ?>" target="_blank"><?php echo $item->link ?></a>
        </div>
    <?php
}