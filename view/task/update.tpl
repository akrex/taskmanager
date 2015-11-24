<h2>Редактирование задачи</h2>
<?php Helper::BeginForm($this->data, "update","task", array("role" => "form")); ?>
    <?php Helper::HiddenFor("task_id", null); ?>
    <?php Helper::InputFor("name", array("class"=>"form-control", "placeholder"=>"Введите название задачи")); ?>
    <?php Helper::InputFor("description", array("class"=>"form-control", "placeholder"=>"Введите описание")); ?>
    <?php Helper::DateTimeLinkedFor("start", "end", null, null); ?>
    <?php Helper::CheckboxFor("email_notify", null); ?>
    <?php Helper::CheckboxFor("phone_notify", null); ?>
    <?php Helper::Submit("Сохранить", "btn-success"); ?>
<?php Helper::EndForm(); ?>