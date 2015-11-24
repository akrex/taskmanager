<table class="table table-hover">
<?php foreach($task as $item) { ?>
    <tr>
        <td>
            <?php if($item["phone_notify"]==1){ ?>
            <span class="glyphicon glyphicon-phone">
            <?php } if($item["email_notify"]==1) { ?>
            </span><span class="glyphicon glyphicon-envelope"></span>
            <?php } ?>
        </td>
        <td class="col-sm-6"><h4><?php echo $item["name"]; ?></h4> <?php echo $item["description"] ?></td>
        <td><b><?php echo $item["start"]."<br \> ".$item["end"];?></b></td>
        <td><div class="pull-right">
                <a class="btn btn-info" href="<?php echo Helper::Action(update, task, array("task" => $item["task_id"])) ?>" role="button">Редактировать</a>
                <a class="btn btn-danger" href="<?php echo Helper::Action(delete, task, array("task" => $item["task_id"])) ?>" onclick="return confirm('Вы действительно хотите удалить эту задачу?')" role="button">Удалить</a>
                </div>      
</td>
    </tr>
<?php } ?>
</table>